<?php

namespace Tests\Feature\Http\Controllers\GameDefinition;

use App\Models\GameCore\GameDefinition\GameDefinitionPhpConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GameDefinitionAjaxControllerTest extends TestCase
{
    protected array $configData;

    protected function prepareExpectedResponseContent(): array
    {
        $responseContent = [];
        $this->configData = Config::get('games');

        foreach ($this->configData['gameDefinition'] as $slug => $parameters) {
            $parameters['numberOfPlayersDescription'] = (new GameDefinitionPhpConfig($slug))
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

        $response->assertJsonCount(count($this->configData['gameDefinition']));
        $response->assertExactJson($expectedContent);
    }
}
