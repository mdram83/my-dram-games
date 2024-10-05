import React from "react";
import {configNetrunners} from "../../configNetrunners.jsx";

export const CharacterAvatar = ({characterName, classPartCommon}) => {

    console.log('players/CharacterAvatar', characterName, classPartCommon);

    const style = {
        backgroundImage: configNetrunners.characters[characterName].imageAvatarM,
    }

    const onClick = () => {

    }

    return (
        <div className={' bg-contain w-full h-full ' + classPartCommon}
            style={style}
            onClick={() => onClick()}
        ></div>
    );
}
