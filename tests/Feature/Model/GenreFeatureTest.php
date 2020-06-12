<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreFeatureTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {

        $genre = Genre::create(['name' => 'test']);

        $genres = Genre::all();

        $this->assertCount(1, $genres);

        $genreKey = array_keys($genres->first()->getAttributes());
        
        $this->assertEqualsCanonicalizing([
            'id', 'name', 'created_at', 'updated_at', 'deleted_at', 'is_active'
        ], $genreKey);
        
    }

    public function testCreate()
    {

        $genre = Genre::create(['name' => 'test']);
        $genre->refresh();

        $uuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $genre->id);
        $this->assertEquals(1, $uuid);

        $this->assertEquals('test', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create(['name' => 'test', 'is_active' => false]);
        $this->assertFalse($genre->is_active);

    }

    public function testUpdate() {

        $genre = Genre::create(['name' => 'test']);

        $data = ['name' => 'test_update'];
        $genre->update($data);

        $this->assertEquals('test_update', $genre->name);

    }

    public function testDelete() {

        $genre = Genre::create(['name' => 'test']);

        $genre->delete();
        $this->assertNull(Genre::find($genre->id));

    }

}
