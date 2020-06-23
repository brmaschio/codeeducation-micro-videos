<?php

namespace Tests\Feature\Controller;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
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

        // Video
        $file = UploadedFile::fake()->create("video.errorType");
        $data = ['video_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['video_file'], 'mimetypes', ['values' => 'video/mp4']);

        $file = UploadedFile::fake()->create('video.mp4')->size(Video::VIDEO_FILE_MAX_SIZE + 1);
        $data = ['video_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['video_file'], 'max.file', ['max' => Video::VIDEO_FILE_MAX_SIZE]);

        // Treiller
        $file = UploadedFile::fake()->create("video.errorType");
        $data = ['trailer_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['trailer_file'], 'mimetypes', ['values' => 'video/mp4']);

        $file = UploadedFile::fake()->create('video.mp4')->size(Video::TRAILER_FILE_MAX_SIZE + 1);
        $data = ['trailer_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['trailer_file'], 'max.file', ['max' => Video::TRAILER_FILE_MAX_SIZE]);

        // Thumb
        $file = UploadedFile::fake()->create("image.errorType");
        $data = ['tumb_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['tumb_file'], 'image', ['values' => 'jpg']);

        $file = UploadedFile::fake()->create('image.jpg')->size(Video::THUMB_FILE_MAX_SIZE + 1);
        $data = ['tumb_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['tumb_file'], 'max.file', ['max' => Video::THUMB_FILE_MAX_SIZE]);

        // Banner
        $file = UploadedFile::fake()->create("image.errorType");
        $data = ['banner_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['banner_file'], 'image', ['values' => 'jpg']);

        $file = UploadedFile::fake()->create('image.jpg')->size(Video::BANNER_FILE_MAX_SIZE + 1);
        $data = ['banner_file' => $file];
        $response = $this->json('POST', $this->routeStore(), $data);
        $this->assertInvalidationFields($response, ['banner_file'], 'max.file', ['max' => Video::BANNER_FILE_MAX_SIZE]);
    }

    public function testInvaidationFileUpdate()
    {
        // Video
        $file = UploadedFile::fake()->create("video.errorType");
        $data = ['video_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['video_file'], 'mimetypes', ['values' => 'video/mp4']);

        $file = UploadedFile::fake()->create('video.mp4')->size(Video::VIDEO_FILE_MAX_SIZE + 1);
        $data = ['video_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['video_file'], 'max.file', ['max' => Video::VIDEO_FILE_MAX_SIZE]);

        // Treiller
        $file = UploadedFile::fake()->create("video.errorType");
        $data = ['trailer_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['trailer_file'], 'mimetypes', ['values' => 'video/mp4']);

        $file = UploadedFile::fake()->create('video.mp4')->size(Video::TRAILER_FILE_MAX_SIZE + 1);
        $data = ['trailer_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['trailer_file'], 'max.file', ['max' => Video::TRAILER_FILE_MAX_SIZE]);

        // Thumb
        $file = UploadedFile::fake()->create("image.errorType");
        $data = ['tumb_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['tumb_file'], 'image', ['values' => 'jpg']);

        $file = UploadedFile::fake()->create('image.jpg')->size(Video::THUMB_FILE_MAX_SIZE + 1);
        $data = ['tumb_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['tumb_file'], 'max.file', ['max' => Video::THUMB_FILE_MAX_SIZE]);

        // Banner
        $file = UploadedFile::fake()->create("image.errorType");
        $data = ['banner_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['banner_file'], 'image', ['values' => 'jpg']);

        $file = UploadedFile::fake()->create('image.jpg')->size(Video::BANNER_FILE_MAX_SIZE + 1);
        $data = ['banner_file' => $file];
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $this->assertInvalidationFields($response, ['banner_file'], 'max.file', ['max' => Video::BANNER_FILE_MAX_SIZE]);
    }

    public function testStoreWithFiles()
    {

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        \Storage::fake();
        $video = UploadedFile::fake()->image('video.mp4');
        $imageTumb = UploadedFile::fake()->image("image.jpg");
        $videoTreiller = UploadedFile::fake()->image("treiller.mp4");
        $imageBanner = UploadedFile::fake()->image("image.jpg");

        $testData = $this->sendData + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
            'video_file' => $video,
            'tumb_file' => $imageTumb,
            'trailer_file' => $videoTreiller,
            'banner_file' => $imageBanner
        ];

        $response = $this->assertStore($testData, $this->sendData);
        $response->assertJsonStructure(['data' =>['video_file', 'tumb_file', 'trailer_file', 'banner_file']]);

        $id = $response->json('data.id');

        \Storage::assertExists("$id/{$video->hashName()}");
        \Storage::assertExists("$id/{$imageTumb->hashName()}");
        \Storage::assertExists("$id/{$videoTreiller->hashName()}");
        \Storage::assertExists("$id/{$imageBanner->hashName()}");
    }

    public function testUpdateWithFiles()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        \Storage::fake();
        $file = UploadedFile::fake()->image('video.mp4');
        $imageTumb = UploadedFile::fake()->image("image.jpg");
        $videoTreiller = UploadedFile::fake()->image("treiller.mp4");
        $imageBanner = UploadedFile::fake()->image("image.jpg");

        $testData = $this->sendData + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
            'video_file' => $file,
            'tumb_file' => $imageTumb,
            'trailer_file' => $videoTreiller,
            'banner_file' => $imageBanner
        ];

        $response = $this->assertUpdate($testData, $this->sendData);
        $response->assertJsonStructure(['data' =>['video_file', 'tumb_file', 'trailer_file', 'banner_file']]);
        $id = $response->json('data.id');
        \Storage::assertExists("$id/{$file->hashName()}");
        \Storage::assertExists("$id/{$imageTumb->hashName()}");
        \Storage::assertExists("$id/{$videoTreiller->hashName()}");
        \Storage::assertExists("$id/{$imageBanner->hashName()}");

        $newFile = UploadedFile::fake()->image('filme.mp4');
        $newImageTumb = UploadedFile::fake()->image("image.jpg");
        $newVideoTreiller = UploadedFile::fake()->image("treiller.mp4");
        $newImageBanner = UploadedFile::fake()->image("image.jpg");

        $testData = $this->sendData + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
            'video_file' => $newFile,
            'tumb_file' => $newImageTumb,
            'trailer_file' => $newVideoTreiller,
            'banner_file' => $newImageBanner
        ];
        $response = $this->assertUpdate($testData, $this->sendData);
        $id = $response->json('data.id');

        \Storage::assertMissing("$id/{$file->hashName()}");
        \Storage::assertMissing("$id/{$imageTumb->hashName()}");
        \Storage::assertMissing("$id/{$videoTreiller->hashName()}");
        \Storage::assertMissing("$id/{$imageBanner->hashName()}");

        \Storage::assertExists("$id/{$newFile->hashName()}");
        \Storage::assertExists("$id/{$newImageTumb->hashName()}");
        \Storage::assertExists("$id/{$newVideoTreiller->hashName()}");
        \Storage::assertExists("$id/{$newImageBanner->hashName()}");
    }

    public function testRollbackFilesInStores()
    {
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function () {
            throw new \Exception;
        });
        $hasError = false;

        try {

            $file = UploadedFile::fake()->image('video.mp4');
            $category = factory(Category::class)->create();
            $genre = factory(Genre::class)->create();

            Video::create($this->sendData + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id],
                'video_file' => $file
            ]);
        } catch (\Exception $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }

        $this->assertTrue($hasError);
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
