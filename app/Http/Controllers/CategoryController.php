<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BasicCrudController
// class CategoryController extends Controller
{

    private $rules = [
        'name' => 'required||max:255',
        'is_active' => 'boolean',
        'description' => 'nullable'
    ];

    protected function model()
    {
        return Category::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    // Estrutura Antiga

    // public function create() {}
    // public function edit(Category $category) {}

    // public function index()
    // {
    //     return Category::all();
    // }

    // // modelo 1 de validar, trocando Request por CategoryRequest
    // public function store(Request $request)
    // {
    //     // outro metodo de validar info
    //     $this->validate($request, $this->rules);
    //     $category = Category::create($request->all());
    //     $category->refresh();
    //     return $category;
    // }


    // public function show(Category $category)
    // {
    //     return $category;
    // }

    // public function update(Request $request, Category $category)
    // {
    //     $this->validate($request, $this->rules);
    //     $category->update($request->all());
    //     return $category;
    // }


    // public function destroy(Category $category)
    // {
    //     $category->delete();
    //     return response()->noContent(); //204
    // }
}
