<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [

        \App\Services\HashGenerator\HashGenerator::class => \App\Services\HashGenerator\Md5\HashGeneratorMd5::class,
        \App\Services\PremiumPass\PremiumPass::class => \App\Services\PremiumPass\PremiumPassCore::class,
        \App\Services\GamePlayDisconnection\GamePlayDisconnectionFactory::class => \App\Services\GamePlayDisconnection\Eloquent\GamePlayDisconnectionFactoryEloquent::class,
        \App\Services\GamePlayDisconnection\GamePlayDisconnectionRepository::class => \App\Services\GamePlayDisconnection\Eloquent\GamePlayDisconnectionRepositoryEloquent::class,

        \App\Extensions\Core\GameOption\GameOptionClassRepository::class => \App\Extensions\Core\GameOption\GameOptionClassRepositoryPhpConfig::class,
        \App\Extensions\Core\GameOption\GameOptionValueConverter::class => \App\Extensions\Core\GameOption\GameOptionValueConverterGeneric::class,
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
