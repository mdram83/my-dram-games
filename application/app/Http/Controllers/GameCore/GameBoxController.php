<?php

namespace App\Http\Controllers\GameCore;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application as ContractsApplication;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use MyDramGames\Core\GameBox\GameBoxRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameBoxController extends Controller
{
    public function show(GameBoxRepository $repository, string $slug): View|Application|Factory|ContractsApplication|Response
    {
        try {
            $gameBox = $repository->getOne($slug)->toArray();
        } catch (Exception) {
            throw new NotFoundHttpException();
        }

        return view('single', ['gameBox' => $gameBox]);
    }
}
