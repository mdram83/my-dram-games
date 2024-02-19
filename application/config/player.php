<?php

return [
    'playerHashCookieName' => env('VITE_PLAYER_HASH_COOKIE_NAME', 'anonymousPlayerHash'),
    'playerHashExpiration' => env('PLAYER_HASH_EXPIRATION', 2 * 60),
];
