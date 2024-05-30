import React, {useEffect, useRef, useState} from "react";
import axios from "axios";
import {useGamePlayStore} from "../../../game-core/game-play/useGamePlayStore.jsx";
import {useThousandStore} from "../useThousandStore.jsx";
import {PlayingCard} from "../../../../template/elements/playing-cards/decks/PlayingCard.jsx";
import DraggableList from "./common/DraggableList.jsx";

export const PlayerHand = ({playerName}) => {

    console.log(' call PlayerHand');

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);
    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const actionSortHand = (order, cards) => {

        console.log('call actionSortHand');

        const sortedHand = order.map((targetIndex, index) => cards[targetIndex]);

        console.log('sorted hand:', sortedHand);

        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        sleep(3000).then(() => {
            axios
                .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {
                    move: {data: {hand: sortedHand}, phase: 'sorting'}
                })
                .then(() => {console.log('sorting finished')})
                .catch(error => {
                    console.log(error.response);
                    setMessage(error.response.data.message ?? 'Unexpected error', true);
                });
        });

        // const timeOutedAxios = () =>
        //     axios
        //         .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {
        //             move: {data: {hand: sortedHand}, phase: 'sorting'}
        //         })
        //         .then(() => {console.log('sorting finished')})
        //         .catch(error => {
        //             console.log(error.response);
        //             setMessage(error.response.data.message ?? 'Unexpected error', true);
        //         });
        //
        // setTimeout(timeOutedAxios(), 2000);

        // axios
        //     .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {
        //         move: {data: {hand: sortedHand}, phase: 'sorting'}
        //     })
        //     .then(() => {console.log('sorting finished')})
        //     .catch(error => {
        //         console.log(error.response);
        //         setMessage(error.response.data.message ?? 'Unexpected error', true);
        //     });
    }


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
            {width && <DraggableList items={renderElements()} parentWidth={width} callback={actionSortHand} />}
        </div>
    );
}
