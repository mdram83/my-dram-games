<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        \App\GameCore\Services\HashGenerator\HashGenerator::class => \App\GameCore\Services\HashGenerator\Md5\HashGeneratorMd5::class,
        \App\GameCore\GamePlayDisconnection\GamePlayDisconnectionFactory::class => \App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionFactoryEloquent::class,
        \App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository::class => \App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionRepositoryEloquent::class,

        \App\Extensions\Core\GameOption\GameOptionClassRepository::class => \App\Extensions\Core\GameOption\GameOptionClassRepositoryPhpConfig::class,
        \App\Extensions\Core\GameOption\GameOptionValueConverter::class => \App\Extensions\Core\GameOption\GameOptionValueConverterGeneric::class,
        \App\GameCore\Services\PremiumPass\PremiumPass::class => \App\GameCore\Services\PremiumPass\PremiumPassCore::class,
        \App\Extensions\Utils\Player\PlayerAnonymousRepository::class => \App\Extensions\Utils\Player\PlayerAnonymousRepositoryEloquent::class,
        \App\Extensions\Utils\Player\PlayerAnonymousFactory::class => \App\Extensions\Utils\Player\PlayerAnonymousFactoryEloquent::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Instantiated by PlayerMiddleware middleware */
        app()->bind(\MyDramGames\Utils\Player\Player::class, fn() => null);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
