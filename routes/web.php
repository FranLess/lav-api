<?php

use App\Listeners\SendWelcomeEmail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest:' . config('fortify.guard')])
    ->get('/reset-password/{token}', function ($token) {
        return $token;
    })->name('password.reset');


if (App::isLocal()) {

    Route::get('mail-sample', function () {
        return (new WelcomeMail(User::factory()->make()))->render();
    });
}
