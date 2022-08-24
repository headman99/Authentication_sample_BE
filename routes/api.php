<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\AuthController;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*** PUBLIC ***/

Route::group(['guest'], function () {
    Route::get('/', [AuthController::class, 'welcome']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/sanctum/csrfToken',[AuthController::class,'csrfToken']);
});


/*** AMDIN ***/
Route::group(['middleware' => ['admin','auth:sanctum']], function () {

});

/*** CLIENT ***/
Route::group(['middleware' => ['client','auth:sanctum']], function () {
    
});

/*** GENERAL ***/
Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('/accessToken',[AuthController::class, 'accessToken']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

/***API TOKEN AUTHENTICATION 
 * Tutte le route che non possono essere autenticate con l'accessToken ma che hanno bisogno lo stesso di autenticazione, viene usata l'api_token di ogni user.
 * ***/

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/message', function (Request $request) {
        return response(['message' => 'authenticated correctly']);
    })->middleware(['auth:api']);
    
});

Route::get('/check', function(){

    if(Auth::check()){
        return response(['message' => 'yes']);
    }else{
        return response(['message' => 'no']);
    }
})->middleware('auth:sanctum');

Route::post('/registerAdmin', [AuthController::class, 'registerAdmin'])->middleware('guest');