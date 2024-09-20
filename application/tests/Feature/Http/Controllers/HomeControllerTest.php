<?php

namespace Tests\Feature\Http\Controllers;

use MyDramGames\Core\GameBox\GameBoxRepository;
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

        $expectedData = ['gameBoxList' => array_map(
            fn($gameDefinition) => $gameDefinition->toArray(),
            $this->app->make(GameBoxRepository::class)->getAll()->toArray()
        )];

        $response->assertViewHasAll($expectedData);
    }
}
