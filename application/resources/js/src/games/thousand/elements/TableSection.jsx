import React from "react";
import {StockSection} from "./StockSection.jsx";
import {StockRecord} from "./StockRecord.jsx";
import {Table} from "./Table.jsx";

export const TableSection = () => {

    console.log('TableSection');
    const commonStandardDivClass = 'flex justify-center items-center';

    return (
        <div className="relative z-10 h-full w-full">
            <div className={commonStandardDivClass}><StockSection/></div>
            <div className={commonStandardDivClass}><StockRecord/></div>
            <div className={commonStandardDivClass}><Table /></div>
        </div>
    );
}



