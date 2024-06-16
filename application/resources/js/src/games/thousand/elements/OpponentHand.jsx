import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {CardBack} from "../../../../template/elements/playing-cards/covers/CardBack.jsx";

export const OpponentHand = ({playerName}) => {

    const handCount = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);

    const angleStep = 6;
    const angleOffset = (handCount % 2) ? 0 : (angleStep / 2);
    const startPosition = -Math.round(handCount / 2) * angleStep + angleStep - angleOffset;

    const renderItems = () => {
        const items = [];
        for (let i = 0; i < handCount; i++) {
            const style = {
                transform: `rotate(${startPosition + (i * angleStep)}deg)`,
            }
            items.push(<div className="absolute transform origin-bottom" style={style} key={i}><CardBack /></div>)
        }
        return items;
    }

    return (
        <div className="relative -mt-[2vh] w-[5vh] sm:w-[10vh] h-[7vh] sm:h-[14vh]">
            {renderItems()}
        </div>
    );
}
