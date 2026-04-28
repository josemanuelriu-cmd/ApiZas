<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\BoardgameController;
use App\Http\Controllers\ZassessionController;
use App\Http\Controllers\GameController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/docs-scalar', 'scalar');

Route::get('/v1/debug-token', function () {
    $privateKey = config('passport.private_key');
    $publicKey  = config('passport.public_key');

    $testData  = 'test';
    $signature = '';
    $signOk    = openssl_sign($testData, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $verifyOk  = $signOk ? openssl_verify($testData, $signature, $publicKey, OPENSSL_ALGO_SHA256) : -1;

    return response()->json([
        'private_key_prefix' => substr($privateKey ?? '', 0, 27),
        'private_key_length' => strlen($privateKey ?? ''),
        'public_key_prefix'  => substr($publicKey ?? '', 0, 26),
        'public_key_length'  => strlen($publicKey ?? ''),
        'sign_ok'            => $signOk,
        'verify_ok'          => $verifyOk,
        'openssl_error'      => openssl_error_string(),
    ]);
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

    //Zassessions
    Route::prefix('v1/zassessions')->group(function () {
        Route::get('', [ZassessionController::class, 'index']); 
        Route::get('/{id}', [ZassessionController::class, 'detail'])->where('id', '[0-9]+');
        Route::post('', [ZassessionController::class, 'store'])
            ->middleware('role:admin,junta'); 
        Route::put('/{id}', [ZassessionController::class, 'update'])
            ->middleware('role:admin,junta')
            ->where('id', '[0-9]+'); 
        Route::delete('/{id}', [ZassessionController::class, 'destroy'])
            ->middleware('role:admin,junta')
            ->where('id', '[0-9]+'); 
        Route::post('/{id}/join', [ZassessionController::class, 'join'])->where('id', '[0-9]+'); 
        Route::delete('/{id}/leave', [ZassessionController::class, 'leave'])->where('id', '[0-9]+'); 
        Route::get('/{id}/users', [ZassessionController::class, 'getUsers'])->where('id', '[0-9]+'); 
        Route::get('/stats', [ZassessionController::class, 'allstats'])
            ->middleware('role:admin,junta,partner'); 
        Route::get('/{id}/stats', [ZassessionController::class, 'sessionStats'])
            ->middleware('role:admin,junta,partner')
            ->where('id', '[0-9]+'); 


        Route::get('/{id}/games', [GameController::class, 'indexSession'])
            ->middleware('role:admin,junta,partner,guest')
            ->where('id', '[0-9]+');     
    });

    //Games    
    Route::prefix('v1/games')->group(function () {
        Route::get('', [GameController::class, 'indexAll'])
            ->middleware('role:admin,junta,partner'); 
        Route::get('/{id}', [GameController::class, 'detail'])
            ->where('id', '[0-9]+'); 
        Route::post('', [GameController::class, 'store'])
            ->middleware('role:admin,junta,partner'); 
        Route::put('/{id}', [GameController::class, 'update'])
            ->middleware('role:admin,junta')
            ->where('id', '[0-9]+'); 
        Route::delete('/{id}', [GameController::class, 'destroy'])
            ->middleware('role:admin,junta')
            ->where('id', '[0-9]+'); 
        Route::post('/{id}/join', [GameController::class, 'join'])->where('id', '[0-9]+'); 
        Route::delete('/{id}/leave', [GameController::class, 'leave'])->where('id', '[0-9]+'); 
        Route::get('/{id}/users', [GameController::class, 'getUsers'])->where('id', '[0-9]+'); 
        Route::get('/{id}/stats', [GameController::class, 'gameStats'])
            ->middleware('role:admin,junta,partner')
            ->where('id', '[0-9]+'); 
    });

    
});
