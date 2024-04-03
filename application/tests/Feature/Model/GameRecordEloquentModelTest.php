<?php

namespace Tests\Feature\Model;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GameRecord\GameRecord;
use App\GameCore\Player\Player;
use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Services\Collection\Collection;
use App\Models\GameInviteEloquentModel;
use App\Models\GameRecordEloquentModel;
use App\Models\PlayerAnonymousEloquent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GameRecordEloquentModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected PlayerAnonymousEloquent $playerAnonymous;
    protected GameInvite $invite;
    protected array $score = ['key' => 'value'];
    protected bool $isWinner = false;
    protected GameRecordEloquentModel $record;

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
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players002,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('tic-tac-toe', $options, $this->user);

        if ($allPlayers) {
            $invite->addPlayer($this->playerAnonymous);
        }

        return $invite;
    }

    protected function prepareGameRecord(GameInvite $invite, Player $player, array $score, ?bool $isWinner = null): GameRecordEloquentModel
    {
        $inviteModel = GameInviteEloquentModel::where('id', '=', $invite->getId())->first();

        $record = new GameRecordEloquentModel();
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
        $record = GameRecordEloquentModel::where('id', '=', $this->record->id)->first();

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
}
