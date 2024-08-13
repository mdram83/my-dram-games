import React from 'react';
import GamePlayController from "../../game-core/game-play/GamePlayController.jsx";
import {unstable_batchedUpdates} from "react-dom";
import {useGamePlayStore} from "../../game-core/game-play/useGamePlayStore.jsx";
import {Menu} from "../../../template/play/components/Menu.jsx";
import {FlashMessageGamePlay} from "../../game-core/game-play/FlashMessageGamePlay.jsx";
import {useThousandStore} from "./useThousandStore.jsx";
import {seatAssignment} from "./seatAssignment.jsx";
import {OpponentSection} from "./elements/OpponentSection.jsx";
import {TableSection} from "./elements/TableSection.jsx";
import {PlayerSection} from "./elements/PlayerSection.jsx";
import {PlayerHand} from "./elements/PlayerHand.jsx";
import {PlayerInfos} from "./elements/PlayerInfos.jsx";
import {TrumpSuit} from "./elements/TrumpSuit.jsx";
import {GameInformation} from "./elements/GameInformation.jsx";
import {GameMessageHandler} from "./elements/GameMessageHandler.jsx";

const playerName = window.MyDramGames.player.name;

const getPlayersNames = (situation) => Object.getOwnPropertyNames(situation.orderedPlayers);

const setupSituation = (situation) => unstable_batchedUpdates(() => {

    useThousandStore.getState().setSituation(situation);

    if (getPlayersNames(situation).length === 4) {
        useThousandStore.getState().setIsFourPlayersGame(true);
        useThousandStore.getState().setIsPlayerFourPlayersDealer(playerName === situation.dealer);
    }

});

const setupMoveEvent = (e) => {

    const previousSituation = useThousandStore.getState().situation;
    const isFourPlayersGame = useThousandStore.getState().isFourPlayersGame;
    const hasSwitchedPlayerToActive = () => (
        e.situation.activePlayer === window.MyDramGames.player.name
        && previousSituation.activePlayer !== e.situation.activePlayer
    );

    unstable_batchedUpdates(() => {
        useThousandStore.getState().setSituation(e.situation);

        if (e.situation.isFinished) {

            useGamePlayStore.getState().setFinished();
            useGamePlayStore.getState().setMessage(e.situation.result.message, false, 10);

        } else {

            useGamePlayStore.getState().setActivePlayer(e.situation.activePlayer);

            if (isFourPlayersGame) {
                useThousandStore.getState().setIsPlayerFourPlayersDealer(playerName === e.situation.dealer);
            }

            if (hasSwitchedPlayerToActive()) {
                useGamePlayStore.getState().setMessage('Your turn', false, 0.5);
            }
        }
    });
}

const controller = new GamePlayController(getPlayersNames, setupSituation, setupMoveEvent);

const [leftHandSeat, frontSeat, rightHandSeat] = seatAssignment();
const fourPlayersGame = controller.getGameInvite().options.numberOfPlayers === 4;
const sidePlayersPosition = fourPlayersGame ? 'mt-[25vh] sm:mt-[20vh]' : 'mt-[20vh] sm:mt-[15vh]';

controller.getRoot().render(
    <div className="relative w-full h-full">

        <div className="fixed top-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800 z-10">
            <Menu gameInvite={controller.getGameInvite()}/>
        </div>

        {/*--- Board ---*/}
        {/*Scrollable*/}
        {/*<div className="relative mt-[10vh] sm:mt-[12vh] pt-[2vh] w-full">*/}
        {/*Fixed*/}
        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-[80vh] sm:h-[76vh] bg-bottom bg-no-repeat bg-cover"
             style={{ backgroundImage: `url(${window.MyDramGames["asset-url"].img + '/games/thousand/pexels-marta-wave-5875935-medium.jpg'})` }}
        >

            <div className="grid grid-cols-3 gap-1 content-stretch h-full">

                <div className="flex justify-center items-start">
                    <div className={sidePlayersPosition}>
                        <OpponentSection playerName={leftHandSeat} fourPlayersGame={fourPlayersGame}/>
                    </div>
                </div>

                <div className="grid grid-rows-2 gap-1 content-stretch h-full justify-items-center items-center">
                    <div>
                        {fourPlayersGame && <OpponentSection playerName={frontSeat} fourPlayersGame={fourPlayersGame}/>}
                        {!fourPlayersGame && <div></div>}
                    </div>
                    <div className="h-full w-full flex justify-center items-center">
                        <TableSection/>
                    </div>
                </div>

                <div className="flex justify-center items-start">
                    <div className={sidePlayersPosition}>
                        <OpponentSection playerName={rightHandSeat} fourPlayersGame={fourPlayersGame}/>
                    </div>
                </div>

            </div>

        </div>

        <div
            className="fixed -bottom-[0.1vh] w-[92%] sm:w-[96%] h-[10vh] sm:h-[12vh] px-[4%] sm:px-[2%] py-[2vh] bg-gray-800">
            <PlayerSection fourPlayersGame={fourPlayersGame}/>
        </div>

        <div className="fixed bottom-[10vh] w-full z-10">
            <div className="flex justify-center">
                <PlayerHand playerName={playerName}/>
            </div>
        </div>

        <div className="fixed bottom-[18vh] sm:bottom-[20vh] w-full z-30">
            <div className="flex justify-center">
                <div className="flex relative justify-center">
                    <PlayerInfos/>
                </div>
            </div>
        </div>

        <div className="fixed mt-[12vh] sm:mt-[14vh] ml-[2vh]">
            <TrumpSuit/>
        </div>

        <div className="fixed mt-[12vh] sm:mt-[14vh] right-[2vw] z-20">
            <GameInformation />
        </div>

        <GameMessageHandler />

        <FlashMessageGamePlay/>

    </div>
);
