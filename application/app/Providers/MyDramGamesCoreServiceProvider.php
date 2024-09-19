<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MyDramGamesCoreServiceProvider extends ServiceProvider
{
    public array $bindings = [
        \MyDramGames\Core\GameOption\GameOptionCollection::class => \MyDramGames\Core\GameOption\GameOptionCollectionPowered::class,
        \MyDramGames\Core\GameOption\GameOptionValueCollection::class => \MyDramGames\Core\GameOption\GameOptionValueCollectionPowered::class,
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
