import React, {useEffect, useState} from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {a, useSpring} from "@react-spring/web";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const CharacterAvatar = ({characterName, classPartCommon, classPartSize}) => {

    const playerName = useNetrunnersStore(state => state.situation.characters[characterName].player);
    const setPlayerInfoScreen = useNetrunnersStore(state => state.setPlayerInfoScreen);

    const [flipped, setFlipped] = useState(false);
    const { transform, opacity } = useSpring({
        opacity: flipped ? 1 : 0,
        transform: `perspective(50vh) rotateY(${flipped ? 180 : 0}deg)`,
        config: { mass: 10, tension: 500, friction: 80 },
    });

    const classDivAnimated = ' absolute cursor-pointer bg-contain ' + classPartSize + classPartCommon;

    const onClick = () => {
        setPlayerInfoScreen(true, playerName, true);
    }

    useEffect(() => {
        setFlipped(true);
    });

    return (
        <div className={' flex items-center justify-center bg-contain w-full h-full ' + classPartCommon}
             onClick={onClick}
        >
            <a.div
                className={classDivAnimated}
                style={{
                    willChange: 'transform, opacity',
                    opacity: opacity.to((o) => 1 - o), transform,
                    backgroundImage: configNetrunners.covers.character.imageCoverM,
                }}
            />
            <a.div
                className={classDivAnimated}
                style={{
                    opacity,
                    transform,
                    rotateY: "180deg",
                    backgroundImage: configNetrunners.characters[characterName].imageAvatarM,
                }}
            />
        </div>
    );
}
