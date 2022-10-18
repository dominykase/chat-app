<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatRoomController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/chat', function () {
    return Inertia::render('ChatPage');
})->middleware(['auth', 'verified'])->name('chat');

Route::middleware('auth:sanctum')->get('/chat/rooms', [ChatRoomController::class, 'rooms']);
Route::middleware('auth:sanctum')->post('/chat/room/create', [ChatRoomController::class, 'createChatRoom']);
Route::middleware('auth:sanctum')->get('/chat/room/{roomId}/users', [ChatRoomController::class, 'getUsers']);

Route::middleware('auth:sanctum')->get('/chat/room/{roomId}', [ChatController::class, 'messages']);
Route::middleware('auth:sanctum')->post('/chat/rooms/{roomId}/message', [ChatController::class, 'newMessage']);
