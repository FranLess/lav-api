<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::prefix('comments')
    ->name('comments.')
    ->controller(CommentController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::get('/{comment}', 'show')
            ->name('show');

        Route::post('/', 'store')
            ->name('store');

        Route::patch('/{comment}', 'update')
            ->name('update');

        Route::delete('{comment}', 'destroy')
            ->name('destroy');
    });
