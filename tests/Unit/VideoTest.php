<?php

namespace Tests\Unit;

use App\Models\Video;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class VideoTest extends TestCase
{

    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }

    public function testFillable()
    {
        $fillable = ['title', 'description', 'year_launched', 'opened', 'rating', 'duration'];

        $this->assertEquals($fillable, $this->video->getFillable());
    }

    public function testUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class];

        $videoTraits = array_keys(class_uses(video::class));

        $this->assertEquals($traits, $videoTraits);
    }

    public function testCasts()
    {
        $casts = [
            'id' => 'string',
            'opened' => 'boolean',
            'year_launched' => 'integer',
            'duration' => 'integer'
        ];

        $this->assertEquals($casts, $this->video->getCasts());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->video->incrementing);
    }

    public function testDatesAtributes()
    {
        $dates = ['deleted_at', 'updated_at', 'created_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->video->getDates());
        }

        $this->assertCount(count($dates), $this->video->getDates());
    }
}
