import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {CardBack} from "../../../../template/elements/playing-cards/covers/CardBack.jsx";

export const StockSection = () => {

    console.log('call TableSection');

    const stock = useThousandStore(state => state.situation.stock);

    console.log(stock);

    const renderStockElements = () => {

        let list = [];

        for (let i = 1; i <= stock; i++) {
            list.push(<li key={i}><CardBack /></li>);
        }

        console.log(list);

        return list;
    }

    return (
        <div><ul>{renderStockElements()}</ul></div>
    );
}
