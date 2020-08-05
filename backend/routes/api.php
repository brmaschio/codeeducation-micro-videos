<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('categories', 'CategoryController');
Route::resource('genres', 'GenreController');
Route::resource('cast_members', 'CastMemberController');
Route::resource('videos', 'VideoController');