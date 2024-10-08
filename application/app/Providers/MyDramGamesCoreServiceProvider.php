<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MyDramGamesCoreServiceProvider extends ServiceProvider
{
    public array $bindings = [

        \MyDramGames\Core\GameOption\GameOptionCollection::class => \MyDramGames\Core\GameOption\GameOptionCollectionPowered::class,
        \MyDramGames\Core\GameOption\GameOptionValueCollection::class => \MyDramGames\Core\GameOption\GameOptionValueCollectionPowered::class,
        \MyDramGames\Core\GameOption\GameOptionConfiguration::class => \MyDramGames\Core\GameOption\GameOptionConfigurationGeneric::class,
        \MyDramGames\Core\GameOption\GameOptionConfigurationCollection::class => \MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered::class,

        \MyDramGames\Core\GameSetup\GameSetupRepository::class => \MyDramGames\Core\GameSetup\GameSetupBaseRepository::class,

        \MyDramGames\Core\GameBox\GameBoxCollection::class => \MyDramGames\Core\GameBox\GameBoxCollectionPowered::class,
        \MyDramGames\Core\GameBox\GameBoxRepository::class => \App\Extensions\Core\GameBox\GameBoxRepositoryPhpConfig::class,

        \MyDramGames\Core\GameInvite\GameInviteRepository::class => \App\Extensions\Core\GameInvite\GameInviteRepositoryEloquent::class,
        \MyDramGames\Core\GameInvite\GameInviteFactory::class => \App\Extensions\Core\GameInvite\GameInviteFactoryEloquent::class,

        \MyDramGames\Core\GamePlay\GamePlayRepository::class => \App\Extensions\Core\GamePlay\GamePlayStorableRepository::class,
        \MyDramGames\Core\GamePlay\GamePlayFactory::class => \MyDramGames\Core\GamePlay\GamePlayFactoryStorable::class,

        \MyDramGames\Core\GamePlay\Services\GamePlayServicesProvider::class => \MyDramGames\Core\GamePlay\Services\GamePlayServicesProviderGeneric::class,

        \MyDramGames\Core\GamePlay\Storage\GamePlayStorage::class => \App\Extensions\Core\GamePlay\Storage\GamePlayStorageEloquent::class,
        \MyDramGames\Core\GamePlay\Storage\GamePlayStorageFactory::class => \App\Extensions\Core\GamePlay\Storage\GamePlayStorageFactoryEloquent::class,
        \MyDramGames\Core\GamePlay\Storage\GamePlayStorageRepository::class => \App\Extensions\Core\GamePlay\Storage\GamePlayStorageRepositoryEloquent::class,

        \MyDramGames\Core\GameRecord\GameRecordCollection::class => \MyDramGames\Core\GameRecord\GameRecordCollectionPowered::class,
        \MyDramGames\Core\GameRecord\GameRecordFactory::class => \App\Extensions\Core\GameRecord\GameRecordFactoryEloquent::class,
        \MyDramGames\Core\GameRecord\GameRecordRepository::class => \App\Extensions\Core\GameRecord\GameRecordRepositoryEloquent::class,
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
