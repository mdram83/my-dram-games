<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\GameCore\GameBox\GameBoxRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameBoxControllerTest extends TestCase
{
    protected string $slug;

    public function setUp(): void
    {
        parent::setUp();
        $this->slug = App::make(GameBoxRepository::class)->getAll()[0]->getSlug();
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
