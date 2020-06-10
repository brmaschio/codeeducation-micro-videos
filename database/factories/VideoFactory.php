<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    
    $rating = Video::RATING_LIST[array_rand(Video::RATING_LIST)];
    
    return [
        'title' => $faker->unique()->sentence(4),
        'description' => $faker->text(10),
        'year_launched' => rand(1895, 2020),
        'opened' => rand(0, 1),
        'rating' => $rating,
        'duration' => rand(1, 30)
    ];
});
