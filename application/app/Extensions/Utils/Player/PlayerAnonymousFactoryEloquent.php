<?php

namespace App\Extensions\Utils\Player;

use App\GameCore\Services\HashGenerator\HashGenerator;
use App\Models\PlayerAnonymousEloquent;
use MyDramGames\Utils\Player\PlayerAnonymous;

class PlayerAnonymousFactoryEloquent implements PlayerAnonymousFactory
{
    public function __construct(
        protected HashGenerator $generator
    )
    {

    }

    /**
     * @throws \App\Extensions\Utils\Player\PlayerAnonymousFactoryException
     */
    public function create(array $attributes = []): PlayerAnonymous
    {
        if (!isset($attributes['key']) || $attributes['key'] === '') {
            throw new PlayerAnonymousFactoryException(PlayerAnonymousFactoryException::MESSAGE_WRONG_ATTRIBUTES);
        }

        return PlayerAnonymousEloquent::factory()->create([
            'hash' => $this->generator->generateHash($attributes['key'])
        ]);
    }
}
