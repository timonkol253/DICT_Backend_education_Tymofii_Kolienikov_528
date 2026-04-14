<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestbookController;

// Головний маршрут гостьової книги
Route::get('/guestbook', [GuestbookController::class, 'index'])->name('guestbook');
Route::post('/guestbook', [GuestbookController::class, 'store'])->name('guestbook.store');

// Перенаправлення з головної сторінки на гостьову книгу
Route::get('/', function () {
    return redirect()->route('guestbook');
});
