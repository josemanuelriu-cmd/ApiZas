<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/v1/login', [UserController::class, 'login']);
Route::post('/v1/register', [UserController::class, 'register']);
Route::middleware('auth:api')->group(function () {
    Route::post('/v1/logout', [UserController::class, 'logout']);
});
