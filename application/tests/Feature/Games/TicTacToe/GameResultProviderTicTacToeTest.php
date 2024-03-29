<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameResult\GameResultProvider;
use App\GameCore\GameResult\GameResultProviderException;
use App\GameCore\Services\Collection\Collection;
use App\Games\GameResultTicTacToe;
use App\Games\TicTacToe\CollectionGameCharacterTicTacToe;
use App\Games\TicTacToe\GameBoardTicTacToe;
use App\Games\TicTacToe\GameCharacterTicTacToe;
use App\Games\TicTacToe\GameResultProviderTicTacToe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameResultProviderTicTacToeTest extends TestCase
{
    use RefreshDatabase;

    protected GameResultProviderTicTacToe $provider;
    protected array $players;
    protected CollectionGameCharacterTicTacToe $characters;
    protected GameBoardTicTacToe $board;

    public function setUp(): void
    {
        parent::setUp();
        $this->provider = new GameResultProviderTicTacToe();
        $this->players = [User::factory()->create(), User::factory()->create()];
        $this->characters = new CollectionGameCharacterTicTacToe(
            App::make(Collection::class),
            [
                new GameCharacterTicTacToe('x', $this->players[0]),
                new GameCharacterTicTacToe('o', $this->players[1]),
            ],
        );
        $this->board = new GameBoardTicTacToe();
    }

    protected function setupBoard(array $fields): void
    {
        foreach ($fields as $key => $value) {
            if (isset($value)) {
                $this->board->setFieldValue((string) $key, $value);
            }
        }
    }

    protected function getData(string $nextMoveCharacterName = 'x'): array
    {
        return [
            'board' => $this->board,
            'characters' => $this->characters,
            'nextMoveCharacterName' => $nextMoveCharacterName,
        ];
    }

    public function testInterfaceImplemented(): void
    {
        $this->assertInstanceOf(GameResultProvider::class, $this->provider);
    }

    public function testThrowExceptionIfMissingNextCharacterName(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        $data = ['characters' => $this->characters, 'board' => $this->board];
        $this->provider->getResult($data);
    }

    public function testThrowExceptionIfIncorrectNextCharacterName(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        $this->provider->getResult($this->getData('a'));
    }

    public function testThrowExceptionIfDataMissBoard(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        $data = ['characters' => $this->characters];
        $this->provider->getResult($data);
    }

    public function testThrowExceptionIfDataHasWrongBoard(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        $data = ['characters' => $this->characters, 'board' => 'wrong-board'];
        $this->provider->getResult($data);
    }

    public function testThrowExceptionIfDataMissCharacters(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        $data = ['board' => $this->board];
        $this->provider->getResult($data);
    }

    public function testThrowExceptionIfDataHasWrongCharacters(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        $data = ['characters' => 'wrong-characters', 'board' => $this->board];
        $this->provider->getResult($data);
    }

    public function testGetResultFromWinningRowOne(): void
    {
        $this->setupBoard([1 => 'x', 2 => 'x', 3 => 'x', 4 => 'o', 5 => 'o']);
        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals(['1', '2', '3'], $result->toArray()['winningFields']);
        $this->assertEquals($this->players[0]->getName(), $result->toArray()['winnerName']);
    }

    public function testGetResultFromWinningRowTwo(): void
    {
        $this->setupBoard([1 => 'x', 2 => 'o', 3 => 'x', 4 => 'o', 5 => 'o', 6 => 'o', 7 => 'x', 8 => 'x']);
        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals(['4', '5', '6'], $result->toArray()['winningFields']);
        $this->assertEquals($this->players[1]->getName(), $result->toArray()['winnerName']);
    }

    public function testGetResultFromWinningRowThree(): void
    {
        $this->setupBoard([7 => 'x', 8 => 'x', 9 => 'x', 1 => 'o', 2 => 'o']);
        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals(['7', '8', '9'], $result->toArray()['winningFields']);
        $this->assertEquals($this->players[0]->getName(), $result->toArray()['winnerName']);
    }

    public function testGetResultFromWinningColumnOne(): void
    {
        $this->setupBoard([1 => 'x', 4 => 'x', 7 => 'x', 5 => 'o', 6 => 'o']);
        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals(['1', '4', '7'], $result->toArray()['winningFields']);
        $this->assertEquals($this->players[0]->getName(), $result->toArray()['winnerName']);
    }

    public function testGetResultFromWinningColumnTwo(): void
    {
        $this->setupBoard([2 => 'x', 5 => 'x', 8 => 'x', 1 => 'o', 6 => 'o']);
        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals(['2', '5', '8'], $result->toArray()['winningFields']);
        $this->assertEquals($this->players[0]->getName(), $result->toArray()['winnerName']);
    }

    public function testGetResultFromWinningColumnThree(): void
    {
        $this->setupBoard([3 => 'x', 6 => 'x', 9 => 'x', 1 => 'o', 2 => 'o']);
        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals(['3', '6', '9'], $result->toArray()['winningFields']);
        $this->assertEquals($this->players[0]->getName(), $result->toArray()['winnerName']);
    }

    public function testGetResultFromWinningDiagonalLeftRight(): void
    {
        $this->setupBoard([1 => 'x', 5 => 'x', 9 => 'x', 2 => 'o', 6 => 'o']);
        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals(['1', '5', '9'], $result->toArray()['winningFields']);
        $this->assertEquals($this->players[0]->getName(), $result->toArray()['winnerName']);
    }

    public function testGetResultFromWinningDiagonalRightLeft(): void
    {
        $this->setupBoard([3 => 'x', 5 => 'x', 7 => 'x', 1 => 'o', 2 => 'o']);
        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals(['3', '5', '7'], $result->toArray()['winningFields']);
        $this->assertEquals($this->players[0]->getName(), $result->toArray()['winnerName']);
    }

    public function testGetDrawCombinationOneEnsuringNoMutationToPassedData(): void
    {
        $this->setupBoard([
            1 => 'o', 2 => 'x', 3 => 'o',
            4 => 'o', 5 => 'x', 6 => 'x',
            7 => null, 8 => 'o', 9 => 'x',
        ]);
        $originalBoardJson = $this->board->toJson();
        $originalCharacters = $this->characters->toArray();

        $result = $this->provider->getResult($this->getData());

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals([], $result->toArray()['winningFields']);
        $this->assertNull($result->toArray()['winnerName']);
        $this->assertJsonStringEqualsJsonString($originalBoardJson, $this->board->toJson());
        $this->assertEquals($originalCharacters, $this->characters->toArray());
    }

    public function testGetDrawCombinationTwo(): void
    {
        $this->setupBoard([
            1 => 'o', 2 => 'x', 3 => null,
            4 => 'x', 5 => 'o', 6 => null,
            7 => 'x', 8 => 'o', 9 => 'x',
        ]);

        $result = $this->provider->getResult($this->getData('o'));

        $this->assertInstanceOf(GameResultTicTacToe::class, $result);
        $this->assertEquals([], $result->toArray()['winningFields']);
        $this->assertNull($result->toArray()['winnerName']);
    }

    public function testGetResultWithoutWinOrDrawOne(): void
    {
        $this->setupBoard([
            1 => null, 2 => 'o', 3 => null,
            4 => null, 5 => 'x', 6 => null,
            7 => null, 8 => null, 9 => null,
        ]);

        $result = $this->provider->getResult($this->getData());
        $this->assertNull($result);
    }

    public function testGetResultWithoutWinOrDrawTwo(): void
    {
        $this->setupBoard([
            1 => null, 2 => 'o', 3 => 'x',
            4 => 'x', 5 => 'x', 6 => 'o',
            7 => 'o', 8 => null, 9 => 'x',
        ]);

        $result = $this->provider->getResult($this->getData('o'));
        $this->assertNull($result);
    }

    public function testGetResultWithoutWinOrDrawThree(): void
    {
        $this->setupBoard([
            1 => null, 2 => 'o', 3 => 'x',
            4 => 'x', 5 => 'x', 6 => 'o',
            7 => 'o', 8 => 'o', 9 => 'x',
        ]);

        $result = $this->provider->getResult($this->getData());
        $this->assertNull($result);
    }


//$this->setupBoard([
//1 => null, 2 => null, 3 => null,
//4 => null, 5 => null, 6 => null,
//7 => null, 8 => null, 9 => null,
//]);

    // buildDrawResultDifferentCombinations
    // returnNullIfNoResultCanBeBuildYet
}
