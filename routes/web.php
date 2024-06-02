<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/create', [LinkController::class, 'createLink'])->name('create');
Route::post('/create', [LinkController::class, 'create']);
Route::get('/links', [LinkController::class, 'index']);
Route::get('/links/count/{count?}', [LinkController::class, 'getLinksCount']);

Route::get('/links/search/{link}', [LinkController::class, 'search'])->name('search-link');
Route::post('/update-redis-value', [LinkController::class, 'updateRedisValue'])->name('update.redis.value');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
