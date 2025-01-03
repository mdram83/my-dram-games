import React from 'react';
import GamePlayController from "../../game-core/game-play/GamePlayController.jsx";
import {unstable_batchedUpdates} from "react-dom";
import {useGamePlayStore} from "../../game-core/game-play/useGamePlayStore.jsx";
import {Menu} from "../../../template/play/components/Menu.jsx";
import {FlashMessageGamePlay} from "../../game-core/game-play/FlashMessageGamePlay.jsx";
import {useNetrunnersStore} from "./useNetrunnersStore.jsx";
import {PlayersList} from "./elements/players/PlayersList.jsx";
import {CharactersGrid} from "./elements/character/CharactersGrid.jsx";
import {PlayerInfo} from "./elements/players/PlayerInfo.jsx";
import {GameMap} from "./elements/map/GameMap.jsx";
import {GameMoveSubmitter} from "./elements/console/GameMoveSubmitter.jsx";
import {Console} from "./elements/console/Console.jsx";
import {RechargeInfo} from "./elements/actions/RechargeInfo.jsx";
import {EncounterSelection} from "./elements/encounters/EncounterSelection.jsx";
import {ConflictController} from "./elements/battle/ConflictController.jsx";
import {RestartInfo} from "./elements/actions/RestartInfo.jsx";
import {Result} from "./elements/players/Result.jsx";

const getPlayersNames = (situation) => Object.getOwnPropertyNames(situation.players);

const setupSituation = (situation) => unstable_batchedUpdates(() => useNetrunnersStore.getState().setSituation(situation));

const setupMoveEvent = (e) => {

    const previousSituation = useNetrunnersStore.getState().situation;
    const hasSwitchedPlayerToActive = () => (
        e.situation.activePlayer === window.MyDramGames.player.name
        && previousSituation.activePlayer !== e.situation.activePlayer
    );

    unstable_batchedUpdates(() => {
        useNetrunnersStore.getState().setSituation(e.situation);

        if (e.situation.isFinished) {

            useGamePlayStore.getState().setFinished();
            useGamePlayStore.getState().setMessage(e.situation.result.message, false, 10);

        } else {

            useGamePlayStore.getState().setActivePlayer(e.situation.activePlayer);
            if (hasSwitchedPlayerToActive()) {
                useGamePlayStore.getState().setMessage('Your turn', false, 0.5);
            }

        }
    });
}

const controller = new GamePlayController(getPlayersNames, setupSituation, setupMoveEvent);

const onLoadPhaseKey = useNetrunnersStore.getState().situation.phase.key;

controller.getRoot().render(
    <div className="relative w-full h-full">

        {/*Top Bar*/}
        <div className="fixed top-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800 z-10">
            <Menu gameInvite={controller.getGameInvite()}/>
        </div>

        {/*Main Area*/}
        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-[80vh] sm:h-[76vh] bg-gray-900">
            {onLoadPhaseKey === 'character' && <CharactersGrid/>}
            <GameMap/>
        </div>

        {/*Bottom Bar*/}
        <div className="fixed -bottom-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800">
            <GameMoveSubmitter/>
            <Console/>
        </div>

        {/*All Others*/}
        <div className="fixed bottom-[10vh] sm:top-[12vh] w-full sm:w-fit h-fit left-0 sm:left-[2vh] z-20">
            <div className="flex justify-center sm:justify-start">
                <PlayersList/>
            </div>
        </div>

        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-fit z-10">
            <div className="flex justify-center">
                <PlayerInfo/>
            </div>
        </div>

        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-fit z-10">
            <div className="flex justify-center">
                <RechargeInfo/>
                <EncounterSelection/>
                <ConflictController/>
                <RestartInfo/>
            </div>
        </div>

        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-fit z-50">
            <div className="flex justify-center">
                <Result />
            </div>
        </div>

        <FlashMessageGamePlay/>

    </div>
);
