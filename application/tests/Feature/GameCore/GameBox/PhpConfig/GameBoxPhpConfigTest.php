<?php

namespace Tests\Feature\GameCore\GameBox\PhpConfig;

use App\GameCore\GameBox\GameBox;
use App\GameCore\GameBox\GameBoxException;
use App\GameCore\GameBox\PhpConfig\GameBoxPhpConfig;
use App\GameCore\GameOption\GameOptionNumberOfPlayers;
use App\GameCore\GameOptionType\GameOptionTypeEnum;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameSetup\GameSetup;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\GameSetupTicTacToe;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GameBoxPhpConfigTest extends TestCase
{
    protected string $slug = 'tic-tac-toe';
    protected array $box;
    protected GameSetup $setup;
    protected GameBoxPhpConfig $gameBox;

    public function setUp(): void
    {
        parent::setUp();
        $this->box = Config::get('games.box.' . $this->slug);
        $this->setup = new GameSetupTicTacToe(App::make(Collection::class));
        $this->gameBox = new GameBoxPhpConfig($this->slug, $this->setup);
    }

    protected function mockConfigFacade(?array $box = []): void
    {
        if ($box === []) {
            $box = $this->box;
        }

        Config::shouldReceive('get')
            ->once()
            ->with('games.box.' . $this->slug )
            ->andReturn($box);
    }

    protected function getMockGameSetup(array $numberOfPlayersValues): GameSetup
    {
        $option = new GameOptionNumberOfPlayers($numberOfPlayersValues, $numberOfPlayersValues[0]);
        $setup = $this->createMock(GameSetup::class);
        $setup->method('getNumberOfPlayers')->willReturn($option);
        return $setup;
    }

    public function testGameDefinitionCreated(): void
    {
        $this->assertInstanceOf(GameBox::class, $this->gameBox);
    }

    public function testThrowExceptionIfNoSlugInConfig(): void
    {
        $this->expectException(GameBoxException::class);
        new GameBoxPhpConfig('definitely-missing-slug-1', $this->setup);
    }

    public function testThrowExceptionIfNoNameInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $box = $this->box;
        unset($box['name']);

        $this->mockConfigFacade($box);
        new GameBoxPhpConfig($this->slug, $this->setup);
    }

    public function testThrowExceptionIfNoIsActiveInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $box = $this->box;
        unset($box['isActive']);

        $this->mockConfigFacade($box);
        new GameBoxPhpConfig($this->slug, $this->setup);
    }

    public function testGetName(): void
    {
        $this->assertEquals($this->box['name'], $this->gameBox->getName());
    }

    public function testGetSlug(): void
    {
        $this->assertEquals($this->slug, $this->gameBox->getSlug());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals($this->box['description'], $this->gameBox->getDescription());
    }

    public function testGetNumberOfPlayersDescriptionWithOneNumber(): void
    {
        $this->assertEquals('2', $this->gameBox->getNumberOfPlayersDescription());
    }

    public function testGetNumberOfPlayersDescriptionWithConsecutiveNumbers(): void
    {
        $setup = $this->getMockGameSetup([
            GameOptionValueNumberOfPlayers::Players002,
            GameOptionValueNumberOfPlayers::Players003,
            GameOptionValueNumberOfPlayers::Players004,
        ]);
        $gameBox = new GameBoxPhpConfig($this->slug, $setup);

        $this->assertEquals('2-4', $gameBox->getNumberOfPlayersDescription());
    }

    public function testGetNumberOfPlayersDescriptionWithNonConsecutiveNumbers(): void
    {
        $setup = $this->getMockGameSetup([
            GameOptionValueNumberOfPlayers::Players002,
            GameOptionValueNumberOfPlayers::Players004,
            GameOptionValueNumberOfPlayers::Players006,
        ]);
        $gameBox = new GameBoxPhpConfig($this->slug, $setup);

        $this->assertEquals('2, 4, 6', $gameBox->getNumberOfPlayersDescription());
    }

    public function testGetDurationInMinutes(): void
    {
        $this->assertEquals($this->box['durationInMinutes'], $this->gameBox->getDurationInMinutes());
    }

    public function testGetMinPlayerAge(): void
    {
        $this->assertEquals($this->box['minPlayerAge'], $this->gameBox->getMinPlayerAge());
    }

    public function testGetIsActive(): void
    {
        $this->assertEquals($this->box['isActive'], $this->gameBox->isActive());
    }

    public function testGetGameSetup(): void
    {
        $this->assertSame($this->setup, $this->gameBox->getGameSetup());
    }

    public function testToArray(): void
    {
        $expected = array_merge(
            [
                'slug' => $this->slug,
                'name' => $this->box['name'],
                'description' => $this->box['description'],
                'durationInMinutes' => $this->box['durationInMinutes'],
                'minPlayerAge' => $this->box['minPlayerAge'],
                'isActive' => $this->box['isActive'],
            ],
            ['numberOfPlayersDescription' => '2'],
            ['options' =>
                [
                    'numberOfPlayers' => [
                        'availableValues' => [2],
                        'defaultValue' => 2,
                        'type' => GameOptionTypeEnum::Radio,
                        'name' => $this->gameBox->getGameSetup()->getOption('numberOfPlayers')->getName(),
                        'description' => $this->gameBox->getGameSetup()->getOption('numberOfPlayers')->getDescription(),
                    ],
                    'autostart' => [
                        'availableValues' => [1, 0],
                        'defaultValue' => 0,
                        'type' => GameOptionTypeEnum::Checkbox,
                        'name' => $this->gameBox->getGameSetup()->getOption('autostart')->getName(),
                        'description' => $this->gameBox->getGameSetup()->getOption('autostart')->getDescription(),
                    ],
                    'forfeitAfter' => [
                        'availableValues' => [0, 60],
                        'defaultValue' => 0,
                        'type' => GameOptionTypeEnum::Radio,
                        'name' => $this->gameBox->getGameSetup()->getOption('forfeitAfter')->getName(),
                        'description' => $this->gameBox->getGameSetup()->getOption('forfeitAfter')->getDescription(),
                    ],
                ],
            ],
        );

        $this->assertEquals($expected, $this->gameBox->toArray());
    }
}
