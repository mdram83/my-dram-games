<?php

namespace App\Http\Controllers\GameCore;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Response;
use MyDramGames\Core\GameBox\GameBoxRepository;

class GameBoxAjaxController extends Controller
{
    public function index(GameBoxRepository $repository): Response
    {
        try {
            $content = array_map(fn($gameBox) => $gameBox->toArray(), $repository->getAll()->toArray());
            if (count($content) === 0) {
                throw new Exception();
            }
        } catch (Exception) {
            return new Response(static::MESSAGE_NOT_FOUND, 404);
        }

        return new Response(json_encode($content));
    }
}
