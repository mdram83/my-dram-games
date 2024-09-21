<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        \App\GameCore\Services\HashGenerator\HashGenerator::class => \App\GameCore\Services\HashGenerator\Md5\HashGeneratorMd5::class,
        \App\GameCore\GamePlayDisconnection\GamePlayDisconnectionFactory::class => \App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionFactoryEloquent::class,
        \App\GameCore\GamePlayDisconnection\GamePlayDisconnectionRepository::class => \App\GameCore\GamePlayDisconnection\Eloquent\GamePlayDisconnectionRepositoryEloquent::class,

        \App\GameCore\GameOption\GameOptionClassRepository::class => \App\GameCore\GameOption\PhpConfig\GameOptionClassRepositoryPhpConfig::class,
        \App\GameCore\GameOptionValue\GameOptionValueConverter::class => \App\GameCore\GameOptionValue\GameOptionValueConverterGeneric::class,
        \App\GameCore\Services\PremiumPass\PremiumPass::class => \App\GameCore\Services\PremiumPass\PremiumPassCore::class,
        \App\GameCore\Player\PlayerAnonymousRepository::class => \App\GameCore\Player\Eloquent\PlayerAnonymousRepositoryEloquent::class,
        \App\GameCore\Player\PlayerAnonymousFactory::class => \App\GameCore\Player\Eloquent\PlayerAnonymousFactoryEloquent::class,
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
