<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\BoardgameController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/v1/login', [UserController::class, 'login']);
Route::post('/v1/register', [UserController::class, 'register']);
Route::middleware('auth:api')->group(function () {
    //Logout
    Route::post('/v1/logout', [UserController::class, 'logout']);

    //Users
    Route::prefix('v1/users')->group(function () {
        Route::get('', [UserController::class, 'index'])
            ->middleware('role:admin,junta'); 
        Route::get('/{id}', [UserController::class, 'detail'])
            ->middleware('role:admin,junta')
            ->where('id', '[0-9]+'); 
        Route::post('', [UserController::class, 'store'])
            ->middleware('role:admin'); 
        Route::put('/{id}', [UserController::class, 'update'])
            ->where('id', '[0-9]+'); 
        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->middleware('role:admin')    
            ->where('id', '[0-9]+'); 
    });

    //Types
    Route::prefix('v1/types')->group(function () {
        Route::get('', [TypeController::class, 'index'])
            ->middleware('role:admin,junta,partner'); 
        Route::get('/{id}', [TypeController::class, 'detail'])
            ->middleware('role:admin,junta,partner')
            ->where('id', '[0-9]+'); 
        Route::post('', [TypeController::class, 'store'])
            ->middleware('role:admin,junta'); 
        Route::put('/{id}', [TypeController::class, 'update'])
            ->middleware('role:admin,junta')
            ->where('id', '[0-9]+'); 
        Route::delete('/{id}', [TypeController::class, 'destroy'])
            ->middleware('role:admin')
            ->where('id', '[0-9]+'); 
    });
    
    //Boardgames
    Route::prefix('v1/boardgames')->group(function () {
        Route::get('', [BoardgameController::class, 'index']); 
        Route::get('/{id}', [BoardgameController::class, 'detail'])
            ->where('id', '[0-9]+'); 
        Route::post('', [BoardgameController::class, 'store'])
            ->middleware('role:admin,junta'); 
        Route::put('/{id}', [BoardgameController::class, 'update'])
            ->middleware('role:admin,junta')
            ->where('id', '[0-9]+'); 
        Route::delete('/{id}', [BoardgameController::class, 'destroy'])
            ->middleware('role:admin,junta')
            ->where('id', '[0-9]+'); 
    });
    
});
