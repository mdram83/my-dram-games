<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MyDramGames\Utils\Php\Collection\Collection;
use MyDramGames\Utils\Php\Collection\CollectionGeneric;
use MyDramGames\Utils\Player\PlayerCollection;
use MyDramGames\Utils\Player\PlayerCollectionGeneric;

class MyDramGamesUtilsServiceProvider extends ServiceProvider
{
    public $bindings = [
        Collection::class => CollectionGeneric::class,
        PlayerCollection::class => PlayerCollectionGeneric::class,
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
