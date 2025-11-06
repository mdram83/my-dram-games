import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {EncounterDetails} from "./EncounterDetails.jsx";

export const EncounterSelection = () => {

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const drawnEncounters = useNetrunnersStore(state => state.situation.drawnEncounters);
    const yourTurn = useNetrunnersStore(state => state.yourTurn);
    const display = drawnEncounters.length > 0;

    const onClick = (index) => {
        submitMove({encounter: index}, gamePlayId , setMessage, 'encounter');
    }

    const classDivContainer =
        ' h-[70vh] w-[50vh] mt-[2vh] shadow-xl shadow-black text-white '
        + ' border-[0.3vh] sm:border-[0.25vh] border-solid border-fuchsia-500 rounded-[2vh] '
        + (drawnEncounters.length === 2 ? ' sm:w-[100vh] ' : ' ');

    const classEncounter =
        ' justify-items-center p-[4%] col-span-2 '
        + (drawnEncounters.length === 2 ? ' sm:col-span-1 ' : ' ');

    const classDetails = (drawnEncounters.length === 2 ? ' max-w-[50%] aspect-square sm:max-w-full ' : ' ');

    const renderEncounters = () => drawnEncounters.map((encounter, index) =>
        <EncounterDetails
            classEncounter={classEncounter}
            classDetails={classDetails}
            key={index}
            action={yourTurn ? () => onClick(index) : undefined}
            itemKey={encounter.key}
            name={encounter.name}
            itemType={encounter.itemType}
            description={encounter.description}
            power={encounter.power}
        />
    );

    return (
        <>
            {
                display &&
                <div className={classDivContainer}>

                    {/*TOP BAR WITH STATUS INFO*/}
                    <div className='flex bg-neutral-900 rounded-t-[2vh] text-[2vh] font-mono'>
                        <div className='grow p-[1vh] text-lime-500 uppercase flex justify-center'>
                            Confirm encounter selection
                        </div>
                    </div>

                    {/*DETAILS SECTION*/}
                    <div className='flex grid grid-cols-2 gap-0 w-full h-[65.5vh] bg-neutral-800/95 rounded-b-[2vh]'>
                        {renderEncounters()}
                    </div>

                </div>
            }
        </>
    );
}
