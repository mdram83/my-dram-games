import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {submitMove} from "../../submitMove.jsx";

export const LocationSelection = ({classDivCommon, classDivAction, enabled, row, column, gamePlayId, setMessage}) => {

    console.log('LocationSelection', row, column, enabled);

    const onClick = () => {
        if (!enabled) {
            return;
        }
        submitMove({row: row, column: column}, gamePlayId , setMessage, 'location');
    }

    const style = {
        backgroundImage: configNetrunners.covers.location.imageCoverM,
        filter: 'grayscale(100%)',
    };

    return (
        <div className={classDivAction} onClick={onClick}>
            <div className={classDivCommon} style={style}>

            </div>
        </div>
    );
}
