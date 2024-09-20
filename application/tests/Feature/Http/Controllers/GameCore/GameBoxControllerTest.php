<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use Illuminate\Support\Facades\App;
use MyDramGames\Core\GameBox\GameBoxRepository;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameBoxControllerTest extends TestCase
{
    protected string $slug;

    public function setUp(): void
    {
        parent::setUp();
        $this->slug = App::make(GameBoxRepository::class)->getAll()->pullFirst()->getSlug();
    }

    public function testResponseOk(): void
    {
        $response = $this->get(route('games.show', $this->slug));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDataIsProvided(): void
    {
        $response = $this->get(route('games.show', $this->slug));

        $repository = $this->app->make(GameBoxRepository::class);
        $gameBox = $repository->getOne($this->slug)->toArray();
        $expectedData = ['gameBox' => $gameBox];

        $response->assertViewHasAll($expectedData);
    }
}
