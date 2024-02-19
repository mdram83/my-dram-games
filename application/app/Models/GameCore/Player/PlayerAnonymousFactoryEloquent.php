<?php

namespace App\Models\GameCore\Player;

class PlayerAnonymousFactoryEloquent implements PlayerAnonymousFactory
{
    public function __construct(
        protected PlayerAnonymousHashGenerator $generator
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
