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
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(GameDefinitionFactory::class, fn() => new GameDefinitionFactoryPhpConfig());

        app()->bind(
            GameDefinitionRepository::class,
            fn() => new GameDefinitionRepositoryPhpConfig(new GameDefinitionFactoryPhpConfig())
        );

        app()->bind(GameFactory::class, fn() => new GameFactoryEloquent(
            app()->make(GameDefinitionRepository::class),
            app()->make(GameDefinitionFactory::class)
        ));

        app()->bind(GameRepository::class, fn() => app()->make(GameRepositoryEloquent::class));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
