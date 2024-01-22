<?php

use App\Http\Controllers\GameDefinition\GameDefinitionAjaxController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/games/{slug}', fn() => view('home'))->name('games');
Route::get('/play/{slug}', fn() => view('home'))->name('play');

Route::get('/', HomeController::class)->name('home');

Route::middleware('ajax')->group(function() {
    Route::get('/ajax/gameDefinition', [GameDefinitionAjaxController::class, 'index'])
        ->name('ajax.gameDefinition.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
