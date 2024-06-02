import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {CardBack} from "../../../../template/elements/playing-cards/covers/CardBack.jsx";

export const StockSection = () => {

    console.log('StockSection');

    const stock = useThousandStore(state => state.situation.stock);
    const phaseKey = useThousandStore(state => state.situation.phase.key);
    const isPhaseBidding = phaseKey === 'bidding';

    const renderItems = () => {
        const items = [];
        for (let i = 0; i < stock; i++) {
            items.push(<div key={i}><CardBack /></div>)
        }
        return items;
    }

    const renderStock = () => {

        if (!isPhaseBidding) {
            return <></>;
        }

        return (
            <div className="grid grid-cols-3 gap-[2vh] sm:gap-[4vh]">
                {renderItems()}
            </div>
        );

    }

    return renderStock();
}
