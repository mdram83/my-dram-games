<?php

namespace App\Providers;

use App\Extensions\Utils\Php\Collection\CollectionEngineLaravel;
use Illuminate\Support\ServiceProvider;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\PlayerCollection;
use MyDramGames\Utils\Player\PlayerCollectionPowered;

class MyDramGamesUtilsServiceProvider extends ServiceProvider
{
    public $bindings = [
        CollectionEngine::class => CollectionEngineLaravel::class,
        PlayerCollection::class => PlayerCollectionPowered::class,
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
