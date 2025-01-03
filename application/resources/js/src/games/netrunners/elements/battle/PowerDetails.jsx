import React from "react";

export const PowerDetails = ({power, addClass = ' text-[5vh] '}) => {

    const classDiv = ' flex h-full aspect-square items-center justify-center font-sans font-semibold '
        + (power > 0 ? ' text-green-600 ' : ' text-neutral-600 ');

    return <div className={classDiv + addClass}>{power}</div>;
}
