<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('redis', [PostController::class, 'index']);
Route::get('redis/{id}', [PostController::class, 'show']);
Route::post('redis', [PostController::class, 'store']);
Route::put('redis/{id}', [PostController::class, 'update']);
Route::delete('redis/{id}', [PostController::class, 'destroy']);
