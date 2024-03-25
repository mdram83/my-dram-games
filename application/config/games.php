<?php

use App\GameCore\GameElements\GameMove\PhpConfig\GameMoveAbsFactoryRepositoryPhpConfig;
use App\GameCore\GamePlay\PhpConfig\GamePlayAbsFactoryRepositoryPhpConfig;
use App\GameCore\GamePlay\PhpConfig\GamePlayAbsRepositoryPhpConfig;
use App\GameCore\GameSetup\PhpConfig\GameSetupAbsFactoryRepositoryPhpConfig;
use App\Games\TicTacToe\GameMoveAbsFactoryTicTacToe;
use App\Games\TicTacToe\GameMoveTicTacToe;
use App\Games\TicTacToe\GamePlayAbsFactoryTicTacToe;
use App\Games\TicTacToe\GamePlayTicTacToe;
use App\Games\TicTacToe\GameSetupAbsFactoryTicTacToe;

if (config('app.env') === 'production') {

    /* List of production ready games */
    return [

        'box' => [

            'tic-tac-toe' => [
                'name' => 'Tic Tac Toe',
                'description' => 'Famous Tic Tac Toe game that you can now play with friends online!',
                'durationInMinutes' => 1,
                'minPlayerAge' => 4,
                'isActive' => true,
                GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryTicTacToe::class,
                GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryTicTacToe::class,
                GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayTicTacToe::class,
                GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryTicTacToe::class,
            ],
        ],
    ];
}

/* List of non production environment games */
return [

    'box' => [

        'tic-tac-toe' => [
            'name' => 'Tic Tac Toe',
            'description' => 'Famous Tic Tac Toe game that you can now play with friends online!',
            'durationInMinutes' => 1,
            'minPlayerAge' => 4,
            'isActive' => true,
            GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryTicTacToe::class,
            GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryTicTacToe::class,
            GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayTicTacToe::class,
            GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryTicTacToe::class,
        ],

        'turbo' => [
            'name' => 'Turbo',
            'description' => 'Take part in exciting car races.',
            'durationInMinutes' => 60,
            'minPlayerAge' => 10,
            'isActive' => false,
            GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryTicTacToe::class,
            GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryTicTacToe::class,
            GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayTicTacToe::class,
            GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryTicTacToe::class,
        ],

        'boss-monster-raise-of-the-minibosses' => [
            'name' => 'Boss Monster - Rise Of The Minibosses',
            'description' => 'Become a villain, build a dungeon, lure in adventurers, and destroy them!',
            'durationInMinutes' => 30,
            'minPlayerAge' => 13,
            'isActive' => false,
            GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryTicTacToe::class,
            GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryTicTacToe::class,
            GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayTicTacToe::class,
            GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryTicTacToe::class,
        ],

    ],
];
