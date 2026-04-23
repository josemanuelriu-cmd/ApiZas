<?php
use Illuminate\Support\Facades\Route;

Route::get('/v1', fn() => response()->json([
    'app'    => config('app.name'),
    'version'=> 'v1',
    'status' => 'ok',
]));