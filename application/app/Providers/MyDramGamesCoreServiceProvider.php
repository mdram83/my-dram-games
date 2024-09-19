<?php

namespace App\Providers;

use App\GameCore\GamePlay\Generic\GamePlayServicesProviderGeneric;
use Illuminate\Support\ServiceProvider;
use MyDramGames\Core\GameOption\GameOptionCollection;
use MyDramGames\Core\GameOption\GameOptionCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionValueCollection;
use MyDramGames\Core\GameOption\GameOptionValueCollectionPowered;
use MyDramGames\Core\GamePlay\Services\GamePlayServicesProvider;

class MyDramGamesCoreServiceProvider extends ServiceProvider
{
    public $bindings = [
        GameOptionCollection::class => GameOptionCollectionPowered::class,
        GameOptionValueCollection::class => GameOptionValueCollectionPowered::class,
        GamePlayServicesProvider::class => GamePlayServicesProviderGeneric::class,
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
