<?php

use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Seeder;

class VideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $genres = Genre::all();

        factory(\App\Models\Video::class, 100)->create()
        ->each(function (Video $video) use ($genres) {
            $subGeres = $genres->random(5)->load('categories');
            $categoriesId = [];
            foreach ($subGeres as $genres) {
                array_push($categoriesId, ...$genres->categories->pluck('id')->toArray());
            }
            $categoriesId = array_unique($categoriesId);
            $video->categories()->attach($categoriesId);
            $video->genres()->attach($subGeres->pluck('id')->toArray());
        });
    }
}
