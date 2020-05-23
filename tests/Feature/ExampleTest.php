<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    use DatabaseMigrations;

    public function testDB()
    {
        
        Genre::create(['name' =>  'firstTest']);

        $this->assertTrue(true);

    }

}
