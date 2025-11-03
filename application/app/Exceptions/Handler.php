<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\Exceptions\GameSetupException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (GameSetupException|GameBoxException $e) {
            return new Response(['message' => $e->getMessage()], 400);
        });

        $this->renderable(function (GameInviteException $e) {
            return new Response(['message' => $e->getMessage()], 500);
        });
    }
}
