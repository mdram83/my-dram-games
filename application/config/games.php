<?php

use MyDramGames\Games\Netrunners\GameMove\GameMoveNetrunnersFactory;
use MyDramGames\Games\Netrunners\GamePlay\GamePlayNetrunners;
use MyDramGames\Games\Netrunners\GameSetup\GameSetupNetrunners;
use MyDramGames\Games\Thousand\Extensions\Core\GameMove\GameMoveFactoryThousand;
use MyDramGames\Games\Thousand\Extensions\Core\GamePlay\GamePlayThousand;
use MyDramGames\Games\Thousand\Extensions\Core\GameSetup\GameSetupThousand;
use MyDramGames\Games\TicTacToe\Extensions\Core\GameMoveTicTacToe;
use MyDramGames\Games\TicTacToe\Extensions\Core\GamePlayTicTacToe;
use MyDramGames\Games\TicTacToe\Extensions\Core\GameSetupTicTacToe;

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
                'isPremium' => false,
                'gameSetupClassname' => GameSetupTicTacToe::class,
                'gamePlayClassname' => GamePlayTicTacToe::class,
                'gameMoveFactoryClassname' => GameMoveTicTacToe::class,
            ],

            'thousand' => [
                'name' => 'Thousand',
                'description' => 'Another Classic, a Thousand Schnapsen playing card game.',
                'durationInMinutes' => 120,
                'minPlayerAge' => 10,
                'isActive' => true,
                'isPremium' => false,
                'gameSetupClassname' => GameSetupThousand::class,
                'gamePlayClassname' => GamePlayThousand::class,
                'gameMoveFactoryClassname' => GameMoveFactoryThousand::class,
            ],

            'netrunners' => [
                'name' => 'Netrunners',
                'description' => 'Fight evil corporate and become a new netrunners legend.',
                'durationInMinutes' => 90,
                'minPlayerAge' => 10,
                'isActive' => true,
                'isPremium' => true,
                'gameSetupClassname' => GameSetupNetrunners::class,
                'gamePlayClassname' => GamePlayNetrunners::class,
                'gameMoveFactoryClassname' => GameMoveNetrunnersFactory::class,
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
            'isPremium' => false,
            'gameSetupClassname' => GameSetupTicTacToe::class,
            'gamePlayClassname' => GamePlayTicTacToe::class,
            'gameMoveFactoryClassname' => GameMoveTicTacToe::class,
        ],

        'thousand' => [
            'name' => 'Thousand',
            'description' => 'Another Classic, a Thousand Schnapsen playing card game.',
            'durationInMinutes' => 120,
            'minPlayerAge' => 10,
            'isActive' => true,
            'isPremium' => true,
            'gameSetupClassname' => GameSetupThousand::class,
            'gamePlayClassname' => GamePlayThousand::class,
            'gameMoveFactoryClassname' => GameMoveFactoryThousand::class,
        ],

        'turbo' => [
            'name' => 'Turbo',
            'description' => 'Take part in exciting car races.',
            'durationInMinutes' => 60,
            'minPlayerAge' => 10,
            'isActive' => false,
            'isPremium' => true,
            'gameSetupClassname' => GameSetupTicTacToe::class,
            'gamePlayClassname' => GamePlayTicTacToe::class,
            'gameMoveFactoryClassname' => GameMoveTicTacToe::class,
        ],

        'netrunners' => [
            'name' => 'Netrunners',
            'description' => 'Fight evil corporate and become a new netrunners legend.',
            'durationInMinutes' => 90,
            'minPlayerAge' => 10,
            'isActive' => true,
            'isPremium' => false,
            'gameSetupClassname' => GameSetupNetrunners::class,
            'gamePlayClassname' => GamePlayNetrunners::class,
            'gameMoveFactoryClassname' => GameMoveNetrunnersFactory::class,
        ],

    ],
];
