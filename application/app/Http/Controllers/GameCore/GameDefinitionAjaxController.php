<?php

namespace App\Http\Controllers\GameCore;

use App\GameCore\GameDefinition\GameDefinitionRepository;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Response;

class GameDefinitionAjaxController extends Controller
{
    public function index(GameDefinitionRepository $repository): Response
    {
        try {
            $content = array_map(fn($gameDefinition) => $gameDefinition->toArray(), $repository->getAll());
            if (count($content) === 0) {
                throw new Exception();
            }
        } catch (Exception) {
            return new Response(static::MESSAGE_NOT_FOUND, 404);
        }

        return new Response(json_encode($content));
    }
}
