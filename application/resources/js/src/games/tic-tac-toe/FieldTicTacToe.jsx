import React from "react";

export const FieldTicTacToe = ({fieldKey, fieldValue}) => {

    const fieldBaseClass = ' w-[17.5vh] h-[17.5vh] flex justify-center items-center font-semibold text-[8vh] text-neutral-700';
    const borderBaseClass = ' border-solid border-neutral-500 ';

    const borderCombinedClass = (key) => {

        const horizontalBorder = ((key <= 6) ? ' border-b-[0.75vh] ' : ' border-b-0 ') + ' border-t-0 ';
        const verticalBorder = ((key % 3 > 0) ? ' border-r-[0.75vh] ' : ' border-r-0 ') + ' border-l-0 ';

        return horizontalBorder + verticalBorder + borderBaseClass;
    }

    return <div className={fieldBaseClass + borderCombinedClass(fieldKey)}>{fieldValue}</div>;
}
