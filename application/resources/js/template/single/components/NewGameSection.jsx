import React from 'react';
import {NewGameForm} from "./NewGameForm.jsx";
import {NewGameSummary} from "./NewGameSummary.jsx";
import {SiteButton} from "../../components/SiteButton.jsx";

export const NewGameSection = ({gameDefinition, storeUrl, joinUrlBase}) => {

    const [buttonOn, toggleButton] = React.useState(true);
    const [formOn, toggleForm] = React.useState(false);
    const [gameOn, toggleGame] = React.useState(false);
    const [game, setGame] = React.useState(undefined);

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
            {formOn && <NewGameForm {...gameDefinition} storeUrl={storeUrl} onCancel={() => cancelForm()} onCreate={(game) => showGame(game)} />}
            {gameOn && <NewGameSummary game={game} joinUrlBase={joinUrlBase} />}
        </>
    );
}
