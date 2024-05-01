<?php

namespace Games\Thousand;

use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameRecord\CollectionGameRecord;
use App\GameCore\GameRecord\GameRecord;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\GameResult\GameResultProvider;
use App\GameCore\GameResult\GameResultProviderException;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\Games\Thousand\GameResultProviderThousand;
use App\Games\Thousand\GameResultThousand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameResultProviderThousandTest extends TestCase
{
    use RefreshDatabase;

    private Collection $handler;
    private GameRecordFactory $recordFactory;
    private GameInvite $invite;
    private GameResultProviderThousand $provider;

    private CollectionGamePlayPlayers $players;
    private array $playersDataWin;
    private array $playersDataNoWin;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = App::make(Collection::class);
        $this->recordFactory = $this->createMock(GameRecordFactory::class);
        $this->recordFactory->method('create')->willReturn($this->createMock(GameRecord::class));
        $this->invite = $this->createMock(GameInvite::class);
        $this->provider = new GameResultProviderThousand($this->handler, $this->recordFactory);

        $players = [];
        for ($i = 0; $i <= 2; $i++) {
            $player = $this->createMock(Player::class);
            $player->method('getId')->willReturn("Id$i");
            $player->method('getName')->willReturn("Player_$i");
            $players[] = $player;

            $this->playersDataWin[$player->getId()]['points'] = [1 => ($i + 1) * 100, 2 => ($i + 1) * 350];
            $this->playersDataNoWin[$player->getId()]['points'] = [1 => ($i + 1) * 10, 2 => ($i + 1) * 100];

            $this->playersDataWin[$player->getId()]['seat'] = $i + 1;
            $this->playersDataNoWin[$player->getId()]['seat'] = $i + 1;
        }

        $this->players = new CollectionGamePlayPlayers(clone $this->handler, $players);
    }

    public function testInterface(): void
    {
        $this->assertInstanceOf(GameResultProvider::class, $this->provider);
    }

    public function testThrowExceptionWhenGetResultWithoutPlayersCollection(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        $this->provider->getResult(['players' => [], 'playersData' => $this->playersDataWin]);
    }

    public function testThrowExceptionWhenGetResultPlayersCollectionNotMatchingPlayersData(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        $this->playersDataWin['wrongId'] = $this->playersDataWin['Id0'];
        unset($this->playersDataWin['Id0']);

        $this->provider->getResult(['players' => $this->players, 'playersData' => $this->playersDataWin]);
    }

    public function testThrowExceptionWhenGetResultPlayersDataMissPoints(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);

        unset($this->playersDataWin['Id0']['points']);
        $this->provider->getResult(['players' => $this->players, 'playersData' => $this->playersDataWin]);
    }

    public function testThrowExceptionWhenGetResultsCalledSecondTime(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_RESULTS_ALREADY_SET);

        $this->provider->getResult(['players' => $this->players, 'playersData' => $this->playersDataWin]);
        $this->provider->getResult(['players' => $this->players, 'playersData' => $this->playersDataWin]);
    }

    public function testThrowExceptionWhenCreateGameRecordWithoutResult(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_RESULT_NOT_SET);

        $this->provider->createGameRecords($this->invite);
    }

    public function testThrowExceptionWhenCreateGameRecordSecondTime(): void
    {
        $this->expectException(GameResultProviderException::class);
        $this->expectExceptionMessage(GameResultProviderException::MESSAGE_RECORD_ALREADY_SET);

        $this->provider->getResult(['players' => $this->players, 'playersData' => $this->playersDataWin]);
        $this->provider->createGameRecords($this->invite);
        $this->provider->createGameRecords($this->invite);
    }

    public function testGetResultWithWin(): void
    {
        $result = $this->provider->getResult(['players' => $this->players, 'playersData' => $this->playersDataWin]);
        $this->assertInstanceOf(GameResultThousand::class, $result);
    }

    public function testGetResultWithNoWin(): void
    {
        $result = $this->provider->getResult(['players' => $this->players, 'playersData' => $this->playersDataNoWin]);
        $this->assertNull($result);
    }

    public function testCreateGameRecordsWin(): void
    {
        $this->provider->getResult(['players' => $this->players, 'playersData' => $this->playersDataWin]);
        $records = $this->provider->createGameRecords($this->invite);

        $this->assertInstanceOf(CollectionGameRecord::class, $records);
        $this->assertEquals(3, $records->count());
    }
}
