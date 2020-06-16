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
        $file = UploadedFile::fake()->create('video.mp4');

        $testData = $this->sendData + [
            'categories_id' => [$category->id], 
            'genres_id' => [$genre->id],
            'video_file' => $file
        ];

        $response = $this->assertStore($testData, $this->sendData);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $id = $response->json('id');
        \Storage::assertExists("$id/{$file->hashName()}");

    }

    public function testUpdateWithFiles()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');

        $testData = $this->sendData + [
            'categories_id' => [$category->id], 
            'genres_id' => [$genre->id],
            'video_file' => $file
        ];

        $response = $this->assertUpdate($testData, $this->sendData);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $id = $response->json('id');
        \Storage::assertExists("$id/{$file->hashName()}");
        
    }

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
