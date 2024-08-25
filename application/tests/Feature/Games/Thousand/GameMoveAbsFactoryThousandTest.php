<?php

namespace Games\Thousand;

use App\GameCore\GameElements\GameMove\GameMoveException;
use App\Games\Thousand\Elements\GameMoveThousandBidding;
use App\Games\Thousand\Elements\GameMoveThousandCollectTricks;
use App\Games\Thousand\Elements\GameMoveThousandCountPoints;
use App\Games\Thousand\Elements\GameMoveThousandDeclaration;
use App\Games\Thousand\Elements\GameMoveThousandPlayCard;
use App\Games\Thousand\Elements\GameMoveThousandSorting;
use App\Games\Thousand\Elements\GameMoveThousandStockDistribution;
use App\Games\Thousand\GameMoveAbsFactoryThousand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GameMoveAbsFactoryThousandTest extends TestCase
{
    use RefreshDatabase;

    private GameMoveAbsFactoryThousand $factory;
    private Player $player;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new GameMoveAbsFactoryThousand();
        $this->player = User::factory()->create();
    }

    public function testThrowExceptionWhenPhaseKeyMissing(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $this->factory->create($this->player, []);
    }

    public function testThrowExceptionWhenPhaseKeyEmpty(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $this->factory->create($this->player, ['phase' => '']);
    }

    public function testThrowExceptionWhenPhaseKeyNotSortingAndNotExist(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $this->factory->create($this->player, ['phase' => 'something-definitely-wrong']);
    }

    public function testThrowExceptionWhenInputsDataMissing(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $this->factory->create($this->player, ['phase' => 'sorting']);
    }

    public function testThrowExceptionWhenInputsDataNotArray(): void
    {
        $this->expectException(GameMoveException::class);
        $this->expectExceptionMessage(GameMoveException::MESSAGE_INVALID_MOVE_PARAMS);

        $this->factory->create($this->player, ['phase' => 'sorting', 'data' => 'not-array']);
    }

    public function testCreateGameMoveThousandSorting(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'sorting', 'data' => ['hand' => ['123', '234']]]);
        $this->assertInstanceOf(GameMoveThousandSorting::class, $move);
    }

    public function testCreateGameMoveThousandBidding(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'bidding', 'data' => ['decision' => 'bid', 'bidAmount' => 110]]);
        $this->assertInstanceOf(GameMoveThousandBidding::class, $move);
    }

    public function testCreateGameMoveThousandStockDistribution(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'stock-distribution', 'data' => ['distribution' => ['playerX' => '123', 'playerY' => '234']]]);
        $this->assertInstanceOf(GameMoveThousandStockDistribution::class, $move);
    }

    public function testCreateGameMoveThousandDeclaration(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'declaration', 'data' => ['declaration' => 200]]);
        $this->assertInstanceOf(GameMoveThousandDeclaration::class, $move);
    }

    public function testCreateGameMovePlayCardPhase1(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'playing-first-card', 'data' => ['card' => '123']]);
        $this->assertInstanceOf(GameMoveThousandPlayCard::class, $move);
    }

    public function testCreateGameMovePlayCardPhase2(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'playing-second-card', 'data' => ['card' => '123']]);
        $this->assertInstanceOf(GameMoveThousandPlayCard::class, $move);
    }

    public function testCreateGameMovePlayCardPhase3(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'playing-third-card', 'data' => ['card' => '123']]);
        $this->assertInstanceOf(GameMoveThousandPlayCard::class, $move);
    }

    public function testCreateGameMoveCollectTricks(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'collecting-tricks', 'data' => ['collect' => true]]);
        $this->assertInstanceOf(GameMoveThousandCollectTricks::class, $move);
    }

    public function testCreateGameMoveThousandCountPoints(): void
    {
        $move = $this->factory->create($this->player, ['phase' => 'counting-points', 'data' => ['ready' => true]]);
        $this->assertInstanceOf(GameMoveThousandCountPoints::class, $move);
    }

    // later test for one specific phase (but not sorting as this is unique)
}
