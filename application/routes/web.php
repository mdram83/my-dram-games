<?php

use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\GameDefinition\GameDefinitionAjaxController;
use App\Http\Controllers\GameDefinition\GameDefinitionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/games/{slug}', [GameDefinitionController::class, 'show'])->name('games');
Route::get('/join/{gameId}', fn() => 'joining test')->name('join');

Route::middleware('ajax')->group(function() {
    Route::get('/ajax/gameDefinition', [GameDefinitionAjaxController::class, 'index'])->name('ajax.gameDefinition.index');
    Route::post('/ajax/play/{slug}', [GameController::class, 'store'])->name('ajax.play.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
