<?php

namespace Tests\Feature\Controller;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerUploadTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create();

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ];
    }

    public function testInvaidationFileCreate()
    {

        $file = UploadedFile::fake()->create("video.errorType");
        $data = ['video_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['video_file'], 'mimetypes', ['values' => 'video/mp4']);

        $file = UploadedFile::fake()->create('video.mp4')->size(2000);
        $data = ['video_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['video_file'], 'max.file', ['max' => 1024]);

    }

    public function testInvaidationFileUpdate()
    {

        $file = UploadedFile::fake()->create("video.errorType");
        $data = ['video_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['video_file'], 'mimetypes', ['values' => 'video/mp4']);


        $file = UploadedFile::fake()->create('video.mp4')->size(2000);
        $data = ['video_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['video_file'], 'max.file', ['max' => 1024]);

    }

    public function testStoreWithFiles()
    {

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        \Storage::fake();
        $video = UploadedFile::fake()->image('video.mp4');
        $imageTumb = UploadedFile::fake()->image("image.jpg");

        $testData = $this->sendData + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
            'video_file' => $video,
            'tumb_file' => $imageTumb
        ];

        $response = $this->assertStore($testData, $this->sendData);
        $response->assertJsonStructure(['video_file', 'tumb_file']);
        $id = $response->json('id');

        // dump($response->json());

        \Storage::assertExists("$id/{$video->hashName()}");
        \Storage::assertExists("$id/{$imageTumb->hashName()}");

    }

    public function testUpdateWithFiles()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        \Storage::fake();
        $file = UploadedFile::fake()->image('video.mp4');

        $testData = $this->sendData + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
            'video_file' => $file
        ];

        $response = $this->assertUpdate($testData, $this->sendData);
        $response->assertJsonStructure(['video_file']);
        $id = $response->json('id');
        \Storage::assertExists("$id/{$file->hashName()}");

    }

    // public function testRollbackFilesInStores()
    // {
    //     \Storage::fake();
    //     \Event::listen(TransactionCommitted::class, function () {
    //         throw new \Exception;
    //     });
    //     $hasError = false;

    //     try {

    //         $file = UploadedFile::fake()->image('video.mp4');
    //         $category = factory(Category::class)->create();
    //         $genre = factory(Genre::class)->create();

    //         Video::create($this->sendData + [
    //             'categories_id' => [$category->id],
    //             'genres_id' => [$genre->id],
    //             'video_file' => $file
    //         ]);

    //     } catch (Exception $th) {
    //         $this->assertCount(0, Storage::allFiles());
    //         $hasError = true;
    //     }

    //     $this->assertTrue($hasError);
    // }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', [$this->video->id]);
    }

    protected function model()
    {
        return Video::class;
    }
}
