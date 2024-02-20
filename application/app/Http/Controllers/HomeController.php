<?php

namespace App\Http\Controllers;

use App\GameCore\GameBox\GameBoxRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(GameBoxRepository $repository): View|Application|Factory|ContractsApplication
    {
        try {
            $gameDefinitionData = array_map(fn($gameDefinition) => $gameDefinition->toArray(), $repository->getAll());
        } catch (Exception) {

        }

        return view('home', ['gameDefinitionData' => $gameDefinitionData ?? []]);
    }
}
