import React from "react";
import {StockSection} from "./StockSection.jsx";
import {StockRecord} from "./StockRecord.jsx";
import {PlayerInfoBidding} from "./PlayerInfoBidding.jsx";
import {PlayerInfoStockDistribution} from "./PlayerInfoStockDistribution.jsx";
import {PlayerInfoDeclaration} from "./PlayerInfoDeclaration.jsx";

export const TableSection = () => {

    console.log('TableSection');
    const playerName = window.MyDramGames.player.name;

    const commonAbsoluteDivClass = 'absolute bottom-[10vh] -left-[15%] sm:-left-[10%] w-[130%] sm:w-[120%]';
    const commonAbsoluteChildDivClass = 'flex justify-center';
    const commonStandardDivClass = 'flex justify-center items-center';

    return (
        <div className="relative z-10 h-full w-full">

            <div className={commonAbsoluteDivClass}>
                <div className={commonAbsoluteChildDivClass}>
                    <PlayerInfoBidding playerName={playerName}/>
                </div>
            </div>

            <div className={commonAbsoluteDivClass}>
                <div className={commonAbsoluteChildDivClass}>
                    <PlayerInfoStockDistribution/>
                </div>
            </div>

            <div className={commonAbsoluteDivClass}>
                <div className={commonAbsoluteChildDivClass}>
                    <PlayerInfoDeclaration/>
                </div>
            </div>

            <div className={commonStandardDivClass}><StockSection/></div>
            <div className={commonStandardDivClass}><StockRecord/></div>
            <div className={commonStandardDivClass}>Table Goes Here</div>
        </div>

    );
}



