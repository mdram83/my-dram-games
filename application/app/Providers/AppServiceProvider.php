<?php

namespace App\Providers;

use App\GameCore\GameElements\GameDeck\PlayingCard\Generic\PlayingCardDealerGeneric;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardDeckProviderPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardFactoryPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitRepositoryPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardFactory;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuitRepository;
use App\GameCore\GameElements\GameMove\GameMoveAbsFactoryRepository;
use App\GameCore\GameElements\GameMove\PhpConfig\GameMoveAbsFactoryRepositoryPhpConfig;
use App\GameCore\GameInvite\Eloquent\GameInviteFactoryEloquent;
use App\GameCore\GameInvite\Eloquent\GameInviteRepositoryEloquent;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameInvite\GameInviteRepository;
use App\GameCore\GameBox\GameBoxRepository;
use App\GameCore\GameBox\PhpConfig\GameBoxRepositoryPhpConfig;
use App\GameCore\GameOption\GameOptionClassRepository;
use App\GameCore\GameOption\PhpConfig\GameOptionClassClassRepositoryPhpConfig;
use App\GameCore\GameOptionValue\GameOptionValueConverter;
use App\GameCore\GameOptionValue\GameOptionValueConverterEnum;
use App\GameCore\GamePlay\GamePlayAbsFactoryRepository;
use App\GameCore\GamePlay\GamePlayAbsRepository;
use App\GameCore\GamePlay\GamePlayRepository;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GamePlay\Generic\GamePlayRepositoryGeneric;
use App\GameCore\GamePlay\Generic\GamePlayServicesProviderGeneric;
use App\GameCore\GamePlay\PhpConfig\GamePlayAbsFactoryRepositoryPhpConfig;
use App\GameCore\GamePlay\PhpConfig\GamePlayAbsRepositoryPhpConfig;
use App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionFactoryEloquent;
use App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionRepositoryEloquent;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionFactory;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageEloquent;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageFactoryEloquent;
use App\GameCore\GamePlayStorage\Eloquent\GamePlayStorageRepositoryEloquent;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GamePlayStorage\GamePlayStorageFactory;
use App\GameCore\GamePlayStorage\GamePlayStorageRepository;
use App\GameCore\GameRecord\Eloquent\GameRecordFactoryEloquent;
use App\GameCore\GameRecord\Eloquent\GameRecordRepositoryEloquent;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\GameRecord\GameRecordRepository;
use App\GameCore\GameSetup\GameSetupAbsFactoryRepository;
use App\GameCore\GameSetup\PhpConfig\GameSetupAbsFactoryRepositoryPhpConfig;
use App\GameCore\Player\Eloquent\PlayerAnonymousFactoryEloquent;
use App\GameCore\Player\Eloquent\PlayerAnonymousRepositoryEloquent;
use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Player\PlayerAnonymousRepository;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\Laravel\CollectionLaravel;
use App\GameCore\Services\HashGenerator\Md5\HashGeneratorMd5;
use App\GameCore\Services\HashGenerator\HashGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Instantiated by PlayerMiddleware middleware */
        app()->bind(Player::class, fn() => null);

        app()->bind(HashGenerator::class, HashGeneratorMd5::class);
        app()->bind(Collection::class, CollectionLaravel::class);

        app()->bind(PlayerAnonymousRepository::class, PlayerAnonymousRepositoryEloquent::class);
        app()->bind(PlayerAnonymousFactory::class, PlayerAnonymousFactoryEloquent::class);

        app()->bind(GameBoxRepository::class, GameBoxRepositoryPhpConfig::class);

        app()->bind(GameInviteRepository::class, GameInviteRepositoryEloquent::class);
        app()->bind(GameInviteFactory::class, GameInviteFactoryEloquent::class);

        app()->bind(GameSetupAbsFactoryRepository::class, GameSetupAbsFactoryRepositoryPhpConfig::class);

        app()->bind(GameOptionClassRepository::class, GameOptionClassClassRepositoryPhpConfig::class);
        app()->bind(GameOptionValueConverter::class, GameOptionValueConverterEnum::class);

        app()->bind(GamePlayStorage::class, GamePlayStorageEloquent::class);
        app()->bind(GamePlayStorageRepository::class, GamePlayStorageRepositoryEloquent::class);
        app()->bind(GamePlayStorageFactory::class, GamePlayStorageFactoryEloquent::class);
        app()->bind(GamePlayAbsFactoryRepository::class, GamePlayAbsFactoryRepositoryPhpConfig::class);
        app()->bind(GamePlayRepository::class, GamePlayRepositoryGeneric::class);
        app()->bind(GamePlayAbsRepository::class, GamePlayAbsRepositoryPhpConfig::class);

        app()->bind(GameMoveAbsFactoryRepository::class, GameMoveAbsFactoryRepositoryPhpConfig::class);

        app()->bind(GameRecordFactory::class, GameRecordFactoryEloquent::class);
        app()->bind(GameRecordRepository::class, GameRecordRepositoryEloquent::class);

        app()->bind(GamePlayDisconnectionFactory::class, GamePlayDisconnectionFactoryEloquent::class);
        app()->bind(GamePlayDisconnectionRepository::class, GamePlayDisconnectionRepositoryEloquent::class);

        app()->bind(PlayingCardFactory::class, PlayingCardFactoryPhpEnum::class);
        app()->bind(PlayingCardDeckProvider::class, PlayingCardDeckProviderPhpEnum::class);
        app()->bind(PlayingCardSuitRepository::class, PlayingCardSuitRepositoryPhpEnum::class);
        app()->bind(PlayingCardDealer::class, PlayingCardDealerGeneric::class);

        app()->bind(GamePlayServicesProvider::class, GamePlayServicesProviderGeneric::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
