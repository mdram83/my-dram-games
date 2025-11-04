<?php

namespace App\Http\Controllers\GameCore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use MyDramGames\Core\GameBox\GameBoxRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameBoxAjaxController extends Controller
{
    public function index(GameBoxRepository $repository): Response
    {
        $content = array_map(fn($gameBox) => $gameBox->toArray(), $repository->getAll()->toArray());

        if (count($content) === 0) {
            throw new NotFoundHttpException();
        }

        return new Response(json_encode($content));
    }
}
