<?php

namespace Tests\Feature\Http\Controllers\GameCore;

use App\GameCore\GameBox\PhpConfig\GameBoxPhpConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameBoxAjaxControllerTest extends TestCase
{
    protected array $configData;

    protected function prepareExpectedResponseContent(): array
    {
        $responseContent = [];
        $this->configData = Config::get('games');

        foreach ($this->configData['box'] as $slug => $parameters) {
            $parameters['numberOfPlayersDescription'] = (new GameBoxPhpConfig($slug))
                ->getNumberOfPlayersDescription();
            $responseContent[] = array_merge(['slug' => $slug], $parameters);
        }
        return $responseContent;
    }

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
        $expectedContent = $this->prepareExpectedResponseContent();

        $response->assertJsonCount(count($this->configData['box']));
        $response->assertExactJson($expectedContent);
    }
}
