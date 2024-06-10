import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {CardBack} from "../../../../template/elements/playing-cards/covers/CardBack.jsx";

export const OpponentTricks = ({playerName}) => {

    console.log('OpponentTricks:' + playerName);

    const tricksCount = useThousandStore(state => state.situation.orderedPlayers[playerName].tricks);
    const hasTricks = tricksCount > 0;

    const isCurrentPlayer = playerName === window.MyDramGames.player.name;

    const divClassName = isCurrentPlayer
        ? ' relative  '
        : ' relative -ml-[0.5vh] w-[5vh] sm:w-[10vh] h-[7vh] sm:h-[14vh] ';

    const renderItems = () => {

        const items = [];

        for (let i = 0; i < tricksCount; i++) {

            const style = {
                transform: `rotate(90deg) scale(${isCurrentPlayer ? '0.7' : '1.0'})`,
                top: `${i * 0.1}vh`,
                left: `${i * 0.05}vh`,
            }

            items.push(
                <div className="absolute transform origin-center" style={style} key={i}>
                    <CardBack/>
                </div>
            );
        }

        return items;
    }

    return <>{hasTricks && <div className={divClassName}>{renderItems()}</div>}</>;
}
