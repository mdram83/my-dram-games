import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";

export const LocationSelection = ({classDivCommon, classDivAction, row, column, onClick}) => {

    console.log('LocationSelection', row, column);

    const style = {
        backgroundImage: configNetrunners.covers.location.imageCoverM,
    };

    return (
        <div className={classDivAction} onClick={onClick}>
            <div className={classDivCommon} style={style}>
            </div>
        </div>
    );
}
