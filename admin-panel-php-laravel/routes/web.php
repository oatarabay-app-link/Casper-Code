<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'CasperVPN PHP Admin Panel',
        'version' => '1.0.0',
        'status' => 'running',
        'framework' => 'Laravel ' . app()->version()
    ]);
});

Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
});
