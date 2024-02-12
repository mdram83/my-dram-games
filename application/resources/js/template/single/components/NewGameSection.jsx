import React from 'react';
import {NewGameForm} from "./NewGameForm.jsx";
import {NewGameSummary} from "./NewGameSummary.jsx";
import {SiteButton} from "../../components/SiteButton.jsx";

export const NewGameSection = ({gameDefinition, currentGame = undefined}) => {

    const [buttonOn, toggleButton] = React.useState(!currentGame);
    const [formOn, toggleForm] = React.useState(false);
    const [gameOn, toggleGame] = React.useState(!!currentGame);
    const [game, setGame] = React.useState(currentGame);

    const enableForm = () => {
        toggleButton(false);
        toggleForm(true);
    }

    const cancelForm = () => {
        toggleForm(false);
        toggleButton(true);
    }

    const showGame = (game) => {
        setGame(game);
        toggleButton(false);
        toggleForm(false);
        toggleGame(true);
    }

    return (
        <>
            {buttonOn && <SiteButton value="New Game" onClick={() => enableForm()} />}
            {formOn && <NewGameForm {...gameDefinition} onCancel={() => cancelForm()} onCreate={(game) => showGame(game)} />}
            {gameOn && <NewGameSummary game={game} slug={gameDefinition.slug} />}
        </>
    );
}
