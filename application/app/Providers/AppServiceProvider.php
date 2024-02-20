<?php

namespace App\Providers;

use App\GameCore\Game\Eloquent\GameFactoryEloquent;
use App\GameCore\Game\Eloquent\GameRepositoryEloquent;
use App\GameCore\Game\GameFactory;
use App\GameCore\Game\GameRepository;
use App\GameCore\GameDefinition\GameDefinitionRepository;
use App\GameCore\GameDefinition\PhPConfig\GameDefinitionRepositoryPhpConfig;
use App\GameCore\Player\Eloquent\PlayerAnonymousFactoryEloquent;
use App\GameCore\Player\Eloquent\PlayerAnonymousRepositoryEloquent;
use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Player\PlayerAnonymousRepository;
use App\GameCore\Services\HashGenerator\Md5\PlayerAnonymousHashGeneratorMd5;
use App\GameCore\Services\HashGenerator\PlayerAnonymousHashGenerator;
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

        app()->bind(PlayerAnonymousHashGenerator::class, fn() => new PlayerAnonymousHashGeneratorMd5());
        app()->bind(PlayerAnonymousRepository::class, fn() => new PlayerAnonymousRepositoryEloquent());
        app()->bind(PlayerAnonymousFactory::class, fn() => app()->make(PlayerAnonymousFactoryEloquent::class));
        app()->bind(GameDefinitionRepository::class, fn() => new GameDefinitionRepositoryPhpConfig());
        app()->bind(GameRepository::class, fn() => app()->make(GameRepositoryEloquent::class));
        app()->bind(GameFactory::class, fn() => app()->make(GameFactoryEloquent::class));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
