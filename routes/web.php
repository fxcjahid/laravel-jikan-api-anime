<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;

Route::get('', [AnimeController::class, 'index'])->name('animes.index');
Route::get('/animes/{anime}', [AnimeController::class, 'show'])->name('animes.show');
