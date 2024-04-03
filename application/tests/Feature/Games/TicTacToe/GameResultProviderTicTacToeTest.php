<?php

namespace Tests\Feature\Games\TicTacToe;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameRecord\CollectionGameRecord;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\GameRecord\GameRecordRepository;
use App\GameCore\GameResult\GameResultProvider;
use App\GameCore\GameResult\GameResultProviderException;
use App\GameCore\Services\Collection\Collection;
use App\Games\TicTacToe\Elements\GameBoardTicTacToe;
use App\Games\TicTacToe\Elements\GameCharacterTicTacToe;
use App\Games\TicTacToe\GameResultProviderTicTacToe;
use App\Games\TicTacToe\GameResultTicTacToe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameResultProviderTicTacToeTest extends TestCase
{
    use RefreshDatabase;

    protected GameResultProviderTicTacToe $provider;
    protected array $players;
    protected \App\Games\TicTacToe\Elements\CollectionGameCharacterTicTacToe $characters;
    protected GameBoardTicTacToe $board;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new GameResultProviderTicTacToe(
            App::make(Collection::class),
            App::make(GameRecordFactory::class)
        );

        $this->board = new \App\Games\TicTacToe\Elements\GameBoardTicTacToe();
        $this->players = [User::factory()->create(), User::factory()->create()];

        $this->characters = new \App\Games\TicTacToe\Elements\CollectionGameCharacterTicTacToe(
            App::make(Collection::class),
            [
                new GameCharacterTicTacToe('x', $this->players[0]),
                new \App\Games\TicTacToe\Elements\GameCharacterTicTacToe('o', $this->players[1]),
            ],
        );
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

    protected function getGameInvite(): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->players[0]);
        $invite->addPlayer($this->players[1]);

        return $invite;
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

    public function testGetResultWithoutWinOrDrawFour(): void
    {
        $this->setupBoard([
            1 => 'x', 2 => 'x', 3 => 'o',
            4 => 'o', 5 => 'x', 6 => 'x',
            7 => null, 8 => null, 9 => 'o',
        ]);

        $result = $this->provider->getResult($this->getData('o'));
        $this->assertNull($result);
    }

    public function testThrowExceptionIfResultAlreadyProvided(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_RESULTS_ALREADY_SET);

        $this->setupBoard([3 => 'x', 5 => 'x', 7 => 'x', 1 => 'o', 2 => 'o']);
        $this->provider->getResult($this->getData());
        $this->provider->getResult($this->getData());
    }

    public function testThrowExceptionWhenCreatingRecordWithoutResult(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_RESULT_NOT_SET);

        $this->provider->createGameRecords($this->getGameInvite());
    }

    public function testThrowExceptionWhenCreatingRecordAlreadyCreated(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_RECORD_ALREADY_SET);

        $this->setupBoard([3 => 'x', 5 => 'x', 7 => 'x', 1 => 'o', 2 => 'o']);
        $this->provider->getResult($this->getData());
        $invite = $this->getGameInvite();
        $this->provider->createGameRecords($invite);
        $this->provider->createGameRecords($invite);
    }

    public function testCreateGameRecords(): void
    {
        $this->setupBoard([3 => 'x', 5 => 'x', 7 => 'x', 1 => 'o', 2 => 'o']);
        $this->provider->getResult($this->getData());
        $invite = $this->getGameInvite();
        $recordsFromProvider = $this->provider->createGameRecords($invite);
        $recordsFromRepository = App::make(GameRecordRepository::class)->getByGameInvite($invite);

        $providedWinnerRecord = current(array_filter($recordsFromProvider->toArray(), fn($element) => $element->isWinner()));
        $providedLooserRecord = current(array_filter($recordsFromProvider->toArray(), fn($element) => $element->isWinner() === false));

        $repositoryWinnerRecord = current(array_filter($recordsFromProvider->toArray(), fn($element) => $element->isWinner()));
        $repositoryLooserRecord = current(array_filter($recordsFromProvider->toArray(), fn($element) => $element->isWinner() === false));

        $this->assertInstanceOf(CollectionGameRecord::class, $recordsFromProvider);
        $this->assertEquals(['character' => 'x'], $providedWinnerRecord->getScore());
        $this->assertEquals(2, $recordsFromProvider->count());
        $this->assertEquals(2, $recordsFromRepository->count());
        $this->assertEquals(
            [
                'winner' => [
                    $repositoryWinnerRecord->getPlayer()->getId(),
                    $repositoryWinnerRecord->isWinner(),
                    $repositoryWinnerRecord->getScore(),
                ],
                'looser' => [
                    $repositoryLooserRecord->getPlayer()->getId(),
                    $repositoryLooserRecord->isWinner(),
                    $repositoryLooserRecord->getScore(),
                ],
            ],
            [
                'winner' => [
                    $providedWinnerRecord->getPlayer()->getId(),
                    $providedWinnerRecord->isWinner(),
                    $providedWinnerRecord->getScore(),
                ],
                'looser' => [
                    $providedLooserRecord->getPlayer()->getId(),
                    $providedLooserRecord->isWinner(),
                    $providedLooserRecord->getScore(),
                ],
            ],
        );
    }
}
