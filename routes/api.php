<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Registro y login de usuario
Route::post('register', 'App\Http\Controllers\UserController@register');
Route::post('login', 'App\Http\Controllers\UserController@authenticate');

//Autenticacion de usuario
Route::group(['middleware' => ['jwt.verify']], function() {

	//Perfil de usuario
    Route::post('user','App\Http\Controllers\UserController@getAuthenticatedUser');
    Route::post('create','App\Http\Controllers\UserController@createCard');

});
