<?php

namespace App\Providers;

use App\Extensions\Core\GameIndex\GameIndexRepositoryPhpClass;
use App\Extensions\Core\GamePlay\Storage\GamePlayStorageEloq;
use App\Extensions\Core\GamePlay\Storage\GamePlayStorageFactoryEloq;
use App\GameCore\GamePlay\Generic\GamePlayServicesProviderGeneric;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageFactoryEloquent;
use Illuminate\Support\ServiceProvider;
use MyDramGames\Core\GameIndex\GameIndexRepository;
use MyDramGames\Core\GameOption\GameOptionCollection;
use MyDramGames\Core\GameOption\GameOptionCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionValueCollection;
use MyDramGames\Core\GameOption\GameOptionValueCollectionPowered;
use MyDramGames\Core\GamePlay\Services\GamePlayServicesProvider;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorage;
use MyDramGames\Core\GamePlay\Storage\GamePlayStorageFactory;

class MyDramGamesCoreServiceProvider extends ServiceProvider
{
    public $bindings = [
        GameIndexRepository::class => GameIndexRepositoryPhpClass::class,
        GameOptionCollection::class => GameOptionCollectionPowered::class,
        GameOptionValueCollection::class => GameOptionValueCollectionPowered::class,
        GamePlayStorage::class => GamePlayStorageEloq::class,
        GamePlayStorageFactory::class => GamePlayStorageFactoryEloq::class,
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