<?php

namespace App\Http\Controllers;

use App\Models\Video;

class VideoController extends BasicCrudController
{
    
    private $rules;

    public function __construct()
    {
        
    }

    protected function model()
    {
        return Video::class;
    }

    protected function rulesStore()
    {
        // TODO: store
    }

    protected function rulesUpdate()
    {
        // TODO: update
    }

}
