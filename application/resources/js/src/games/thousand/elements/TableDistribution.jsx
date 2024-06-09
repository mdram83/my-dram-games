import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import axios from "axios";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";

export const TableDistribution = () => {

    console.log('TableDistribution');

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);
    const phaseKey = useThousandStore(state => state.situation.phase.key);

    const meldMarriageDecision = useThousandStore(state => state.meldMarriageDecision);
    const setMeldMarriageDecision = useThousandStore(state => state.setMeldMarriageDecision);

    const activeCardKey = useThousandStore(state => state.activeCardKey);
    const setActiveCardKey = useThousandStore(state => state.setActiveCardKey);
    const hasActiveCard = activeCardKey !== null;

    const divClassBase = ' w-[60px] h-[84px] -ml-[30px] '
        + ' bg-white bg-opacity-30 shadow-md'
        + ' border-solid border-[0.5vh] rounded-lg ';
    const divClassBorder = hasActiveCard ? ' border-orange-500 shadow-orange-500 cursor-pointer ' : ' border-gray-500 ';

    const playCard = () => {

        if (!hasActiveCard) {
            return null;
        }

        const card = activeCardKey;
        setActiveCardKey(null);

        const marriage = meldMarriageDecision;
        setMeldMarriageDecision(false);

        axios
            .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {
                move: {data: {card: card, marriage: marriage}, phase: phaseKey}
            })
            .then(() => {})
            .catch(error => {
                console.log(error.response);
                setMessage(error.response.data.message ?? 'Unexpected error', true);
            });
    }

    return (
        <div className={divClassBase + divClassBorder} onClick={playCard}></div>
    );
}
