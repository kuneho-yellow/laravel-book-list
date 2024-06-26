<?php

use App\Http\Controllers\BookController;

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

Route::get('/', [BookController::class, 'index'])->name('index');
Route::get('/books', [BookController::class, 'query'])->name('books');
Route::post('/add', [BookController::class, 'store'])->name('books.add');
Route::delete('/book/{id}', [BookController::class, 'destroy'])->name('books.delete');
Route::put('/book/{id}', [BookController::class, 'update'])->name('books.edit');
Route::post('/export', [BookController::class, 'export'])->name('books.export');
Route::fallback(function () {
    return redirect()->route('index');
});
