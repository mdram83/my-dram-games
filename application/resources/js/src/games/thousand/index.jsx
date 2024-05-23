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

const getPlayersNames = (situation) => Object.getOwnPropertyNames(situation.orderedPlayers);

const setupSituation = (situation) => unstable_batchedUpdates(
    () => useThousandStore.getState().setSituation(situation)
);

const setupMoveEvent = (e) =>
    unstable_batchedUpdates(() => {
        useThousandStore.getState().setSituation(e.situation);

        if (e.situation.isFinished) {

            useGamePlayStore.getState().setFinished();
            useGamePlayStore.getState().setMessage(e.situation.result.message, false, 10);

            // useTicTacToeStore.getState().setWinningFields(e.situation.result.details.winningFields); // TODO adjust for thousand

        } else {

            useGamePlayStore.getState().setActivePlayer(e.situation.activePlayer);

            if (e.situation.activePlayer === window.MyDramGames.player.name) {
                useGamePlayStore.getState().setMessage('Your turn', false, 0.5);
            }
        }
    });

const controller = new GamePlayController(getPlayersNames, setupSituation, setupMoveEvent);

// TODO remove after testing
console.log(useThousandStore.getState().situation);
console.log(useGamePlayStore.getState());

const [leftHandSeat, frontSeat, rightHandSeat] = seatAssignment();
const fourPlayersGame = controller.getGameInvite().options.numberOfPlayers === 4;
const sidePlayersPosition = fourPlayersGame ? 'mb-[10vh]' : 'mb-[30vh]';

controller.getRoot().render(

    <div className="relative w-full h-full">

        <div className="fixed top-0 w-full h-[10vh] sm:h-[12vh] bg-gray-800 z-10">
            <Menu gameInvite={controller.getGameInvite()} />
        </div>

        {/*--- Board ---*/}
        {/*Scrollable*/}
        {/*<div className="relative mt-[10vh] sm:mt-[12vh] pt-[2vh] w-full">*/}
        {/*Fixed*/}
        <div className="fixed mt-[10vh] sm:mt-[12vh] w-full h-[74vh] bg-[url('https://media.istockphoto.com/id/966787750/pl/zdj%C4%99cie/puste-t%C5%82o-tabeli.jpg?s=612x612&w=0&k=20&c=ovSedN2ph7_RUmMmOU7llHEyM8wsnBN7qO_db6Qi4Hc=')]">

            <div className="grid grid-cols-3 gap-1 content-stretch h-full">

                <div className="flex justify-center items-center">
                    <div className={sidePlayersPosition}>
                        <OpponentSection playerName={leftHandSeat} fourPlayersGame={fourPlayersGame} />
                    </div>
                </div>

                <div className="grid grid-rows-3 gap-1 content-stretch h-full justify-items-center items-center">
                    <div>
                        {fourPlayersGame && <OpponentSection playerName={frontSeat} fourPlayersGame={fourPlayersGame} />}
                        {!fourPlayersGame && <div></div>}
                    </div>
                    <div className="row-span-2">
                        <TableSection />
                    </div>
                </div>

                <div className="flex justify-center items-center">
                    <div className={sidePlayersPosition}>
                        <OpponentSection playerName={rightHandSeat} fourPlayersGame={fourPlayersGame} />
                    </div>
                </div>

            </div>

        </div>

        <div className="fixed bottom-0 w-full h-[16vh] sm:h-[12vh] px-[4%] sm:px-[2%] py-[2vh] bg-gray-800">
            <PlayerSection />
        </div>

        <FlashMessageGamePlay />

    </div>
);

