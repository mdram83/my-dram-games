import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {StockSection} from "./StockSection.jsx";

export const TableSection = () => {

    console.log('call TableSection');

    // const table = useThousandStore(state => state.situation.table);



    return (
        <div>
            <div><StockSection /></div>
            <div>Table goes here</div>
        </div>
    );
}
