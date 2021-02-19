<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
*Grupo de rutas de usuarios , Registro/Login/Reset_password
*/
Route::prefix('users')->group(function (){
	//Registro y login de usuario
	Route::post('/register', 'App\Http\Controllers\UserController@signUp');
	Route::post('/login', 'App\Http\Controllers\UserController@SignIn');
	Route::post('/reset', 'App\Http\Controllers\UserController@resetPass');

}
/*
*Grupo de rutas de cartas , Crear/Editar
*/
Route::prefix('cards')->group(function (){

	Route::post('/create','App\Http\Controllers\cardController@createCard')->middleware('Admin');

}
/*
*Grupo de rutas de colecciones , Crear/Editar
*/
Route::prefix('collection')->group(function (){

	Route::post('/create','App\Http\Controllers\collectionController@createCollection')->middleware('Admin');

}

