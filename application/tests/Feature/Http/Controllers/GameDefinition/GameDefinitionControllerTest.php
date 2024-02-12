<?php

namespace Tests\Feature\Http\Controllers\GameDefinition;

use App\Models\GameCore\GameDefinition\GameDefinitionRepository;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameDefinitionControllerTest extends TestCase
{
    protected string $slug;

    public function setUp(): void
    {
        parent::setUp();
        $gameDefinitionData = Config::get('games');
        $this->slug = array_keys($gameDefinitionData['gameDefinition'])[0];
    }

    public function testResponseOk(): void
    {
        $response = $this->get(route('games.show', $this->slug));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDataIsProvided(): void
    {
        $response = $this->get(route('games.show', $this->slug));

        $repository = $this->app->make(GameDefinitionRepository::class);
        $gameDefinition = $repository->getOne($this->slug)->toArray();
        $expectedData = ['gameDefinition' => $gameDefinition];

        $response->assertViewHasAll($expectedData);
    }
}
