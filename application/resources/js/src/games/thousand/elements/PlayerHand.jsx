import React from "react";
import {useThousandStore} from "../useThousandStore.jsx";
import {PlayingCard} from "../../../../template/elements/playing-cards/decks/PlayingCard.jsx";

export const PlayerHand = ({playerName}) => {

    console.log(' call PlayerHand');

    const hand = useThousandStore(state => state.situation.orderedPlayers[playerName].hand);

    console.log(hand);

    // FIXME I dont understand why do I need to add empty cell to grid to keep it in one row...
    // TODO anyway I should now add Spring and sortable elements instead. Let's see how it works then (Maybe without a grid, depends how Spring handles that)
    const gridClassName = 'grid grid-cols-' + (hand.length + 1).toString();

    const renderElements = () => {
        const elements = [];
        hand.forEach((cardKey) => elements.push(<PlayingCard key={cardKey} cardKey={cardKey} />));
        // return hand.map((cardKey) => <PlayingCard key={cardKey} cardKey={cardKey} />);
        return elements;
    }

    return (
        <div className={'flex ' + gridClassName}>{renderElements()}</div>
    );
}
