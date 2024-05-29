import React, {useEffect, useRef, useState} from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {PlayingCard} from "../../../../template/elements/playing-cards/decks/PlayingCard.jsx";
import DraggableList from "./common/DraggableList.jsx";

export const PlayerHand = ({playerName}) => {

    console.log(' call PlayerHand');

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);

    const renderElements = () => {
        return hand.map((cardKey) => <PlayingCard key={cardKey} cardKey={cardKey} />);
    }

    const ref = useRef(null);
    const [width, setWidth] = useState(0);

    useEffect(() => {

        const handleResize = () => {
            if (ref.current) {
                setWidth(ref.current.offsetWidth);
            }
        }

        handleResize();

        window.addEventListener('resize', handleResize);

        return () => {
            window.removeEventListener('resize', handleResize);
        };

    }, []);


    return (
        <div ref={ref} className="flex items-center justify-center -mt-[2vh]">
            {width && <DraggableList items={renderElements()} parentWidth={width} />}
        </div>
    );
}
