import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";

export const GameDetails = () => {

    console.log('GameDetails');

    const round = useThousandStore(state => state.situation.round);
    const phase = useThousandStore(state => state.situation.phase);
    const player = useThousandStore(state => state.situation.activePlayer);
    const dealer = useThousandStore(state => state.situation.dealer);
    const obligation = useThousandStore(state => state.situation.obligation);
    const bidWinner = useThousandStore(state => state.situation.bidWinner);
    const bidAmount = useThousandStore(state => state.situation.bidAmount);

    const isPhaseCountPoints = phase.key === 'counting-points';

    const headerClassName = ' text-center font-sans font-semibold text-[2vh] sm:text-[2.4vh] text-gray-800 uppercase ';
    const labelClassName = ' mt-[1vh] font-sans font-bold text-[1.6vh] sm:text-[2vh] text-gray-600 uppercase ';
    const valueClassName = ' font-sans font-semibold text-[1.8vh] sm:text-[2.2vh] text-orange-600 ';
    const ellipsisClassName = ' text-ellipsis overflow-hidden ';
    const wrapClassName = ' text-pretty ';

    return (
        <div className="w-full justify-center mt-[2vh] sm:mt-0 px-0 sm:px-[1vh]">

            <div className={headerClassName}>Round {round}</div>

            <div>
                <div className={labelClassName}>Phase</div>
                <div className={valueClassName + wrapClassName}>{phase.name}</div>
            </div>

            {!isPhaseCountPoints &&
            <div>
                <div className={labelClassName}>Current Move</div>
                <div className={valueClassName + ellipsisClassName}>{player}</div>
            </div>
            }

            <div>
                <div className={labelClassName}>Dealer</div>
                <div className={valueClassName + ellipsisClassName}>{dealer}</div>
            </div>

            <div>
                <div className={labelClassName}>Obligation</div>
                <div className={valueClassName + ellipsisClassName}>{obligation}</div>
            </div>

            {bidWinner &&
            <div>
                <div className={labelClassName}>Bid Result</div>
                <div className={valueClassName + wrapClassName}>{bidWinner} : {bidAmount} points</div>
            </div>
            }


        </div>
    );
}
