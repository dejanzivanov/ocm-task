<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/news/settings', [App\Http\Controllers\NewsController::class, 'settings'])->name('news.settings')->middleware('auth');
Route::post('/news/api-save', [App\Http\Controllers\NewsController::class, 'apiSave'])->name('news.apiSave')->middleware('auth');
Route::post('/news/api-delete', [App\Http\Controllers\NewsController::class,'apiDelete'])->name('news.apiDelete')->middleware('auth');
