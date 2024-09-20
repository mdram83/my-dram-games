<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use MyDramGames\Core\GameBox\GameBoxRepository;

class HomeController extends Controller
{
    public function __invoke(GameBoxRepository $repository): View|Application|Factory|ContractsApplication
    {
        try {
            $responseContent = array_map(fn($gameBox) => $gameBox->toArray(), $repository->getAll()->toArray());
        } catch (Exception) {

        }

        return view('home', ['gameBoxList' => $responseContent ?? []]);
    }
}
