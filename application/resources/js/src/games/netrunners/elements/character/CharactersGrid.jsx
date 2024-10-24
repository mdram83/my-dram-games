import React, {useEffect} from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {CharactersGridItem} from "./CharactersGridItem.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";

export const CharactersGrid = () => {

    console.log('players/CharactersGrid');

    const phaseKey = useNetrunnersStore(state => state.situation.phase.key);
    const isPhaseCharacterSelection = phaseKey === 'character';

    const [fade, setFade] = React.useState(false);
    const [hidden, setHidden] = React.useState(false);

    useEffect(() => {
        if (!isPhaseCharacterSelection) {
            setTimeout(() => setFade(true), configNetrunners.engine.phaseFadeTimeout);
            setTimeout(() => setHidden(true), configNetrunners.engine.phaseFadeTimeout + 430);
        }
    }, [isPhaseCharacterSelection]);

    const gridItems = (new Array(6).fill(null)).map((_, index) =>
        <div key={index} className='flex items-center justify-center'>
            <CharactersGridItem gridKey={index} />
        </div>
    );

    const classDiv = ' w-full h-full flex justify-center ';

    return (
        <div className={classDiv + (hidden ? ' hidden ' : (fade ? ' animate-fadeout ' : ''))}>
            <div className='grid grid-cols-2 sm:grid-cols-3 h-[88%] sm:h-full w-full sm:w-[60%]'>
                {gridItems}
            </div>
        </div>
    );
}
