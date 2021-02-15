<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Registro y login de usuario
Route::post('register', 'App\Http\Controllers\UserController@register');
Route::post('login', 'App\Http\Controllers\UserController@authenticate');
Route::post('reset', 'App\Http\Controllers\UserController@resetPass');


//Autenticacion de usuario
Route::group(['middleware' => ['jwt.verify']], function() {

	//Perfil de usuario
    Route::post('user','App\Http\Controllers\UserController@getAuthenticatedUser');


    //Crear carta
    Route::post('create','App\Http\Controllers\cardController@createCard');
    

    // Route::post('create','App\Http\Controllers\UserController@createCard');

});
