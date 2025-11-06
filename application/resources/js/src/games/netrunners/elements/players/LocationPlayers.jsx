import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {Attribute} from "../misc/Attribute.jsx";
import {configNetrunners} from "../../configNetrunners.jsx";

export const LocationPlayers = ({row, column, parentRotation, hasEncounter}) => {

    const locationPlayers = useNetrunnersStore(state => state.situation.map[row][column].players);
    const players = useNetrunnersStore(state => state.situation.players);

    const size = {
        Lg: ((!hasEncounter && locationPlayers.length === 1) ? 6 : 3),
        Sm: ((!hasEncounter && locationPlayers.length === 1) ? 8 : 4),
    };

    const renderPlayers = () => {

        return locationPlayers.map((player, index) => {

            const divStyles = {
                position: 'absolute',
            };

            switch (index + (hasEncounter * 1)) {
                case 0:
                    divStyles.top = '50%';
                    divStyles.left = '50%';
                    divStyles.transform = ' translate(-50%, -50%) ';
                    break;
                case 1:
                    divStyles.top = '1%';
                    divStyles.left = '1%';
                    break;
                case 2:
                    divStyles.top = '1%';
                    divStyles.right = '1%';
                    break;
                case 3:
                    divStyles.bottom = '1%';
                    divStyles.left = '1%';
                    break;
                case 4:
                    divStyles.bottom = '1%';
                    divStyles.right = '1%';
                    break;
                case 5:
                    divStyles.top = '1%';
                    divStyles.left = '50%';
                    divStyles.transform = ' translate(-50%, 0%) ';
                    break;
            }

            const character = players[player].character;

            const attributeStyles = {
                backgroundImage: configNetrunners.characters[character].imageAvatarS,
            };

            const attributeClassnameAdd = configNetrunners.characters[character].classAvatarBorder
                + ' bg-cover bg-center bg-no-repeat ';

            return (
                <div key={index} style={divStyles}>
                    <Attribute className={attributeClassnameAdd} sizeVh={size.Lg} sizeVhSm={size.Sm} additionalStyles={attributeStyles} />
                </div>
            );
        });
    }

    return (
        <div className='relative size-full bg-white/0' style={{transform: ` rotate(${-parentRotation}deg `}}>
            {renderPlayers()}
        </div>
    );
}
