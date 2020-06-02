<?php

namespace Tests\Controller\Feature;

use App\Http\Controllers\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

class BasicCrudControllerTest extends TestCase
{

    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    public function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        $category = CategoryStub::create(['name' => 'Name_test', 'description' => 'Teste_description']);
        $result = $this->controller->index()->toArray();
        $this->assertEquals([$category->toArray()], $result);
    }

    public function testInvalidationData()
    {
        $this->expectException(ValidationException::class);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => 'teste_name', 'description' => 'test_description']);

        $obj = $this->controller->store($request);

        $this->assertEquals(CategoryStub::find(1)->toArray(), $obj->toArray());
    }

    public function findOrFailFetchModel($id)
    {

        $category = CategoryStub::create(['name' => 'teste_name', 'description' => 'test_description']);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function findOrFailThrowExceptionWhenIdInvalid($id)
    {

        $this->expectException(ModelNotFoundException::class);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function testShow()
    {
        $category = CategoryStub::create(['name' => 'teste_name', 'description' => 'test_description']);
        $result = $this->controller->show($category->id);
        $this->assertEquals(CategoryStub::find(1)->toArray(), $result->toArray());
    }

    public function testUpdate()
    {
        $category = CategoryStub::create(['name' => 'teste_name', 'description' => 'test_description']);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => 'teste_name', 'description' => 'test_description']);

        $result = $this->controller->update($request, $category->id);
        $this->assertEquals(CategoryStub::find(1)->toArray(), $result->toArray());
    }

    public function testDestroy()
    {
        $category = CategoryStub::create(['name' => 'teste_name', 'description' => 'test_description']);
        $response = $this->controller->destroy($category->id);

        $this->createTestResponse($response)->assertStatus(204);

        $this->assertCount(0, CategoryStub::all());

    }
}
