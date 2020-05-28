<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{

    use DatabaseMigrations;

    public function testIndex()
    {

        $category = factory(Category::class)->create();

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([$category->toArray()]);
    }

    public function testShow()
    {

        $category = factory(Category::class)->create();

        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(200)->assertJson($category->toArray());
    }

    public function testInvalidationData()
    {

        $response = $this->json('POST', route('categories.store'), []);
        $this->assertInvalidationRequired($response);

        $data = ['name' => str_repeat('a', 256), 'is_active' => 'a'];
        $response = $this->json('POST', route('categories.store'), $data);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), $data);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    public function testStore()
    {

        $response = $this->json('POST', route('categories.store'), ['name' => 'test']);

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(201)->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $data = ['name' => 'test', 'description' => 'desc', 'is_active' => false];
        $response = $this->json('POST', route('categories.store'), $data);

        $response->assertJsonFragment(['description' => 'desc', 'is_active' => false]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create(['is_active' => false]);
        $data = ['name' => 'test', 'description' => 'desc', 'is_active' => true];
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), $data);

        $id = $response->json('id');
        $category = Category::find($id);

        $response->assertStatus(200)->assertJson($category->toArray())
            ->assertJsonFragment(['description' => 'desc', 'is_active' => true]);

        $data = ['name' => 'test', 'description' => ''];
        $response = $this->json('PUT',route('categories.update', ['category' => $category->id]), $data);

        $response->assertJsonFragment(['description' => null]);

        $category->description = 'test';
        $category->save();

        $data = ['name' => 'test','description'=> null];
        $response = $this->json('PUT',route('categories.update', ['category' => $category->id]),$data);

        $response->assertJsonFragment(['description' => null]);

    }

    public function testDelete() {

        $category = factory(Category::class)->create();

        $response = $this->json('DELETE',route('categories.destroy', ['category' => $category->id]));

        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));

    }

    protected function assertInvalidationRequired($response)
    {

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' => 'name'])

            ]);
    }

    protected function assertInvalidationMax($response)
    {

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])

            ]);
    }

    protected function assertInvalidationBoolean($response)
    {

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.boolean', ['attribute' => 'is active'])

            ]);
    }
}
