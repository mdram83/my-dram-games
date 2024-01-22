<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\GameCore\GameDefinition\GameDefinitionPhpConfig;
use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    public function testResponseOk(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testResponseHasGameDefinitionData(): void
    {
        $response = $this->get(route('home'));

        $repository = $this->app->make(GameDefinitionRepository::class);
        $gameDefinitionData = array_map(fn($gameDefinition) => $gameDefinition->toArray(), $repository->getAll());
        $expectedData = ['gameDefinitionData' => $gameDefinitionData];

        $response->assertViewHasAll($expectedData);
    }
}
