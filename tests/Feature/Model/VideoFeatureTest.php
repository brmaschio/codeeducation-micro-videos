<?php

namespace Tests\Feature;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VideoFeatureTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {

        $video = factory(Video::class)->create();

        $videos = Video::all();

        $this->assertCount(1, $videos);
        
    }

    public function testCreate()
    {

        $video = Video::create([
            'title' => 'test',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ]);
        $video->refresh();

        $uuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $video->id);
        $this->assertEquals(1, $uuid);

        $this->assertEquals('test', $video->title);


    }

    public function testUpdate() {

        $video = Video::create([
            'title' => 'test',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ]);

        $data = ['title' => 'test_update'];
        $video->update($data);

        $this->assertEquals('test_update', $video->title);

    }

    public function testDelete() {

        $video = Video::create([
            'title' => 'test',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ]);

        $video->delete();
        $this->assertNull(Video::find($video->id));

    }

}
