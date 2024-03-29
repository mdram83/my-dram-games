<?php

use App\Http\Controllers\GameCore\GameInviteController;
use App\Http\Controllers\GameCore\GameBoxAjaxController;
use App\Http\Controllers\GameCore\GameBoxController;
use App\Http\Controllers\GameCore\GamePlayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('player')->group(function() {
    Route::get('/games/{slug}', [GameBoxController::class, 'show'])->name('games.show');
    Route::get('/games/{slug}/{gameInviteId}', [GameInviteController::class, 'join'])->name('game-invites.join');
    Route::get('/play/{gamePlayId}', [GamePlayController::class, 'show'])->name('gameplay.show');
});

Route::middleware(['ajax', 'player'])->group(function() {
    Route::post('/ajax/game-invites', [GameInviteController::class, 'store'])->name('ajax.game-invites.store');
    Route::post('/ajax/gameplay', [GamePlayController::class, 'store'])->name('ajax.gameplay.store');
    Route::post('/ajax/gameplay/{gamePlayId}', [GamePlayController::class, 'move'])->name('ajax.gameplay.move');
});

Route::middleware('ajax')->group(function() {
    Route::get('/ajax/games', [GameBoxAjaxController::class, 'index'])->name('ajax.games.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
