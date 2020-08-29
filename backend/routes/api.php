<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('categories', 'CategoryController');
Route::delete('categories', 'CategoryController@destroyCollection');

Route::resource('genres', 'GenreController');
Route::delete('genres', 'GenreController@destroyCollection');

Route::resource('cast_members', 'CastMemberController');
Route::delete('cast_members', 'CastMemberController@destroyCollection');

Route::resource('videos', 'VideoController');
Route::delete('videos', 'VideoController@destroyCollection');

