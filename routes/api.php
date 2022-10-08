<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TareaController;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::group(['prefix' => 'tareas'], function () {
        Route::get('/', [TareaController::class, 'index']);
        Route::post('/', [TareaController::class, 'store']);
        Route::get('/{id}', [TareaController::class, 'show']);
        Route::put('/{id}', [TareaController::class, 'update']);
        Route::delete('/{id}', [TareaController::class, 'destroy']);
    });
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
