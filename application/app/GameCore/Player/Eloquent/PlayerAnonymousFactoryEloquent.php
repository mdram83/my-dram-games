<?php

namespace App\GameCore\Player\Eloquent;

use App\GameCore\Player\PlayerAnonymous;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Player\PlayerAnonymousFactoryException;
use App\GameCore\Services\HashGenerator\HashGenerator;
use App\Models\PlayerAnonymousEloquent;

class PlayerAnonymousFactoryEloquent implements PlayerAnonymousFactory
{
    public function __construct(
        protected HashGenerator $generator
    )
    {

    }

    /**
     * @throws PlayerAnonymousFactoryException
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
