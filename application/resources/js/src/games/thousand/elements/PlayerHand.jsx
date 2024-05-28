import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {PlayingCard} from "../../../../template/elements/playing-cards/decks/PlayingCard.jsx";
import DraggableList from "./common/DraggableList.jsx";

export const PlayerHand = ({playerName}) => {

    console.log(' call PlayerHand');

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);

    // TODO redo and use DraggableList example from react spring
    const renderElements = () => {
        return hand.map((cardKey) => <PlayingCard key={cardKey} cardKey={cardKey} />);
    }

    return (
        <div className="flex items-center">
            <DraggableList items={renderElements()} parentWidth={300} />

        </div>
    );
}

// {renderElements()}
