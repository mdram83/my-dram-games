import React from "react";
import {minicardsSvg} from "./minicardsSvg.jsx";

export const PlayingCard = ({cardKey}) => {

    console.log('call PlayingCard cardKey=', cardKey);

    const getCard = (cardKey) => {
        return minicardsSvg[cardKey];
    }

    // const svgData = getCard(cardKey);
    // const encodedSvg = `data:image/svg+xml;base64,${btoa(decodeURIComponent(encodeURIComponent(svgData)))}`;

    return (
        <div style={{transform: 'scale(1.25)'}}>{getCard(cardKey)}</div>
    );
}
// <img src={encodedSvg} alt=""/>
// Can't make images working with Draggable in react spring
