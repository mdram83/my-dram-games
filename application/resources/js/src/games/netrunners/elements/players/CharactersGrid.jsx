import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {GridAvatar} from "./GridAvatar.jsx";

export const CharactersGrid = () => {

    const phaseKey = useNetrunnersStore(state => state.situation.phase.key);
    const isPhaseValid = phaseKey === 'character';

    const gridItems = (new Array(6).fill(null)).map((_, index) => {
        return (
            <div key={index} className=' flex items-center justify-center '>
                <GridAvatar gridKey={index} />
            </div>
        );
    });

    return (
        isPhaseValid &&
        <div className='w-full h-full flex justify-center'>
            <div className='grid grid-cols-2 sm:grid-cols-3 h-[88%] sm:h-full w-full sm:w-[60%]'>
                {gridItems}
            </div>
        </div>
    );
}
