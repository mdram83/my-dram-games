<?php

namespace App\Providers;

use App\GameCore\GameInvite\Eloquent\GameInviteFactoryEloquent;
use App\GameCore\GameInvite\Eloquent\GameInviteRepositoryEloquent;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameBox\PhpConfig\GameBoxRepositoryPhpConfig;
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

        app()->bind(HashGenerator::class, fn() => new HashGeneratorMd5());
        app()->bind(PlayerAnonymousRepository::class, fn() => new PlayerAnonymousRepositoryEloquent());
        app()->bind(PlayerAnonymousFactory::class, fn() => app()->make(PlayerAnonymousFactoryEloquent::class));
        app()->bind(GameBoxRepository::class, fn() => new GameBoxRepositoryPhpConfig());
        app()->bind(GameInviteRepository::class, fn() => app()->make(GameInviteRepositoryEloquent::class));
        app()->bind(GameInviteFactory::class, fn() => app()->make(GameInviteFactoryEloquent::class));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
