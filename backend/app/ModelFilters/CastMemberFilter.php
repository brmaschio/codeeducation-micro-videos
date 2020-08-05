<?php

namespace App\ModelFilters;

class CastMemberFilter extends DefaultModelFilter
{

    public $relations = [];

    protected $sortable = ['name', 'type', 'created_at'];

    public function search($search)
    {
        $this->where('name', 'like', "%$search%");
    }

}
