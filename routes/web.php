<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [FrontController::class, 'index']);
