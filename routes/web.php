<?php

/*use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
/*use App\Http\Controllers\ProducerController;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;*/
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*
Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [AuthController::class, 'welcome']);
    Route::post('/login', [AuthController::class, 'login']);
});



Route::group(['middleware' => ['admin','auth:api']], function () {

});


Route::group(['middleware' => ['client','auth:api']], function () {
    
});


Route::group(['middleware' => 'auth'], function(){
    Route::post('/token',function(Request $request){
        return response(['token'=>Auth::user()->getRememberToken()]);
    });
    Route::get('/logout', [AuthController::class, 'logout']);
});


Route::post('/registerAdmin', [AuthController::class, 'registerAdmin'])->middleware('guest');*/
