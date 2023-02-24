<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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
    Route::get('/sanctum/csrfToken', [AuthController::class, 'csrfToken']);
    Route::post('/registerAdmin', [AuthController::class, 'registerAdmin'])->middleware('guest');
    Route::post('/registerClient', [AuthController::class, 'registerClient'])->middleware('guest');
});


/*** AMDIN ***/
Route::group(['middleware' => ['admin', 'auth:sanctum']], function () {
    Route::post('/admin/registerIngredient', [AdminController::class, 'registerIngredient']);
    Route::get('/admin/stocks', [AdminController::class, 'getStock']);
    Route::post('/admin/removeIngredient', [AdminController::class, 'removeIngredient']);
    Route::post('/admin/updateIngredientQuantity', [AdminController::class, 'updateIngredientQuantity']);
    Route::post('/admin/updateIngredientDescription', [AdminController::class, 'updateIngredientDescription']);
    Route::post('/admin/addIngredientQuantity', [AdminController::class, 'addIngredientQuantity']);
    Route::get('/admin/getProductsCatalog', [AdminController::class, 'getProductsCatalog']);
    Route::post('/admin/removeProduct', [AdminController::class, 'removeProduct']);
    Route::post('/admin/registerProduct', [AdminController::class, 'registerProduct']);
    Route::get('/admin/getProductGroups', [AdminController::class, 'getProductGroups']);
    Route::post("/admin/addMenuRecipe", [AdminController::class, "addMenuRecipe"]);
    Route::post('/admin/removeMenuRecipe', [AdminController::class, 'removeMenuRecipe']);
    Route::post('/admin/updateMenuRecipeGroup', [AdminController::class, 'updateMenuRecipeGroup']);
    Route::post('/admin/updateMenuRecipeSection', [AdminController::class, 'updateMenuRecipeSection']);
    Route::post('/admin/removeMenuRecipeSection', [AdminController::class, 'removeMenuRecipeSection']);
    Route::post("/admin/getOrdersList" , [AdminController::class, "getOrdersList"] );
    Route::post("/admin/getOrdersListByDate" , [AdminController::class, "getOrdersListByDate"] );
    Route::post("/admin/getOpenProductsInstance",[AdminController::class, "getOpenProductsInstance"]);
    Route::post("/admin/getOrderListCodes",[AdminController::class, "getOrderListCodes"]);
    Route::post("admin/scanProduct", [AdminController::class,"scanProduct"]);
    Route::post("admin/getProductsInstanceByFilter", [AdminController::class,"getProductsInstanceByFilter"]);
    Route::get("admin/getTeams", [AdminController::class,"getTeams"]);
    //Route::post("admin/getProductListByTeam", [AdminController::class,"getProductListByTeam"]);
    Route::post("admin/checkProductList", [AdminController::class,"checkProductList"]);
    Route::post("admin/updateIngredient", [AdminController::class,"updateIngredient"]);
    Route::post("admin/updateProduct", [AdminController::class,"updateProduct"]);
    Route::post("admin/removeTeam", [AdminController::class,"removeTeam"]);
    Route::post("admin/updateTeam", [AdminController::class,"updateTeam"]);
    Route::post("admin/addTeam", [AdminController::class,"addTeam"]);
    Route::post("admin/getIngredientsTeam", [AdminController::class,"getIngredientsTeam"]);
    Route::post("admin/updateIngredientsTeam", [AdminController::class,"updateIngredientsTeam"]);
    Route::post("admin/getTeamProductListByOrder", [AdminController::class,"getTeamProductListByOrder"]);
    Route::post("admin/getTeamIngredientsByProductRecipe", [AdminController::class,"getTeamIngredientsByProductRecipe"]);
}); 

/*** CLIENT ***/
Route::group(['middleware' => ['client', 'auth:sanctum']], function () {
    Route::post('/client/addOrderMenu', [CustomerController::class,"addOrderMenu"]);
});

/*** GENERAL ***/
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/csrfToken', [AuthController::class, 'csrfToken']);
    Route::post("/user/getMenuCatalog", [UserController::class, "getMenuCatalog"]);
    Route::get('/user/getMenuDetails', [UserController::class, 'getMenuDetails']);
});

/***API TOKEN AUTHENTICATION 
 * Tutte le route che non possono essere autenticate con l'accessToken ma che hanno bisogno lo stesso di autenticazione, viene usata l'api_token di ogni user.
 * ***/

Route::group(['middleware' => 'auth:api'], function () {
});
