<?php

namespace Tests\Feature\Controller;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
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

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));
        $response->assertStatus(200)->assertJson([$this->video->toArray()]);
    }

    public function testInvalidationRequired()
    {
        $data = [
            'title' => '', 'description' => '', 'year_launched' => '', 'rating' => '',
            'duration' => '', 'categories_id' => '', 'genres_id' => ''
        ];
        $this->assertInvalidationStoreActions($data, 'required');
        $this->assertInvalidationUpdateActions($data, 'required');
    }

    public function testInvalidationMax()
    {
        $data = ['title' => str_repeat('a', 256)];
        $this->assertInvalidationStoreActions($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationUpdateActions($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationInteger()
    {
        $data = ['duration' => 's'];
        $this->assertInvalidationStoreActions($data, 'integer');
        $this->assertInvalidationUpdateActions($data, 'integer');
    }

    public function testInvalidationYearLaunchField()
    {
        $data = ['year_launched' => 's'];
        $this->assertInvalidationStoreActions($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationUpdateActions($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationOpenedField()
    {
        $data = ['opened' => 's'];
        $this->assertInvalidationStoreActions($data, 'boolean');
        $this->assertInvalidationUpdateActions($data, 'boolean');
    }

    public function testInvalidationRatingField()
    {
        $data = ['rating' => 0];
        $this->assertInvalidationStoreActions($data, 'in');
        $this->assertInvalidationUpdateActions($data, 'in');
    }

    public function testInvalidationcategories_idField()
    {
        $data = ['categories_id' => 'a'];
        $this->assertInvalidationStoreActions($data, 'array');
        $this->assertInvalidationUpdateActions($data, 'array');

        $data = ['categories_id' => [465465]];
        $this->assertInvalidationStoreActions($data, 'exists');
        $this->assertInvalidationUpdateActions($data, 'exists');
    }

    public function testInvalidationgenres_idField()
    {
        $data = ['genres_id' => 'a'];
        $this->assertInvalidationStoreActions($data, 'array');
        $this->assertInvalidationUpdateActions($data, 'array');

        $data = ['genres_id' => [465465]];
        $this->assertInvalidationStoreActions($data, 'exists');
        $this->assertInvalidationUpdateActions($data, 'exists');
    }

    // public function testRollbackStore()
    // {
    //     $controller = \Mockery::mock(VideoController::class)->makePartial()->shouldAllowMockingProtectedMethods();

    //     $controller->shouldReceive('validate')->withAnyArgs()->andReturn($this->sendData);

    //     $controller->shouldReceive('rulesStore')->withAnyArgs()->andReturn([]);

    //     $request = \Mockery::mock(Request::class);

    //     $controller->shouldReceive('handleRelarions')->once()->andThrow(new \Exception());

    //     try {
    //         $controller->store($request);
    //     } catch (\Exception $e) {
    //         $this->assertCount(1, Video::all());
    //     }
    // }

    public function testStore()
    {

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        $testData = $this->sendData + [
            'opened' => false, 
            'categories_id' => [$category->id], 
            'genres_id' => [$genre->id]
        ];

        $response = $this->assertStore($testData, $this->sendData);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $this->assertStore($testData + ['rating' => Video::RATING_LIST[2]], $this->sendData + ['rating' => Video::RATING_LIST[2]]);
        
        $testData = $this->sendData + [
            'opened' => true, 
            'categories_id' => [$category->id], 
            'genres_id' => [$genre->id]
        ];

        $this->assertStore($testData, $this->sendData + ['opened' => true]);

        $this->assertDatabaseHas('category_video', [
            'category_id' => $category->id,
            'video_id' => $response->json('id'),
        ]);

        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genre->id,
            'video_id' => $response->json('id')
        ]);

    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        $testData = $this->sendData + [
            'opened' => false, 
            'categories_id' => [$category->id], 
            'genres_id' => [$genre->id]
        ];

        $response = $this->assertUpdate($testData, $this->sendData);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $this->assertDatabaseHas('category_video', [
            'category_id' => $category->id,
            'video_id' => $response->json('id'),
        ]);

        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genre->id,
            'video_id' => $response->json('id')
        ]);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));
        $response->assertStatus(200)->assertJson($this->video->toArray());
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));

        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
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
