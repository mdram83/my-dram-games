import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {configThousand} from "../configThousand.jsx";

export const GameResult = () => {

    console.log('GameResult');

    const orderedPlayers = useThousandStore(state => state.situation.orderedPlayers);
    const winner        = useThousandStore(state => state.situation.result.winnerName);
    const forfeited = useThousandStore(state => state.situation.result.forfeitedName);

    const hasWinner = !!winner;
    const hasForfeited = !!forfeited;

    const winnerColor = hasWinner ? configThousand[orderedPlayers[winner].seat].playerTextClass : '';
    const forfeitedColor = hasForfeited ? configThousand[orderedPlayers[forfeited].seat].playerTextClass : '';

    const headerClassName = ' text-center font-sans font-semibold text-[2vh] sm:text-[2.4vh] text-gray-800 uppercase ';
    const labelClassName = ' mt-[1.4vh] font-sans font-bold text-[1.4vh] sm:text-[1.6vh] text-gray-400 uppercase ';
    const valueClassName = ' font-sans font-semibold text-[2.0vh] sm:text-[2.2vh] ';
    const wrapClassName = ' text-pretty ';

    return (
        <div className="w-full justify-center mt-[2vh] sm:mt-0 px-0 sm:px-[1vh]">

            <div className={headerClassName}>Game Result</div>

            {hasWinner &&
                <div>
                    <div className={labelClassName}>Congratulations!</div>
                    <div className={valueClassName + wrapClassName}>
                        <span className={winnerColor}>{winner}</span> wins the game
                    </div>
                </div>
            }

            {hasForfeited &&
                <div>
                    <div className={labelClassName}>Game Over</div>
                    <div className={valueClassName + wrapClassName}>
                        <span className={forfeitedColor}>{forfeited}</span> disconnect over agreed time limit
                    </div>
                </div>
            }

        </div>
    );
}
