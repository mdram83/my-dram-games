<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MyDramGamesCoreServiceProvider extends ServiceProvider
{
    public array $bindings = [
        \MyDramGames\Core\GameOption\GameOptionCollection::class => \MyDramGames\Core\GameOption\GameOptionCollectionPowered::class,
        \MyDramGames\Core\GameOption\GameOptionValueCollection::class => \MyDramGames\Core\GameOption\GameOptionValueCollectionPowered::class,
        \MyDramGames\Core\GameOption\GameOptionConfigurationCollection::class => \MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered::class,

        \MyDramGames\Core\GameBox\GameBoxCollection::class => \MyDramGames\Core\GameBox\GameBoxCollectionPowered::class,
        \MyDramGames\Core\GameBox\GameBoxRepository::class => \App\Extensions\Core\GameBox\GameBoxRepositoryPhpConfig::class,

        \MyDramGames\Core\GameSetup\GameSetupRepository::class => \MyDramGames\Core\GameSetup\GameSetupBaseRepository::class,

        \MyDramGames\Core\GamePlay\Services\GamePlayServicesProvider::class => \MyDramGames\Core\GamePlay\Services\GamePlayServicesProviderGeneric::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
