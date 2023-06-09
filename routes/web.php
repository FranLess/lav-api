<?php

use App\Events\ChatMessageEvent;
use App\Listeners\SendWelcomeEmail;
use App\Mail\WelcomeMail;
use App\Models\Post;
use App\Models\User;
use App\Websockets\SocketHandler\UpdatePostSocketHandler;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Http\Request;
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

Route::get('app', function () {
    return view('app');
});

Route::get('shared/posts/{post}', function (Request $request, Post $post) {
    return "Specially made just for you 💕 ;) Post id: {$post->id}";
})->name('shared.post')->middleware('signed');

Route::post('/playground', function (Request $request) {
    event(new ChatMessageEvent($request->message, auth()->user()));
    return null;
});

Route::get('/ws', function () {
    return view('websocket');
});

Route::post('/chat-message', function (Request $request) {
    event(new ChatMessageEvent($request->message, auth()->user()));
    return $request->message;
});
if (App::isLocal()) {
    Route::get('mail-sample', function () {
        return (new WelcomeMail(User::factory()->make()))->render();
    });
}

WebSocketsRouter::webSocket('/socket/update/post', UpdatePostSocketHandler::class);
