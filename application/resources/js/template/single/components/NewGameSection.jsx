import React from 'react';
import {NewGameForm} from "./NewGameForm.jsx";
import {NewGameSummary} from "./NewGameSummary.jsx";
import {SiteButton} from "../../components/SiteButton.jsx";

export const NewGameSection = ({gameBox, loadedGameInvite = undefined}) => {

    const [buttonOn, toggleButton] = React.useState(!loadedGameInvite);
    const [formOn, toggleForm] = React.useState(false);
    const [gameOn, toggleGame] = React.useState(!!loadedGameInvite);
    const [gameInvite, setGameInvite] = React.useState(loadedGameInvite);

    const enableForm = () => {
        toggleButton(false);
        toggleForm(true);
    }

    const cancelForm = () => {
        toggleForm(false);
        toggleButton(true);
    }

    const showGame = (gameInvite) => {
        setGameInvite(gameInvite);
        toggleButton(false);
        toggleForm(false);
        toggleGame(true);
    }

    return (
        <>
            {buttonOn && <SiteButton value="New Game" onClick={() => enableForm()} />}
            {formOn && <NewGameForm {...gameBox} onCancel={() => cancelForm()} onCreate={(gameInvite) => showGame(gameInvite)} />}
            {gameOn && <NewGameSummary gameInvite={gameInvite} slug={gameBox.slug} />}
        </>
    );
}
