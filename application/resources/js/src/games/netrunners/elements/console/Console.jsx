import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {useTypewriter} from "../../../../customHooks/useTypewriter.jsx";

export const Console = () => {

    const activePlayer = useNetrunnersStore(state => state.situation.activePlayer);
    const remainingMoves = useNetrunnersStore(state => state.situation.players[activePlayer].remainingMoves);
    const remainingNodes = useNetrunnersStore(state => state.situation.remainingNodes);
    const remainingEncounters = useNetrunnersStore(state => state.situation.remainingEncounters);

    const text = `  ${activePlayer} | Moves ${remainingMoves} | Nodes ${remainingNodes} | Encounters ${remainingEncounters} `;
    const displayText = useTypewriter(text, 10, 1000);

    return (
        <div className='size-full p-[1vh] bg-neutral-900 leading-tight sm:leading-tight font-mono text-lime-500 text-[2.5vh] sm:text-[2.5vh]'>
            <div> --- campaign stats ---</div>
            <div>{displayText}</div>
            <div className='hidden sm:flex'>>_</div>
        </div>
    );
}
