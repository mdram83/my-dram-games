import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";

export const CharacterDetails = ({playerName}) => {

    const characterName = useNetrunnersStore(state => state.situation.players[playerName].character);
    const character = useNetrunnersStore(state => state.situation.characters[characterName]);
    const abilityNames = Object.getOwnPropertyNames(character.abilities);

    const style = {
        backgroundImage: configNetrunners.characters[characterName].imageAvatarM,
    };

    const classDivCharacterName = ' flex items-center justify-center h-[20%] text-[5vh] font-mono lowercase '
        + configNetrunners.characters[characterName].classAvatarText;

    const abilitiesItems = abilityNames.map(name =>
        <div key={name} className='my-[1vh] sm:my-[1.5vh]'>
            {character.abilities[name].name} : {character.abilities[name].description}
        </div>
    );

    return (
        <div className='size-full'>
            <div className='flex items-end justify-center bg-top bg-no-repeat bg-cover w-[100%] h-[80%] rounded-[1vh]'
                 style={style}
            >
                <div className='w-[98%] px-[1%] sm:w-[96%] sm:px-[2%] leading-none bg-neutral-900/90 rounded-b-[0.8vh] text-lime-500 text-[2vh] sm:text-xs font-mono'>
                    {abilitiesItems}
                </div>

            </div>

            <div className={classDivCharacterName}>
                {characterName}
            </div>

        </div>
    );
}
