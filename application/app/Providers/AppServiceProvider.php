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
use App\Models\GameCore\Player\PlayerAnonymousIdGenerator;
use App\Models\GameCore\Player\PlayerAnonymousIdGeneratorMd5;
use App\Models\GameCore\Player\PlayerRepository;
use App\Models\GameCore\Player\PlayerRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(GameDefinitionFactory::class, fn() => new GameDefinitionFactoryPhpConfig());
        app()->bind(PlayerAnonymousIdGenerator::class, fn() => new PlayerAnonymousIdGeneratorMd5());
        app()->bind(GameRepository::class, fn() => app()->make(GameRepositoryEloquent::class));

        app()->bind(
            GameDefinitionRepository::class,
            fn() => new GameDefinitionRepositoryPhpConfig(new GameDefinitionFactoryPhpConfig())
        );

        app()->bind(GameFactory::class, fn() => new GameFactoryEloquent(
            app()->make(GameDefinitionRepository::class),
            app()->make(GameDefinitionFactory::class)
        ));

        app()->bind(PlayerRepository::class, fn() => new PlayerRepositoryEloquent(
            app()->make(PlayerAnonymousIdGenerator::class)
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
