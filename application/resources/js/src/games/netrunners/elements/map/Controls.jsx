import {useControls} from "react-zoom-pan-pinch";
import React from "react";

export const Controls = () => {

    const {zoomIn, zoomOut, resetTransform} = useControls();

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
                <button className={classnameButton} onClick={() => resetTransform()}>{'\u21BA'}</button>

            </div>
        </div>
    );
}
