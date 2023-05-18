<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('posts')
    ->name('posts.')
    ->controller(PostController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/{post}', 'show')
            ->name('show');

        Route::post('/', 'store')
            ->name('store');

        Route::patch('/{post}', 'update')
            ->name('update');

        Route::delete('{post}', 'destroy')
            ->name('destroy');

        Route::get('/{post}/share', 'share')
            ->name('share');
    });
