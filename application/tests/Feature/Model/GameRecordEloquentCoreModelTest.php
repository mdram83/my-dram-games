<?php

namespace Tests\Feature\Model;

use App\GameCore\Player\PlayerAnonymousFactory;
use App\Models\GameInviteEloquentModel;
use App\Models\GameRecordEloquentCoreModel;
use App\Models\PlayerAnonymousEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollectionPowered;
use MyDramGames\Core\GameOption\GameOptionConfigurationGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueAutostartGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueForfeitAfterGeneric;
use MyDramGames\Core\GameOption\Values\GameOptionValueNumberOfPlayersGeneric;
use MyDramGames\Core\GameRecord\GameRecord;
use MyDramGames\Utils\Php\Collection\CollectionEngine;
use MyDramGames\Utils\Player\Player;
use Tests\TestCase;

class GameRecordEloquentCoreModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected PlayerAnonymousEloquent $playerAnonymous;
    protected GameInvite $invite;
    protected array $score = ['key' => 'value'];
    protected bool $isWinner = false;
    protected GameRecordEloquentCoreModel $record;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->playerAnonymous = App::make(PlayerAnonymousFactory::class)->create(['key' => 'test-player-key']);
        $this->invite = $this->prepareGameInvite();
        $this->record = $this->prepareGameRecord($this->invite, $this->user, $this->score);
    }

    protected function prepareGameInvite(bool $allPlayers = true): GameInvite
    {
        $options = new GameOptionConfigurationCollectionPowered(
            App::make(CollectionEngine::class),
            [
                new GameOptionConfigurationGeneric(
                    'numberOfPlayers',
                    GameOptionValueNumberOfPlayersGeneric::Players002
                ),
                new GameOptionConfigurationGeneric(
                    'autostart',
                    GameOptionValueAutostartGeneric::Disabled
                ),
                new GameOptionConfigurationGeneric(
                    'forfeitAfter',
                    GameOptionValueForfeitAfterGeneric::Disabled
                ),
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->user);

        if ($allPlayers) {
            $invite->addPlayer($this->playerAnonymous);
        }

        return $invite;
    }

    protected function prepareGameRecord(GameInvite $invite, Player $player, array $score, ?bool $isWinner = null): GameRecordEloquentCoreModel
    {
        $inviteModel = GameInviteEloquentModel::where('id', '=', $invite->getId())->first();

        $record = new GameRecordEloquentCoreModel();
        $record->score = json_encode($score);
        $record->gameInvite()->associate($inviteModel);

        $record->playerable()->associate($player);

        if (isset($isWinner)) {
            $record->winnerFlag = $isWinner;
        }

        $record->save();

        return $record;
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(GameRecord::class, $this->record);
    }

    public function testGetScore(): void
    {
        $this->assertEquals($this->score, $this->record->getScore());
    }

    public function testIsWinnerDefaultReturnFalse(): void
    {
        $this->assertFalse($this->record->isWinner());
    }

    public function testIsWinnerSetToTrue(): void
    {
        $this->record->winnerFlag = true;
        $this->record->save();
        $record = GameRecordEloquentCoreModel::where('id', '=', $this->record->id)->first();

        $this->assertTrue($record->isWinner());
    }

    public function testGetPlayerWithUserClass(): void
    {
        $this->assertEquals($this->user->getId(), $this->record->getPlayer()->getId());
    }

    public function testGetPlayerWithPlayerAnonymousClass(): void
    {
        $record = $this->prepareGameRecord($this->invite, $this->playerAnonymous, $this->score);
        $this->assertEquals($this->playerAnonymous->getId(), $record->getPlayer()->getId());
    }

    public function testGetGameInvite(): void
    {
        $this->assertEquals($this->invite->getId(), $this->record->getGameInvite()->getId());
    }
}
