<?php

namespace Games\Thousand;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\Services\Collection\Collection;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use App\Games\Thousand\GameOptionValueThousandNumberOfBombs;
use App\Games\Thousand\GameOptionValueThousandReDealConditions;
use App\Games\Thousand\GamePlayAbsFactoryThousand;
use App\Games\Thousand\GamePlayThousand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GamePlayThousandTest extends TestCase
{
    use RefreshDatabase;

    private GamePlayThousand $play;
    private array $players;

    public function setUp(): void
    {
        parent::setUp();

        $this->players = [
            User::factory()->create(),
            User::factory()->create(),
            User::factory()->create(),
            User::factory()->create(),
        ];

        $this->play = $this->getGamePlay($this->getGameInvite());
    }

    protected function getGameInvite(bool $fourPlayers = false): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => $fourPlayers ? GameOptionValueNumberOfPlayers::Players004 : GameOptionValueNumberOfPlayers::Players003,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
                'thousand-barrel-points' => GameOptionValueThousandBarrelPoints::EightHundred,
                'thousand-number-of-bombs' => GameOptionValueThousandNumberOfBombs::One,
                'thousand-re-deal-conditions' => GameOptionValueThousandReDealConditions::Disabled,
            ]
        );

        $invite = App::make(GameInviteFactory::class)->create('thousand', $options, $this->players[0]);

        $invite->addPlayer($this->players[1]);
        $invite->addPlayer($this->players[2]);
        if ($fourPlayers) {
            $invite->addPlayer($this->players[3]);
        }

        return $invite;
    }

    protected function getGamePlay(GameInvite $invite): GamePlayThousand
    {
        return App::make(GamePlayAbsFactoryThousand::class)->create($invite);
    }
    public function testClassInstance(): void
    {
        $this->assertInstanceOf(GamePlay::class, $this->play);
        $this->assertInstanceOf(GamePlayBase::class, $this->play);
    }

    public function testGetSituationAfterInitiation(): void
    {
        $expected = [

        ];
    }
}
