<?php

namespace App\Providers;

use App\GameCore\GameElements\GameDeck\PlayingCard\Generic\PlayingCardDealerGeneric;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardDeckProviderPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardFactoryPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardRankRepositoryPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PhpEnum\PlayingCardSuitRepositoryPhpEnum;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardFactory;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRankRepository;
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
use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Player\PlayerAnonymousRepository;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\Laravel\CollectionLaravel;
use App\GameCore\Services\PremiumPass\Basic\PremiumPassBasic;
use App\GameCore\Services\PremiumPass\PremiumPass;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        \App\GameCore\Services\HashGenerator\HashGenerator::class => \App\GameCore\Services\HashGenerator\Md5\HashGeneratorMd5::class,
        \App\GameCore\GamePlayDisconnection\GamePlayDisconnectionFactory::class => \App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionFactoryEloquent::class,
        \App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository::class => \App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionRepositoryEloquent::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Instantiated by PlayerMiddleware middleware */
        app()->bind(\MyDramGames\Utils\Player\Player::class, fn() => null);

        // TODO cleanup -> below elements will potentially be not required
        app()->bind(Collection::class, CollectionLaravel::class);
        app()->bind(GameSetupAbsFactoryRepository::class, GameSetupAbsFactoryRepositoryPhpConfig::class);
        app()->bind(PlayingCardDeckProvider::class, PlayingCardDeckProviderPhpEnum::class);
        app()->bind(PlayingCardSuitRepository::class, PlayingCardSuitRepositoryPhpEnum::class);
        app()->bind(PlayingCardRankRepository::class, PlayingCardRankRepositoryPhpEnum::class);
        app()->bind(PlayingCardDealer::class, PlayingCardDealerGeneric::class);
        app()->bind(GamePlayServicesProvider::class, GamePlayServicesProviderGeneric::class);
        app()->bind(GameBoxRepository::class, GameBoxRepositoryPhpConfig::class);

        // TODO replace -> replace with new implementation
        app()->bind(PremiumPass::class, PremiumPassBasic::class); // replace with PremiumPassCore::class (then remove basic)
        app()->bind(GameInviteRepository::class, GameInviteRepositoryEloquent::class); // replace with same but Extensions...
        app()->bind(GameInviteFactory::class, GameInviteFactoryEloquent::class); // replace with same but Extensions...
        app()->bind(GamePlayStorage::class, GamePlayStorageEloquent::class); // replace with same but Extensions...

        // TODO rewrite? -> not clear if below elements requires rewrite or cleanup or will not be required
        app()->bind(PlayerAnonymousRepository::class, PlayerAnonymousRepositoryEloquent::class);
        app()->bind(PlayerAnonymousFactory::class, PlayerAnonymousFactoryEloquent::class);
        app()->bind(GameOptionClassRepository::class, GameOptionClassClassRepositoryPhpConfig::class);
        app()->bind(GameOptionValueConverter::class, GameOptionValueConverterEnum::class);
        app()->bind(GamePlayAbsFactoryRepository::class, GamePlayAbsFactoryRepositoryPhpConfig::class);
        app()->bind(GamePlayAbsRepository::class, GamePlayAbsRepositoryPhpConfig::class);
        app()->bind(GameMoveAbsFactoryRepository::class, GameMoveAbsFactoryRepositoryPhpConfig::class);
        app()->bind(GameRecordRepository::class, GameRecordRepositoryEloquent::class);
        app()->bind(PlayingCardFactory::class, PlayingCardFactoryPhpEnum::class);

        // TODO rewrite -> below elements requires rewrite to adjust to library interfaces
        app()->bind(GamePlayStorageRepository::class, GamePlayStorageRepositoryEloquent::class);
        app()->bind(GamePlayStorageFactory::class, GamePlayStorageFactoryEloquent::class);
        app()->bind(GamePlayRepository::class, GamePlayRepositoryGeneric::class);
        app()->bind(GameRecordFactory::class, GameRecordFactoryEloquent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
