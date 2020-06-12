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
            'duration' => '', 'categories' => '', 'genres' => ''
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

    public function testInvalidationCategoriesField()
    {
        $data = ['categories' => 'a'];
        $this->assertInvalidationStoreActions($data, 'array');
        $this->assertInvalidationUpdateActions($data, 'array');

        $data = ['categories' => [465465]];
        $this->assertInvalidationStoreActions($data, 'exists');
        $this->assertInvalidationUpdateActions($data, 'exists');
    }

    public function testInvalidationGenresField()
    {
        $data = ['genres' => 'a'];
        $this->assertInvalidationStoreActions($data, 'array');
        $this->assertInvalidationUpdateActions($data, 'array');

        $data = ['genres' => [465465]];
        $this->assertInvalidationStoreActions($data, 'exists');
        $this->assertInvalidationUpdateActions($data, 'exists');
    }

    public function testStore()
    {

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();

        $testData = $this->sendData + [
            'opened' => false, 
            'categories' => [$category->id], 
            'genres' => [$genre->id]
        ];

        // dd($testData);

        $response = $this->assertStore($this->sendData, $testData);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        // $this->assertStore($this->sendData + ['opened' => true], $this->sendData + ['opened' => true]);
        // $this->assertStore($this->sendData + ['rating' => Video::RATING_LIST[2]], $this->sendData + ['rating' => Video::RATING_LIST[2]]);
    }

    // public function testUpdate()
    // {
    //     $category = factory(Category::class)->create();
    //     $genre = factory(Genre::class)->create();

    //     $testData = $this->sendData + [
    //         'opened' => false, 
    //         'categories' => [$category->id], 
    //         'genres' => [$genre->id]
    //     ];

    //     $response = $this->assertUpdate($this->sendData, $testData);
    //     $response->assertJsonStructure(['created_at', 'updated_at']);
    // }

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
