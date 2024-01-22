<?php

namespace App\Http\Controllers\GameDefinition;

use App\Http\Controllers\Controller;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

class GameDefinitionController extends Controller
{
    public function show(GameDefinitionRepository $repository, string $slug): View|Application|Factory|ContractsApplication|Response
    {
        try {
            $gameDefinition = $repository->getOne($slug)->toArray();
        } catch (Exception) {
            return new Response('Not found', 404);
        }

        return view('single', ['gameDefinition' => $gameDefinition]);
    }
}
