import React from "react";
import {minicardsSvg} from "./minicardsSvg.jsx";

export const PlayingCard = ({cardKey, scale = 1}) => {

    console.log('call PlayingCard cardKey=', cardKey);

    const style = {
        transform: `scale(${scale.toString()})`,
    }

    const getCard = (cardKey) => {
        return minicardsSvg[cardKey];
    }

    // const svgData = getCard(cardKey);
    // const encodedSvg = `data:image/svg+xml;base64,${btoa(decodeURIComponent(encodeURIComponent(svgData)))}`;
    // for return:
    // <img src={encodedSvg} alt=""/>
    // Can't make images working with Draggable in react spring, maybe later

    return (
        <div style={style}>{getCard(cardKey)}</div>
    );
}
