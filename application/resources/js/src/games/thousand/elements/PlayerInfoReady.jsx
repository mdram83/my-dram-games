import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {configThousand} from "../configThousand.jsx";
import {ActionReady} from "./ActionReady.jsx";

export const PlayerInfoReady = () => {

    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseCountingPoints = phaseKey === 'counting-points';

    const isFinished = useThousandStore(state => state.situation.isFinished);

    const playerName = window.MyDramGames.player.name;

    const ready = useThousandStore(state => state.situation.orderedPlayers[playerName].ready);

    const seat = useThousandStore(state => state.situation.orderedPlayers[playerName].seat);
    const borderColorClass = configThousand[seat].borderColorClass;
    const singleClassName = ' flex justify-center items-center py-[2vh] ';

    const renderDistribution = () => {

        if (!isPhaseCountingPoints || isFinished) {
            return <></>;
        }

        return (
            <div
                className={borderColorClass + ' w-full border-[0.4vh] border-solid bg-white rounded-xl bg-opacity-60 px-[2vh] '}>

                <div className={singleClassName}>
                    <span className="font-sans text-[3vh] font-bold text-orange-600 text-center">
                        CONTINUE
                    </span>
                </div>

                <div className={singleClassName}>
                    <ActionReady phase={phaseKey} disabled={ready} />
                </div>

            </div>

        );
    }

    return renderDistribution();
}
