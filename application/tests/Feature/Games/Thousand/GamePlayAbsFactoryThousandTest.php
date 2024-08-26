<?php

namespace Tests\Feature\Games\Thousand;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameInvite\GameInviteFactory;
use App\GameCore\GameOptionValue\CollectionGameOptionValueInput;
use App\GameCore\GameOptionValue\GameOptionValueAutostart;
use App\GameCore\GameOptionValue\GameOptionValueForfeitAfter;
use App\GameCore\GameOptionValue\GameOptionValueNumberOfPlayers;
use App\GameCore\GamePlay\GamePlayAbsFactory;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlayStorage\GamePlayStorageException;
use App\GameCore\Services\Collection\Collection;
use App\Games\Thousand\GameOptionValueThousandBarrelPoints;
use App\Games\Thousand\GameOptionValueThousandNumberOfBombs;
use App\Games\Thousand\GameOptionValueThousandReDealConditions;
use App\Games\Thousand\GamePlayAbsFactoryThousand;
use App\Games\Thousand\GamePlayThousand;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class GamePlayAbsFactoryThousandTest extends TestCase
{
    protected GamePlayAbsFactoryThousand $factory;

    public function setUp(): void{
        parent::setUp();
        $this->factory = App::make(GamePlayAbsFactoryThousand::class);
    }

    protected function prepareGameInvite(bool $completeSetup = true): GameInvite
    {
        $options = new CollectionGameOptionValueInput(
            App::make(Collection::class),
            [
                'numberOfPlayers' => GameOptionValueNumberOfPlayers::Players003,
                'autostart' => GameOptionValueAutostart::Disabled,
                'forfeitAfter' => GameOptionValueForfeitAfter::Disabled,
                'thousand-barrel-points' => GameOptionValueThousandBarrelPoints::EightHundred,
                'thousand-number-of-bombs' => GameOptionValueThousandNumberOfBombs::One,
                'thousand-re-deal-conditions' => GameOptionValueThousandReDealConditions::Disabled,
            ]
        );

        $factory = App::make(GameInviteFactory::class);
        $invite = $factory->create('thousand', $options, User::factory()->create());

        if ($completeSetup) {
            $invite->addPlayer(User::factory()->create());
            $invite->addPlayer(User::factory()->create());
        }

        return $invite;
    }

    public function testClassInstance(): void
    {
        $this->assertInstanceOf(GamePlayAbsFactory::class, $this->factory);
    }

    public function testThrowExceptionWhenIncompleteInvite(): void
    {
        $this->expectException(GamePlayException::class);
        $this->expectExceptionMessage(GamePlayException::MESSAGE_MISSING_PLAYERS);

        $this->factory->create($this->prepareGameInvite(false));
    }

    public function testThrowExceptionWhenDuplicatedInvite(): void
    {
        $this->expectException(GamePlayStorageException::class);
        $this->expectExceptionMessage(GamePlayStorageException::MESSAGE_INVALID_INVITE);

        $invite = $this->prepareGameInvite();
        $this->factory->create($invite);
        $this->factory->create($invite);
    }

    public function testCreate(): void
    {
        $play = $this->factory->create($this->prepareGameInvite());
        $this->assertInstanceOf(GamePlayThousand::class, $play);
    }
}
