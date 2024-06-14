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

    const headerClassName = 'text-center font-sans font-semibold text-[2vh] sm:text-[2.4vh] text-gray-800 uppercase';
    const labelClassName = 'font-sans font-bold text-[1.6vh] sm:text-[2vh] text-gray-600 uppercase';

    return (
        <div className="w-full justify-center mt-[2vh] sm:mt-0">

            <div className={headerClassName}>Round {round}</div>

            <div>
                <div className={labelClassName}>Phase</div>
                <div>{phase.name}</div>
            </div>
            <div>
                <div className={labelClassName}>Current Move</div>
                <div>{player}</div>
            </div>
            <div>
                <div className={labelClassName}>Dealer</div>
                <div>{dealer}</div>
            </div>
            <div>
                <div className={labelClassName}>Obligation</div>
                <div>{obligation}</div>
            </div>
            <div>
                <div className={labelClassName}>Bid Result</div>
                <div>{bidWinner} : {bidAmount}</div>
            </div>
        </div>
    );
}
