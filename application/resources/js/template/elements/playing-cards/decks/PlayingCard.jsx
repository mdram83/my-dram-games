import React from "react";
import {minicardsSvg} from "./minicardsSvg.jsx";

export const PlayingCard = ({cardKey}) => {

    console.log('call PlayingCard cardKey=', cardKey);

    const getCard = (cardKey) => {
        return minicardsSvg[cardKey];
    }

    return (
        <div>{getCard(cardKey)}</div>
    );
}
