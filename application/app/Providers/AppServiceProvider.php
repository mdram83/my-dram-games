<?php

namespace App\Providers;

use App\GameCore\GameInvite\Eloquent\GameInviteFactoryEloquent;
use App\GameCore\GameInvite\Eloquent\GameInviteRepositoryEloquent;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameBox\PhpConfig\GameBoxRepositoryPhpConfig;
use App\GameCore\GameSetup\GameSetupAbsFactoryRepository;
use App\GameCore\GameSetup\PhpConfig\GameSetupAbsFactoryRepositoryPhpConfig;
use App\GameCore\Player\Eloquent\PlayerAnonymousFactoryEloquent;
use App\GameCore\Player\Eloquent\PlayerAnonymousRepositoryEloquent;
use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Player\PlayerAnonymousRepository;
use App\GameCore\Services\HashGenerator\Md5\HashGeneratorMd5;
use App\GameCore\Services\HashGenerator\HashGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Instantiated by PlayerMiddleware middleware */
        app()->bind(Player::class, fn() => null);

        app()->bind(HashGenerator::class, HashGeneratorMd5::class);
        app()->bind(PlayerAnonymousRepository::class, PlayerAnonymousRepositoryEloquent::class);
        app()->bind(PlayerAnonymousFactory::class, PlayerAnonymousFactoryEloquent::class);
        app()->bind(GameBoxRepository::class, GameBoxRepositoryPhpConfig::class);
        app()->bind(GameInviteRepository::class, GameInviteRepositoryEloquent::class);
        app()->bind(GameInviteFactory::class, GameInviteFactoryEloquent::class);
        app()->bind(GameSetupAbsFactoryRepository::class, GameSetupAbsFactoryRepositoryPhpConfig::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
