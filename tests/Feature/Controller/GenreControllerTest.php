<?php

namespace Tests\Controller\Feature;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    /**
     * 
     *  Mantida estrutura antiga de teste para fins de ficar registrado outra maneira de crialos
     * 
     */

    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    public function testIndex()
    {
        // $genre = Genre::create(['name' => 'test']);
        // $response = $this->get(route('genres.index'));

        // $response->assertStatus(200)->assertJson([$genre->toArray()]);

        $response = $this->get(route('genres.index'));
        $response->assertStatus(200)->assertJson([$this->video->toArray()]);

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

        $data = ['name' => str_repeat('a', 256), 'is_active' => 'a', 'categories_id' => ''];
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

    public function testInvalidationcategories_idField()
    {
        $data = ['categories_id' => 'a'];
        $this->assertInvalidationStoreActions($data, 'array');
        $this->assertInvalidationUpdateActions($data, 'array');

        $data = ['categories_id' => [465465]];
        $this->assertInvalidationStoreActions($data, 'exists');
        $this->assertInvalidationUpdateActions($data, 'exists');
    }

    public function testStore()
    {

        $category = factory(Category::class)->create();

        $testData = ['name' => 'test'];

        $response = $this->assertStore(
            $testData + ['categories_id' => [$category->id]],
            $testData + ['is_active' => true, 'deleted_at' => null]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $response->json('id'),
            'category_id' => $category->id
        ]);
    }

    public function testUpdate()
    {

        $category = factory(Category::class)->create();

        $testData = ['name' => 'test'];

        $response = $this->assertUpdate(
            $testData + ['categories_id' => [$category->id]],
            $testData + ['is_active' => true, 'deleted_at' => null]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testDelete()
    {

        $genre = Genre::create(['name' => 'Terror', 'is_active' => false]);
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]));

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

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', [$this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }
}
