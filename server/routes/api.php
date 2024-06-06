<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware(['check.api.token'])->group(function () {

    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('/Hello', function (Request $request) {
            return response()->json("hello");
        });
        
});

    Route::get('/protected-route', function () {
        return response()->json(['message' => 'You have access']);
    });

    Route::controller(AuthController::class)->group(function(){
        Route::post('/login','login');
        Route::post('/register','register');
    });

});
