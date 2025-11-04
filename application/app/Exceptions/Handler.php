<?php

namespace App\Exceptions;

use App\Http\Controllers\ControllerValidationException;
use App\Services\GamePlayDisconnection\GamePlayDisconnectException;
use App\Services\PremiumPass\PremiumPassException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\Exceptions\GameMoveException;
use MyDramGames\Core\Exceptions\GamePlayException;
use MyDramGames\Core\Exceptions\GamePlayStorageException;
use MyDramGames\Core\Exceptions\GameSetupException;
use MyDramGames\Utils\Exceptions\GameBoardException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    public const string MESSAGE_NOT_FOUND = 'Not found';

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

        $this->renderable(function (PremiumPassException $e) {
            return response()->view('errors.403', ['exception' => $e], SymfonyResponse::HTTP_FORBIDDEN);
        });

        $this->renderable(function (GameInviteException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        });

        $this->renderable(function (GamePlayStorageException|GamePlayDisconnectException $e) {
            return new Response(['message' => static::MESSAGE_NOT_FOUND], SymfonyResponse::HTTP_NOT_FOUND);
        });

        $this->renderable(function (ControllerValidationException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (GameSetupException|GameBoxException|GameMoveException|GameBoardException|GamePlayException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);
        });
    }
}
