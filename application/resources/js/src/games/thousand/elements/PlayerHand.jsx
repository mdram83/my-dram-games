import React, {useEffect, useRef, useState} from "react";
import axios from "axios";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {useThousandStore} from "../useThousandStore.jsx";
import {PlayingCard} from "../../../../template/elements/playing-cards/decks/PlayingCard.jsx";
import DraggableList from "./common/DraggableList.jsx";

export const PlayerHand = ({playerName}) => {

    console.log('PlayerHand');

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);
    const stockDistribution = useThousandStore(state => state.stockDistribution);

    const stockDistributionCardKeys = [];
    for (const [playerName, stockDistributionCardKey] of Object.entries(stockDistribution)) {
        stockDistributionCardKeys.push(stockDistributionCardKey);
    }
    console.log('SDCKs:', stockDistributionCardKeys);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const actionSortHand = (order, cards) => {
        const sortedHand = order.map((targetIndex) => cards[targetIndex]);
        axios
            .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {
                move: {data: {hand: sortedHand}, phase: 'sorting'}
            })
            .then(() => {})
            .catch(error => {
                console.log(error.response);
                setMessage(error.response.data.message ?? 'Unexpected error', true);
            });
    }

    const cardScale = 1.2;
    const cardBaseWidthPx = 57;
    const cardFinalWidthPx = Math.round(cardScale * cardBaseWidthPx);
    const divMaxWidthPx = Math.round(cardFinalWidthPx * hand.length - cardFinalWidthPx / 2);
    const style = {
        maxWidth: `${divMaxWidthPx.toString()}px`,
    }

    const renderElements = () => hand.map(
        (cardKey) => {
            const blocked = stockDistributionCardKeys.includes(cardKey);
            console.log('blocked:', cardKey, blocked);
            // FIXME although above show true, it is not getting passed as true to below component...
            return <PlayingCard key={cardKey} cardKey={cardKey} scale={cardScale} blocked={blocked}/>;
        }
    );

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
        <div ref={ref} className="flex items-center justify-center sm:w-[60%] w-[70%] -mt-[5vh]" style={style}>
            {width && <DraggableList items={renderElements()} parentWidth={width} callback={actionSortHand} />}
        </div>
    );
}
