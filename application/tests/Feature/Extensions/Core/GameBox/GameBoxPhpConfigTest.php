<?php

namespace Tests\Feature\Extensions\Core\GameBox;

use App\Extensions\Core\GameBox\GameBoxPhpConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\GameBox\GameBox;
use MyDramGames\Core\GameOption\GameOption;
use MyDramGames\Core\GameOption\GameOptionCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionTypeGeneric;
use MyDramGames\Core\GameOption\GameOptionValueCollectionPowered;
use MyDramGames\Core\GameOption\Options\GameOptionNumberOfPlayersGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GamePlay\GamePlayStorableBase;
use MyDramGames\Core\GameSetup\GameSetup;
use MyDramGames\Core\GameSetup\GameSetupRepository;
use Tests\TestCase;
use Tests\TestingHelpers\GameMoveFactoryTestingStub;
use Tests\TestingHelpers\GameSetupBaseTestingStub;

class GameBoxPhpConfigTest extends TestCase
{
    protected string $slug = 'tic-tac-toe';
    protected string $missingSlug = 'missing-games-slug';
    protected string $premiumSlug = 'netrunners';
    protected array $box;
    protected GameOption $option;
    protected GameSetup $setup;
    protected GameSetupRepository $setupRepository;
    protected GameBoxPhpConfig $gameBox;

    public function setUp(): void
    {
        parent::setUp();

        $this->box = [
            'name' => 'Tic Tac Toe',
            'description' => 'Famous Tic Tac Toe game that you can now play with friends online!',
            'durationInMinutes' => 1,
            'minPlayerAge' => 4,
            'isActive' => true,
            'isPremium' => false,
            'gameSetupClassname' => GameSetupBaseTestingStub::class,
            'gamePlayClassname' => GamePlayStorableBase::class,
            'gameMoveFactoryClassname' => GameMoveFactoryTestingStub::class,
        ];

        $this->option = $this->getGameOption();
        $this->setup = $this->getMockGameSetup();
        $this->setupRepository = App::make(GameSetupRepository::class);

        $this->mockConfigFacade();
        $this->gameBox = new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);
    }

    protected function mockConfigFacade(?array $box = [], bool $missingSlug = false): void
    {
        if ($box === []) {
            $box = $this->box;
        }

        if (!$missingSlug) {
            Config::shouldReceive('get')
                ->once()
                ->with('games.box.' . $this->slug)
                ->andReturn($box);
        } else {
            Config::shouldReceive('get')
                ->once()
                ->with('games.box.' . $this->missingSlug)
                ->andReturn(false);
        }
    }

    protected function getMockGameSetup(array $numberOfPlayersValues = [2]): GameSetup
    {
        $option = $this->getGameOption($numberOfPlayersValues);

        $setup = $this->createMock(GameSetup::class);
        $setup->method('getNumberOfPlayers')->willReturn($option);
        $setup->method('getAllOptions')->willReturn(new GameOptionCollectionPowered(null, [$option]));

        return $setup;
    }

    protected function getGameOption(array $numberOfPlayersValues = [2]): GameOptionNumberOfPlayersGeneric
    {
        $availableValues = new GameOptionValueCollectionPowered();
        foreach ($numberOfPlayersValues as $valueInt) {
            $availableValues->add(GameOptionValueNumberOfPlayersGeneric::fromValue($valueInt));
        }

        return new GameOptionNumberOfPlayersGeneric(
            $availableValues,
            GameOptionValueNumberOfPlayersGeneric::fromValue($numberOfPlayersValues[0]),
            GameOptionTypeGeneric::fromValue('radio')
        );
    }

    public function testGameDefinitionCreated(): void
    {
        $this->assertInstanceOf(GameBox::class, $this->gameBox);
    }

    public function testThrowExceptionIfNoSlugInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $this->mockConfigFacade(null, true);
        new GameBoxPhpConfig($this->missingSlug, $this->setupRepository, $this->setup);
    }

    public function testThrowExceptionIfNoNameInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $box = $this->box;
        unset($box['name']);

        $this->mockConfigFacade($box);
        new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);
    }

    public function testThrowExceptionIfNoIsActiveInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $box = $this->box;
        unset($box['isActive']);

        $this->mockConfigFacade($box);
        new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);
    }

    public function testThrowExceptionIfNoIsPremiumInConfig(): void
    {
        $this->expectException(GameBoxException::class);

        $box = $this->box;
        unset($box['isPremium']);

        $this->mockConfigFacade($box);
        new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);
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
        $setup = $this->getMockGameSetup([2, 3, 4]);
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->setupRepository, $setup);

        $this->assertEquals('2-4', $gameBox->getNumberOfPlayersDescription());
    }

    public function testGetNumberOfPlayersDescriptionWithNonConsecutiveNumbers(): void
    {
        $setup = $this->getMockGameSetup([2, 4, 6]);
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->setupRepository, $setup);

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
                'isPremium' => $this->box['isPremium'],
            ],
            ['numberOfPlayersDescription' => '2'],
            ['options' =>
                [
                    'numberOfPlayers' => [
                        'availableValues' => [['label' => '2 Players', 'value' => 2]],
                        'defaultValue' => 2,
                        'type' => $this->option->getType()->getValue(),
                        'name' => $this->option->getName(),
                        'description' => $this->option->getDescription(),
                    ],
                ],
            ],
        );

        $this->assertEquals($expected, $this->gameBox->toArray());
    }

    public function testIsPremiumOnNonPremiumGame(): void
    {
        $this->assertFalse($this->gameBox->isPremium());
    }

    public function testIsPremiumOnPremiumGame(): void
    {
        $this->box['isPremium'] = true;
        $this->mockConfigFacade();
        $this->gameBox = new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);

        $this->assertTrue($this->gameBox->isPremium());
    }

    public function testGetGamePlayClassnameThrowExceptionWhenNotFollowingInterface(): void
    {
        $this->expectException(GameBoxException::class);
        $this->expectExceptionMessage(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);

        $this->box['gamePlayClassname'] = GameBoxException::class;
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);
        $gameBox->getGamePlayClassname();
    }

    public function testGetGamePlayClassnameThrowExceptionWhenEmpty(): void
    {
        $this->expectException(GameBoxException::class);
        $this->expectExceptionMessage(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);

        $this->box['gamePlayClassname'] = '';
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);
        $gameBox->getGamePlayClassname();
    }

    public function testGetGamePlayClassname(): void
    {
        $this->assertEquals($this->box['gamePlayClassname'], $this->gameBox->getGamePlayClassname());
    }

    public function testGetGameMoveFactoryClassnameThrowExceptionWhenNotFollowingInterface(): void
    {
        $this->expectException(GameBoxException::class);
        $this->expectExceptionMessage(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);

        $this->box['gameMoveFactoryClassname'] = GameBoxException::class;
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);
        $gameBox->getGameMoveFactoryClassname();
    }

    public function testGetGameMoveFactoryClassnameThrowExceptionWhenEmpty(): void
    {
        $this->expectException(GameBoxException::class);
        $this->expectExceptionMessage(GameBoxException::MESSAGE_INCORRECT_CONFIGURATION);

        $this->box['gameMoveFactoryClassname'] = '';
        $this->mockConfigFacade();
        $gameBox = new GameBoxPhpConfig($this->slug, $this->setupRepository, $this->setup);
        $gameBox->getGameMoveFactoryClassname();
    }

    public function testGetGameMoveFactoryClassname(): void
    {
        $this->assertEquals($this->box['gameMoveFactoryClassname'], $this->gameBox->getGameMoveFactoryClassname());
    }
}
