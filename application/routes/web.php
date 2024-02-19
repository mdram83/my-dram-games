<?php

use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\GameDefinition\GameDefinitionAjaxController;
use App\Http\Controllers\GameDefinition\GameDefinitionController;
use App\Http\Controllers\GamePlay\GamePlayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('player')->group(function() {
    Route::get('/games/{slug}', [GameDefinitionController::class, 'show'])->name('games.show');
    Route::get('/games/{slug}/{gameId}', [GameController::class, 'join'])->name('game-invites.join');
    Route::get('/play/{gameId}', [GamePlayController::class, 'show'])->name('gameplay.show');
});

Route::middleware(['ajax', 'player'])->group(function() {
    Route::post('/ajax/game-invites', [GameController::class, 'store'])->name('ajax.game-invites.store');
    Route::post('/ajax/gameplay', [GamePlayController::class, 'store'])->name('ajax.gameplay.store');
});

Route::middleware('ajax')->group(function() {
    Route::get('/ajax/games', [GameDefinitionAjaxController::class, 'index'])->name('ajax.games.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
