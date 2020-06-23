<?php

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Seeder;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $categories = Category::all();

        factory(\App\Models\Genre::class, 20)->create()
            ->each(function (Genre $genre) use ($categories) {
                $categoriesId = $categories->random(2)->pluck('id')->toArray();
                $genre->categories()->attach($categoriesId);
            });
    }
}
