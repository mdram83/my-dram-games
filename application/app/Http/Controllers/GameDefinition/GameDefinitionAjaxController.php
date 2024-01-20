<?php

namespace App\Http\Controllers\GameDefinition;

use App\Http\Controllers\Controller;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
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
            return new Response('Not found', 404);
        }

        return new Response(json_encode($content));
    }
}
