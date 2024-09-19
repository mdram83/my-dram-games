<?php

use App\GameCore\GameElements\GameMove\PhpConfig\GameMoveAbsFactoryRepositoryPhpConfig;
use App\GameCore\GamePlay\PhpConfig\GamePlayAbsFactoryRepositoryPhpConfig;
use App\GameCore\GamePlay\PhpConfig\GamePlayAbsRepositoryPhpConfig;
use App\GameCore\GameSetup\PhpConfig\GameSetupAbsFactoryRepositoryPhpConfig;
use App\Games\Thousand\GameMoveAbsFactoryThousand;
use App\Games\Thousand\GamePlayAbsFactoryThousand;
use App\Games\Thousand\GamePlayThousand;
use App\Games\Thousand\GameSetupAbsFactoryThousand;
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
                'isPremium' => false,
                'gamePlayClassname' => \MyDramGames\Games\TicTacToe\Extensions\Core\GamePlayTicTacToe::class,
                'gameMoveFactoryClassname' => GameMoveTicTacToe::class,
                GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryTicTacToe::class,
                GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryTicTacToe::class,
                GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayTicTacToe::class,
                GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryTicTacToe::class,
            ],

            'thousand' => [
                'name' => 'Thousand',
                'description' => 'Another Classic, a Thousand Schnapsen playing card game.',
                'durationInMinutes' => 120,
                'minPlayerAge' => 10,
                'isActive' => true,
                'isPremium' => false,
                'gamePlayClassname' => \MyDramGames\Games\Thousand\Extensions\Core\GamePlay\GamePlayThousand::class,
                'gameMoveFactoryClassname' => \MyDramGames\Games\Thousand\Extensions\Core\GameMove\GameMoveFactoryThousand::class,
                GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryThousand::class,
                GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryThousand::class,
                GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayThousand::class,
                GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryThousand::class,
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
            'gamePlayClassname' => \MyDramGames\Games\TicTacToe\Extensions\Core\GamePlayTicTacToe::class,
            'gameMoveFactoryClassname' => GameMoveTicTacToe::class,
            GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryTicTacToe::class,
            GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryTicTacToe::class,
            GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayTicTacToe::class,
            GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryTicTacToe::class,
        ],

        'thousand' => [
            'name' => 'Thousand',
            'description' => 'Another Classic, a Thousand Schnapsen playing card game.',
            'durationInMinutes' => 120,
            'minPlayerAge' => 10,
            'isActive' => true,
            'isPremium' => true,
            'gamePlayClassname' => \MyDramGames\Games\Thousand\Extensions\Core\GamePlay\GamePlayThousand::class,
            'gameMoveFactoryClassname' => \MyDramGames\Games\Thousand\Extensions\Core\GameMove\GameMoveFactoryThousand::class,
            GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryThousand::class,
            GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryThousand::class,
            GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayThousand::class,
            GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryThousand::class,
        ],

        'turbo' => [
            'name' => 'Turbo',
            'description' => 'Take part in exciting car races.',
            'durationInMinutes' => 60,
            'minPlayerAge' => 10,
            'isActive' => false,
            'isPremium' => true,
            'gamePlayClassname' => \MyDramGames\Games\TicTacToe\Extensions\Core\GamePlayTicTacToe::class,
            'gameMoveFactoryClassname' => GameMoveTicTacToe::class,
            GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryTicTacToe::class,
            GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryTicTacToe::class,
            GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayTicTacToe::class,
            GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryTicTacToe::class,
        ],

        'netrunners' => [
            'name' => 'Netrunners',
            'description' => 'Fight evil corporate and become a new netrunners legend.',
            'durationInMinutes' => 90,
            'minPlayerAge' => 10,
            'isActive' => false,
            'isPremium' => true,
            'gamePlayClassname' => \MyDramGames\Games\TicTacToe\Extensions\Core\GamePlayTicTacToe::class,
            'gameMoveFactoryClassname' => GameMoveTicTacToe::class,
            GameSetupAbsFactoryRepositoryPhpConfig::GAME_SETUP_ABS_FACTORY_KEY => GameSetupAbsFactoryTicTacToe::class,
            GamePlayAbsFactoryRepositoryPhpConfig::GAME_PLAY_ABS_FACTORY_KEY => GamePlayAbsFactoryTicTacToe::class,
            GamePlayAbsRepositoryPhpConfig::GAME_PLAY_ABS_CLASS_KEY => GamePlayTicTacToe::class,
            GameMoveAbsFactoryRepositoryPhpConfig::GAME_MOVE_ABS_FACTORY_KEY => GameMoveAbsFactoryTicTacToe::class,
        ],

    ],
];
