<?php

namespace App\Http\Controllers;

use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class GenreController extends BasicCrudController
// class GenreController extends Controller
{

    private $rules = [
        'name' => 'required||max:255',
        'is_active' => 'boolean',
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
    ];

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());

        $self = $this;

        $obj = \DB::transaction(function () use($request, $validatedData, $self) {
            $obj = $this->model()::create($validatedData);
            $self->handleRelarions($obj, $request);
            return $obj;
        });

        $obj->refresh();
        $resource = $this->resource();
        return new $resource($obj);
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());

        $self = $this;

        $obj = \DB::transaction(function () use($request, $validatedData, $self, $obj) {
            $obj->update($validatedData);
            $self->handleRelarions($obj, $request);
            $resource = $this->resource();
            return new $resource($obj);
        });

        return $obj;
    }

    protected function handleRelarions($obj, $request) {
        $obj->categories()->sync($request->get('categories_id'));
    }

    protected function model()
    {
        return Genre::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return GenreResource::class;
    }

    protected function queryBuilder(): Builder
    {
        return parent::queryBuilder()->with('categories');
    }

}
