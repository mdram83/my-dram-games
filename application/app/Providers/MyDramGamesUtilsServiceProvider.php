<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MyDramGamesUtilsServiceProvider extends ServiceProvider
{
    public array $bindings = [
        \MyDramGames\Utils\Php\Collection\Collection::class => \MyDramGames\Utils\Php\Collection\CollectionPoweredExtendable::class,
        \MyDramGames\Utils\Php\Collection\CollectionEngine::class => \MyDramGames\Utils\Php\Collection\CollectionEnginePhpArray::class,
        \MyDramGames\Utils\Player\PlayerCollection::class => \MyDramGames\Utils\Player\PlayerCollectionPowered::class,
        \MyDramGames\Utils\Decks\PlayingCard\Support\PlayingCardDealer::class => \MyDramGames\Utils\Decks\PlayingCard\Generic\PlayingCardDealerGeneric::class,
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
