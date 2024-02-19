<?php

namespace App\Providers;

use App\Models\GameCore\Game\GameFactory;
use App\Models\GameCore\Game\GameFactoryEloquent;
use App\Models\GameCore\Game\GameRepository;
use App\Models\GameCore\Game\GameRepositoryEloquent;
use App\Models\GameCore\GameDefinition\GameDefinitionFactory;
use App\Models\GameCore\GameDefinition\GameDefinitionFactoryPhpConfig;
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
        // Provided by 'player' => PlayerMiddleware class
        app()->bind(Player::class, fn() => null);

        app()->bind(PlayerAnonymousRepository::class, fn() => new PlayerAnonymousRepositoryEloquent());
        app()->bind(GameDefinitionFactory::class, fn() => new GameDefinitionFactoryPhpConfig());
        app()->bind(PlayerAnonymousHashGenerator::class, fn() => new PlayerAnonymousHashGeneratorMd5());
        app()->bind(GameRepository::class, fn() => app()->make(GameRepositoryEloquent::class));

        app()->bind(GameDefinitionRepository::class, fn() => new GameDefinitionRepositoryPhpConfig(
            new GameDefinitionFactoryPhpConfig()
        ));

        app()->bind(GameFactory::class, fn() => new GameFactoryEloquent(
            app()->make(GameDefinitionRepository::class),
            app()->make(GameDefinitionFactory::class)
        ));

        app()->bind(PlayerAnonymousFactory::class, fn() => new PlayerAnonymousFactoryEloquent(
            app()->make(PlayerAnonymousHashGenerator::class)
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
