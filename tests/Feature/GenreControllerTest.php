<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{

    use DatabaseMigrations;

    public function testIndex()
    {
        $genre = Genre::create(['name' => 'test']);
        $response = $this->get(route('genres.index'));

        $response->assertStatus(200)->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = Genre::create(['name' => 'test']);
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response->assertStatus(200)->assertJson($genre->toArray());
    }

    public function testInvalidationData()
    {
        $response = $this->json('POST', route('genres.store'), []);
        $this->assertInvalidationRequired($response);

        $data = ['name' => str_repeat('a', 256), 'is_active' => 'a'];
        $response = $this->json('POST', route('genres.store'), $data);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $genre = Genre::create(['name' => 'test']);
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), $data);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('genres.store'), ['name' => 'test']);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response->assertStatus(201)->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $data = ['name' => 'test', 'is_active' => false];
        $response = $this->json('POST', route('genres.store'), $data);

        $response->assertJsonFragment(['is_active' => false]);
    }

    public function testUpdate()
    {
        $genre = Genre::create(['name'=> 'Terror', 'is_active' => false]);
        $data = ['name' => 'test',  'is_active' => true];
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), $data);

        $id = $response->json('id');
        $Genre = Genre::find($id);

        $response->assertStatus(200)->assertJson($Genre->toArray())->assertJsonFragment(['is_active' => true]);
    }

    public function testDelete() {

        $genre = Genre::create(['name'=> 'Terror', 'is_active' => false]);
        $response = $this->json('DELETE',route('genres.destroy', ['genre' => $genre->id]));

        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));

    }

    protected function assertInvalidationRequired($response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([\Lang::get('validation.required', ['attribute' => 'name'])]);
    }

    protected function assertInvalidationMax($response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([\Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])]);
    }

    protected function assertInvalidationBoolean($response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([\Lang::get('validation.boolean', ['attribute' => 'is active'])]);
    }

}
