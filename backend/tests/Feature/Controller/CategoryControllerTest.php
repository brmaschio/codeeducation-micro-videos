<?php

namespace Tests\Feature\Controller;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;
    private $serializeFields = [
        'id', 'name', 'description', 'is_active', 'deleted_at', 'created_at', 'updated_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200)->assertJson([
            'meta' => ['per_page' => 15]
        ])->assertJsonStructure([
            'data' => ['*' => $this->serializeFields],
            'links' => [],
            'meta'  => [],
        ]);

        $resource = CategoryResource::collection(collect([$this->category]));
        $response->assertJson($resource->response()->getData(true));
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));
        // $response->assertStatus(200)->assertJson($this->category->toArray());

        $response->assertStatus(200);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $response->assertJson($resource->response()->getData(true));
    }

    public function testInvalidationData()
    {
        $data = ['name' => ''];
        $this->assertInvalidationStoreActions($data, 'required');
        $this->assertInvalidationUpdateActions($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationStoreActions($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationUpdateActions($data, 'max.string', ['max' => 255]);

        $data = ['is_active' => 'a'];
        $this->assertInvalidationStoreActions($data, 'boolean');
        $this->assertInvalidationUpdateActions($data, 'boolean');
    }

    public function testStore()
    {
        $data = ['name' => 'test'];
        $dataCheck = ['description' => null, 'is_active' => true, 'deleted_at' => null];
        $response = $this->assertStore($data, $data + $dataCheck);

        // $response->assertJsonStructure(['data' => ['created_at', 'updated_at']]);
        $response->assertJsonStructure(['data' => $this->serializeFields]);

        $data = ['name' => 'test', 'description' => 'desc', 'is_active' => false];
        $dataCheck = ['description' => 'desc', 'is_active' => false];
        $this->assertStore($data, $data + $dataCheck);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $response->assertJson($resource->response()->getData(true));
    }

    public function testUpdate()
    {
        $data = ['description' => 'description', 'is_active' => false];
        $this->category = factory(Category::class)->create($data);

        $data = ['name' => 'test', 'description' => 'desc', 'is_active' => true];
        $dataCheck = ['deleted_at' => null];
        $response = $this->assertUpdate($data, $data + $dataCheck);
        $response->assertJsonStructure(['data' => $this->serializeFields]);

        $id = $response->json('data.id');
        $resource = new CategoryResource(Category::find($id));
        $response->assertJson($resource->response()->getData(true));

        $data = ['name' => 'test', 'description' => ''];
        $dataCheck = ['description' => null];
        $this->assertUpdate($data, array_merge($data, $dataCheck));

        $data['description'] = 'test';
        $dataCheck = ['description' => 'test'];
        $this->assertUpdate($data, array_merge($data, $dataCheck));

        $data['description'] = null;
        $dataCheck = ['description' => null];
        $this->assertUpdate($data, array_merge($data, $dataCheck));
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('categories.destroy', ['category' => $this->category->id]));

        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', [$this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }
}
