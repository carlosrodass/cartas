<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\SellingController;

use App\Http\Middleware\Admin;
use App\Http\Middleware\NonAdmin;
/*
*Grupo de rutas de usuarios , Registro/Login/Reset_password
*/
Route::prefix('users')->group(function (){

	Route::post('/register',[UserController::class, 'signUp']);
	Route::post('/login',[UserController::class, 'signIn']);
	Route::post('/reset',[UserController::class, 'resetPass']);

});
/*
*Grupo de rutas de cartas , Crear/Editar
*/
Route::prefix('cards')->group(function (){

	Route::post('/create',[CardController::class, 'createCard']);
	Route::post('/update',[CardController::class, 'updateCard']);
	// ->middleware('Admin');

});
/*
*Grupo de rutas de colecciones , Crear/Editar
*/
Route::prefix('collection')->group(function (){

	Route::post('/create',[CollectionController::class, 'createCollection']);
	Route::post('/update',[CollectionController::class, 'updateCollection']);
	// ->middleware('Admin');

});

