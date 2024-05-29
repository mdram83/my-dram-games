import React, {useEffect, useRef, useState} from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {PlayingCard} from "../../../../template/elements/playing-cards/decks/PlayingCard.jsx";
import DraggableList from "./common/DraggableList.jsx";

export const PlayerHand = ({playerName}) => {

    console.log(' call PlayerHand');

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);

    const cardScale = 1.2;
    const cardBaseWidthPx = 57;
    const cardFinalWidthPx = Math.round(cardScale * cardBaseWidthPx);
    const divMaxWidthPx = Math.round(cardFinalWidthPx * hand.length - cardFinalWidthPx / 2);
    const style = {
        maxWidth: `${divMaxWidthPx.toString()}px`,
    }

    const renderElements = () => {
        return hand.map((cardKey) => <PlayingCard key={cardKey} cardKey={cardKey} scale={cardScale} />);
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

    // TODO remove id=PlayerHand after testing

    return (
        <div ref={ref} id="PlayerHand" className="flex items-center justify-center sm:w-[60%] w-[70%] -mt-[5vh]" style={style}>
            {width && <DraggableList items={renderElements()} parentWidth={width} />}
        </div>
    );
}
