import {useControls, useTransformEffect} from "react-zoom-pan-pinch";
import React, {useEffect, useState} from "react";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const Controls = () => {

    console.log('Controls');

    const {
        zoomIn,
        zoomOut,
        zoomToElement,
    } = useControls();

    const [scale, setScale] = useState(1);

    const followActivePlayer = useNetrunnersStore(state => state.followActivePlayer);
    const setFollowActivePlayer = useNetrunnersStore(state => state.setFollowActivePlayer);

    const activePlayer = useNetrunnersStore(state => state.situation.activePlayer);
    const activePlayerCoordinates = useNetrunnersStore(state => state.situation.players[activePlayer].coordinates);

    useEffect(() => {
        if (followActivePlayer) {
            setTimeout(() => centerLocation(), 0);
        }
    }, [activePlayerCoordinates]);

    useTransformEffect(({state}) => {
        setScale(state.scale);
    });

    const toggleFollowActivePlayer = () => {
        setFollowActivePlayer(!followActivePlayer);
        if (!followActivePlayer) {
            centerLocation();
        }
    }

    const centerHome = () => {
        centerLocation('100.100', true);
        setFollowActivePlayer(false);
    }

    const centerLocation = (coordinates = undefined, resetScale = false) => {
        if (coordinates === undefined) {
            coordinates = activePlayerCoordinates.row + '.' + activePlayerCoordinates.column;
        }
        zoomToElement(coordinates, resetScale ? 1 : scale);
    };

    const classFollowIcon = 'fa fa-regular fa-eye' + (!followActivePlayer ? '-slash' : '');

    const classnameButton
        = ' w-[6vh] sm:w-[5vh] h-[6vh] sm:h-[5vh] flex items-center justify-center '
        + ' border-[0.2vh] sm:border-[0.15vh] border-solid border-neutral-400 rounded-[1vh]  '
        + ' text-neutral-600 hover:text-black text-[3.5vh] sm:text-[2.5vh] hover:cursor-pointer '
        + ' bg-neutral-200/90 hover:bg-neutral-300/90 shadow-xl shadow-black ';

    return (
        <div className="fixed top-[12vh] sm:top-[14vh] w-fit h-fit right-[2vh] z-20">
            <div className="flex justify-start grid grid-cols-1 gap-3 sm:gap-[0.7vh]">

                <button className={classnameButton + ' pb-[0.5vh] '} onClick={() => zoomIn()}>{'\uFF0B'}</button>
                <button className={classnameButton} onClick={() => zoomOut()}>{'\u2212'}</button>
                <button className={classnameButton} onClick={() => centerHome()}>{'\u2302'}</button>
                <button className={classnameButton} onClick={() => toggleFollowActivePlayer()}><i className={classFollowIcon}></i></button>

            </div>
        </div>
    );
}
