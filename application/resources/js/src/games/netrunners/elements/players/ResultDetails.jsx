import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";

export const ResultDetails = () => {

    const result = useNetrunnersStore(state => state.situation.result);
    const players = useNetrunnersStore(state => state.situation.players);

    const renderResult = () => {
        return result.data.map((detail) => <div key={detail.player}>{renderPlayer(detail.player, detail.position, detail.score)}</div>);
    }

    const renderPlayer = (playerName, position, score) => {

        const styles = {
            backgroundImage: configNetrunners.characters[players[playerName].character].imageAvatarS,
        };

        const classAvatar = configNetrunners.characters[players[playerName].character].classAvatarBorder
            + ' basis-1/6 my-[1vh] bg-bottom bg-no-repeat bg-cover rounded-full aspect-square '
            + (playerName === MyDramGames.player.name ? ' border-[0.5vh] border-solid ' : ' border-[0.4vh] border-dotted ');

        const classData = configNetrunners.characters[players[playerName].character].classAvatarText
            + ' flex items-center justify-center h-[20%] text-[5vh] font-mono ';

        return (
            <div className='flex flex-row items-center justify-center'>
                <div className={'basis-1/6' + classData}>{position}</div>
                <div className={classAvatar} style={styles}></div>
                <div className={'basis-2/3' + classData}>{score} points</div>
            </div>
        )
    }

    const classDivContainer =
        ' h-[70vh] w-[50vh] mt-[2vh] shadow-xl shadow-black text-white '
        + ' border-[0.3vh] sm:border-[0.25vh] border-solid border-fuchsia-500 rounded-[2vh] ';

    return (
        <div className={classDivContainer}>

            {/*TOP BAR WITH STATUS INFO*/}
            <div className='flex bg-neutral-900 rounded-t-[2vh] text-[2vh] font-mono'>
                <div className='grow p-[1vh] text-lime-500 uppercase flex justify-center'>
                    GameResult
                </div>
            </div>

            {/*DETAILS SECTION*/}
            <div className='flex grid grid-rows-5 gap-0 w-full h-[65.5vh] bg-neutral-800/95 rounded-b-[2vh]'>
                {renderResult()}
            </div>

        </div>
    );
}
