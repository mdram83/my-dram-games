<?php

namespace App\Providers;

use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\Game\GameFactoryEloquent;
use App\Models\GameCore\Game\GameRepository;
use App\Models\GameCore\Game\GameRepositoryEloquent;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use App\Models\GameCore\GameDefinition\GameDefinitionRepositoryPhpConfig;
use App\Models\GameCore\Player\Player;
use App\Models\GameCore\Player\PlayerAnonymousFactoryEloquent;
use App\Models\GameCore\Player\PlayerAnonymousFactory;
use App\Models\GameCore\Player\PlayerAnonymousHashGenerator;
use App\Models\GameCore\Player\PlayerAnonymousHashGeneratorMd5;
use App\Models\GameCore\Player\PlayerAnonymousRepository;
use App\Models\GameCore\Player\PlayerAnonymousRepositoryEloquent;
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
