<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {
    try {
        DB::select('SELECT 1');

        return response()->json([
            'status' => 'ok',
            'database' => 'ok',
        ], 200);
    } catch (Throwable) {
        return response()->json([
            'status' => 'error',
            'database' => 'unavailable',
        ], 503);
    }
});
