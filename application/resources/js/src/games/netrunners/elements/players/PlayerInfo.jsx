import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const PlayerInfo = () => {

    console.log('player/PlayerInfo');

    const display = useNetrunnersStore(state => state.playerInfoScreen.display);
    const playerName = useNetrunnersStore(state => state.playerInfoScreen.playerName);
    const characterPriority = useNetrunnersStore(state => state.playerInfoScreen.characterPriority);

    // fadeout
    // subelementy generowane w zaleznosci od parametrow
    // sprawdzanie rozdzielczosci live


    return (
        <>
            {
                display &&
                <div className='h-[56vh] w-[40vh] sm:w-[80vh] mt-[7vh] sm:mt-[9vh] p-[1vh] border border-solid border-red-500 text-white rounded-[2vh] sm:rounded-[2vh]'>
                    PlayerInfo container {playerName},
                    Character: {(characterPriority ? 'Y' : 'N')}
                    <p>Example paragraph</p>
                    <p>Example paragraph</p>
                    <p>Example paragraph</p>
                    <p>Example paragraph</p>
                    <p>Example paragraph</p>
                    <p>Example paragraph</p>
                    <p>Example paragraph</p>
                    <p>Example paragraph</p>
                </div>
            }
        </>
    );
}
