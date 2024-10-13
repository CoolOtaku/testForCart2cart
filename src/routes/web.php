<?php

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\AnimalController;

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

Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
Route::get('/messages/process', [MessageController::class, 'process'])->name('messages.process');

Route::get('/artisan_command', function () {
    return view('artisan_command');
})->name('artisan_command');

Route::get('/shorten', [UrlController::class, 'index'])->name('shorten.index');
Route::post('/shorten', [UrlController::class, 'shorten'])->name('shorten.shorten');

Route::get('/animal', [AnimalController::class, 'index'])->name('animal.index');
Route::post('/animal', [AnimalController::class, 'sound'])->name('animal.sound');

Route::get('/task6', function () {
    return view('task6');
})->name('task6');

Route::get('/task7', function () {
    return view('task7');
})->name('task7');



Route::get('/{shortUrl}', [UrlController::class, 'redirect']);

Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'uk'])) {
        Session::put('locale', $locale);
    }
    return Redirect::back();
})->name('switchLang');
