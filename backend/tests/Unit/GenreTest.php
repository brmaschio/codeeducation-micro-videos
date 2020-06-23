<?php

namespace Tests\Unit;

use App\Models\Genre;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use App\Traits\Uuid;

class GenreTest extends TestCase
{

    private $genre;

    protected function setUp() : void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function testFillable()
    {
        $fillable = ['name', 'is_active'];

        $this->assertEquals($fillable, $this->genre->getFillable());
    }

    public function testUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class];

        $genreTraits = array_keys(class_uses(Genre::class));

        $this->assertEquals($traits, $genreTraits);
    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];

        $this->assertEquals($casts, $this->genre->getCasts());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->genre->incrementing);
    }

    public function testDatesAtributes()
    {
        $dates = ['deleted_at', 'updated_at', 'created_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->genre->getDates());
        }

        $this->assertCount(count($dates), $this->genre->getDates());
    }

}
