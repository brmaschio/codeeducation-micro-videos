<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryFeatureTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {

        // $category = Category::create(['name' => 'test']);
        // ou
        factory(Category::class, 1)->create();

        $categories = Category::all();

        // Teste 1
        $this->assertCount(1, $categories);

        $categoryKey = array_keys($categories->first()->getAttributes());
        
        // Teste 2
        $this->assertEqualsCanonicalizing([
            'id', 'name', 'description', 'created_at', 'updated_at', 'deleted_at', 'is_active'
        ], $categoryKey);
        
    }

    public function testCreate()
    {

        $category = Category::create(['name' => 'test']);
        $category->refresh();

        // Teste uuid valida
        $uuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $category->id);
        $this->assertEquals(1, $uuid);

        // Teste 1,2,3
        $this->assertEquals('test', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        // Teste 4
        $category = Category::create(['name' => 'test', 'description' => null]);
        $this->assertNull($category->description);

        // Teste 5
        $category = Category::create(['name' => 'test', 'description' => 'teste_desc']);
        $this->assertEquals($category->description, 'teste_desc');

        // Teste 6
        $category = Category::create(['name' => 'test', 'is_active' => false]);
        $this->assertFalse($category->is_active);

        // Teste 7
        $category = Category::create(['name' => 'test', 'is_active' => true]);
        $this->assertTrue($category->is_active);

    }

    public function testUpdate() {

        $category = factory(Category::class)->create(
            ['description' => 'teste_desc','is_active' => false]
        )->first();

        $data = ['name' => 'name_update', 'description' => 'teste_desc_update', 'is_active' => true];
        $category->update($data);

        foreach($data as $key => $value){
            $this->assertEquals($value, $category->{$key});
        }

    }

    public function testDelete() {

        $category = Category::create(['name' => 'test']);

        $category->delete();
        $this->assertNull(Category::find($category->id));

    }
}
