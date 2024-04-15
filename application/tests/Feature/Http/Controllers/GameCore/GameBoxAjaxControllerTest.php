<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\GameCore\GameBox\PhpConfig\GameBoxRepositoryPhpConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameBoxAjaxControllerTest extends TestCase
{
    protected function getResponse(): TestResponse
    {
        return $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get(route('ajax.games.index'));
    }

    public function testNonAjaxRequestResponseUnauthorized(): void
    {
        $response = $this->get(route('ajax.games.index'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAjaxRequestResponseStatusOk(): void
    {
        $response = $this->getResponse();
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testIndexResponseWithJson(): void
    {
        $response = $this->getResponse();
        $repository = App::make(GameBoxRepositoryPhpConfig::class);
        $expectedContent = array_map(fn ($gameBox) => $gameBox->toArray(), $repository->getAll());

        $response->assertJsonCount(count($expectedContent));
        $response->assertExactJson($expectedContent);
    }
}
