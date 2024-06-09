import React, {useEffect, useState} from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {configThousand} from "../configThousand.jsx";
import {ActionDeclare} from "./ActionDeclare.jsx";

export const PlayerInfoDeclaration = () => {

    console.log('PlayerInfoDeclaration');

    // TODO when setting state in Thousand store, check if bidding phase and save it to dedicated isPhaseStockDistribution store value (will not change with each phase and limit rerenders)
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseDeclaration = phaseKey === 'declaration';

    const playerName = window.MyDramGames.player.name;
    const activePlayer = useGamePlayStore(state => state.activePlayer);
    const isActivePlayer = playerName === activePlayer;

    const bidAmount = useThousandStore(state => state.situation.bidAmount);
    const [declaration, setDeclaration] = useState(bidAmount);

    const updateDeclaration = (e) => {
        setDeclaration(e.target.value);
    }

    const seat = useThousandStore(state => state.situation.orderedPlayers[playerName].seat);
    const borderColorClass = configThousand[seat].borderColorClass;
    const singleClassName = ' flex justify-center items-center py-[2vh] ';

    useEffect(() => {
        setDeclaration(bidAmount);
    }, [bidAmount]);

    const renderDeclaration = () => {

        if (!isPhaseDeclaration || !isActivePlayer) {
            return <></>;
        }

        return (
            <div
                className={borderColorClass + ' w-full border-[0.4vh] border-solid bg-white rounded-xl bg-opacity-60 px-[2vh] '}>

                <div className={singleClassName}>
                    <span className="font-sans text-[3vh] font-bold text-orange-600 text-center">
                        DECLARATION: {declaration}
                    </span>
                </div>

                <div className={singleClassName}>
                    <input type="range" min={bidAmount} max="300" step="10" onChange={updateDeclaration} className='w-full'/>
                </div>

                <div className={singleClassName}>
                    <ActionDeclare phase={phaseKey} declaration={declaration}/>
                </div>

                <div className={singleClassName}>
                    <ActionDeclare phase={phaseKey} declaration={0}/>
                </div>

            </div>

        );
    }

    return renderDeclaration();
}
