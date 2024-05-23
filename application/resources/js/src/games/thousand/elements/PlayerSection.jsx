import React from "react";
import {Avatar} from "./Avatar.jsx";

export const PlayerSection = () => {

    console.log('call PlayerSection');

    // TODO continue here, adding bidding info and actions

    return (
        <div className="flex items-center h-full">
            <Avatar playerName={window.MyDramGames.player.name} />
        </div>
    );
}
