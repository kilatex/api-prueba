<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\API\AuthController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);

Route::get('api/profile/{id?}', [PersonController::class,'show'])->middleware('auth:api');
Route::post('api/person/register', [PersonController::class,'store']);
Route::get('api/person/{number?}', [PersonController::class,'list'])->middleware('auth:api');
Route::get('api/person/avatar/{filename?}', [PersonController::class,'getAvatar']);
Route::post('api/person/update/{id_person?}', [PersonController::class,'update'])->middleware('auth:api');
Route::post('api/person/search', [PersonController::class,'search'])->middleware('auth:api');
Route::delete('api/person/{id_person?}', [PersonController::class,'destroy'])->middleware('auth:api');


/*
{
    "first_name" : "Santiago",
    "last_name"  : "Maldonado",
    "document" : "29699795",
    "type_person" : "1",
    "email" : "santiagodmaldon@gmail.com"
}
*/