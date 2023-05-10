<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->name('users.')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/{user}', 'show')
            ->name('show');

        Route::post('/', 'store')
            ->name('store');

        Route::patch('/{user}', 'update')
            ->name('update');

        Route::delete('{user}', 'destroy')
            ->name('destroy');
    });
