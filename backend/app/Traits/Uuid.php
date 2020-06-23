<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid as uuid4;

trait Uuid
{

    public static function boot()
    {
        parent::boot();
        
        static::creating(function($obj){
            $obj->id = uuid4::uuid4();
        });
    
    }

}