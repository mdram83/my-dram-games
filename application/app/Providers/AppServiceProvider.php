<?php

namespace App\Providers;

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
        app()->bind(
            GameDefinitionRepository::class,
            fn() => new GameDefinitionRepositoryPhpConfig(new GameDefinitionFactoryPhpConfig())
        );
        app()->bind(GameDefinitionFactory::class, fn() => new GameDefinitionFactoryPhpConfig());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
