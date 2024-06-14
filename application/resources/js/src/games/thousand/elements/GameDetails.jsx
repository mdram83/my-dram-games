import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {configThousand} from "../configThousand.jsx";

export const GameDetails = () => {

    console.log('GameDetails');

    const round = useThousandStore(state => state.situation.round);
    const phase = useThousandStore(state => state.situation.phase);
    const player = useThousandStore(state => state.situation.activePlayer);
    const dealer = useThousandStore(state => state.situation.dealer);
    const obligation = useThousandStore(state => state.situation.obligation);
    const bidWinner = useThousandStore(state => state.situation.bidWinner);
    const bidAmount = useThousandStore(state => state.situation.bidAmount);
    const orderedPlayers = useThousandStore(state => state.situation.orderedPlayers);

    const isPhaseCountPoints = phase.key === 'counting-points';
    const isFourPlayersGame =  Object.getOwnPropertyNames(orderedPlayers).length === 4;

    const headerClassName = ' text-center font-sans font-semibold text-[2vh] sm:text-[2.4vh] text-gray-800 uppercase ';
    const labelClassName = ' mt-[1.4vh] font-sans font-bold text-[1.4vh] sm:text-[1.6vh] text-gray-400 uppercase ';
    const valueClassName = ' font-sans font-semibold text-[2.0vh] sm:text-[2.2vh] ';
    const valueGenericColor = ' text-gray-800 ';
    const ellipsisClassName = ' text-ellipsis overflow-hidden ';
    const wrapClassName = ' text-pretty ';

    const playerColor = configThousand[orderedPlayers[player].seat].playerTextClass;
    const dealerColor = configThousand[orderedPlayers[dealer].seat].playerTextClass;
    const obligationColor = configThousand[orderedPlayers[obligation].seat].playerTextClass;
    const bidWinnerColor = bidWinner ? configThousand[orderedPlayers[bidWinner].seat].playerTextClass : '';

    return (
        <div className="w-full justify-center mt-[2vh] sm:mt-0 px-0 sm:px-[1vh]">

            <div className={headerClassName}>Round {round}</div>

            <div>
                <div className={labelClassName}>Phase</div>
                <div className={valueClassName + wrapClassName + valueGenericColor}>{phase.name}</div>
            </div>

            {!isPhaseCountPoints &&
            <div>
                <div className={labelClassName}>Current Move</div>
                <div className={valueClassName + ellipsisClassName + playerColor}>{player}</div>
            </div>
            }

            {isFourPlayersGame &&
            <div>
                <div className={labelClassName}>Dealer</div>
                <div className={valueClassName + ellipsisClassName + dealerColor}>{dealer}</div>
            </div>
            }

            <div>
                <div className={labelClassName}>Obligation</div>
                <div className={valueClassName + ellipsisClassName + obligationColor}>{obligation}</div>
            </div>

            {bidWinner &&
            <div>
                <div className={labelClassName}>Bid Result</div>
                <div className={valueClassName + wrapClassName + bidWinnerColor}>{bidWinner} : {bidAmount} points</div>
            </div>
            }


        </div>
    );
}
