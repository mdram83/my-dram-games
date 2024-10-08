import React from 'react';
import {GameInviteCreate} from "./GameInviteCreate.jsx";
import {GameInviteShow} from "./GameInviteShow.jsx";
import {SiteButton} from "../../../template/components/SiteButton.jsx";

export const GameInviteControls = ({gameBox, loadedGameInvite = undefined, gamePlayId = undefined, gameRecords = undefined}) => {

    const [buttonCreateOn, setButtonCreateOn] = React.useState(!loadedGameInvite);
    const [formCreateOn, setFormCreateOn] = React.useState(false);
    const [gameInviteShowOn, setGameInviteShowOn] = React.useState(!!loadedGameInvite);
    const [gameInvite, setGameInvite] = React.useState(loadedGameInvite);

    const isPlayerPremium = MyDramGames.player.premium;
    const isGamePremium = gameBox.isPremium;
    const premiumPass = (!isGamePremium ? true : (isPlayerPremium));

    const enableForm = () => {
        setButtonCreateOn(false);
        setFormCreateOn(true);
    }

    const cancelForm = () => {
        setFormCreateOn(false);
        setButtonCreateOn(true);
    }

    const showGameInvite = (gameInvite) => {
        setGameInvite(gameInvite);
        setButtonCreateOn(false);
        setFormCreateOn(false);
        setGameInviteShowOn(true);
    }

    return (
        <>
            {buttonCreateOn && premiumPass && <SiteButton value="New Game" onClick={() => enableForm()} />}

            {formCreateOn && <GameInviteCreate {...gameBox}
                                               onCancel={() => cancelForm()}
                                               onStore={(gameInvite) => showGameInvite(gameInvite)}
            />}

            {gameInviteShowOn && <GameInviteShow gameInvite={gameInvite}
                                                 slug={gameBox.slug}
                                                 gamePlayId={gamePlayId}
                                                 gameRecords={gameRecords} />}
        </>
    );
}
