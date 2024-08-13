import axios from "axios";
import React from 'react';
import {SiteButton} from "../../../template/components/SiteButton.jsx";
import {FlashMessage} from "../../../template/components/FlashMessage";
import {GameInviteOptionsHandler} from "./GameInviteOptionsHandler.jsx";

export const GameInviteCreate = (props) => {

    const [buttonCreateIcon, setButtonCreateIcon] = React.useState('fa-angle-double-right');
    const [buttonCreateOn, setButtonCreateOn] = React.useState(true);
    const [buttonCancelOn, setButtonCancelOn] = React.useState(true);
    const [errorMessage, setErrorMessage] = React.useState(undefined);

    const toggleButtons = (on) => {
        setButtonCreateOn(on);
        setButtonCancelOn(on);
        setButtonCreateIcon(on ? 'fa-angle-double-right' : 'fa-cog fa-spin');
    }

    const optionsHandler = new GameInviteOptionsHandler(props.options);

    const submit = () => {
        toggleButtons(false);
        axios
            .post(window.MyDramGames.routes['ajax.game-invites.store'], {
                options: optionsHandler.getOptionsValues(),
                slug: props.slug,
            })
            .then(response => {
                props.onStore(response.data.gameInvite);
                window.history.pushState(
                    {},
                    'Game Lobby',
                    window.MyDramGames.routes["game-invites.join"](props.slug, response.data.gameInvite.id)
                );
            })
            .catch(error => {
                setErrorMessage(error.response.data.message.message ?? 'Unexpected error');
                toggleButtons(true);
            });
    }

    return (
        <div>

            <h4 className="font-bold text-white font-sans mb-4">Game Settings</h4>

            {optionsHandler.getRenderedOptions()}

            {(errorMessage !== undefined) && <FlashMessage message={errorMessage} isError={true} />}

            <div className="w-full sm:w-auto flex justify-center sm:justify-start">

                <SiteButton value="Create"
                            onClick={() => submit()}
                            className="mr-2"
                            faClassName={buttonCreateIcon}
                            disabled={!buttonCreateOn}
                />
                <SiteButton value="Cancel"
                            onClick={props.onCancel}
                            faClassName="fa-times"
                            disabled={!buttonCancelOn}
                />

            </div>

        </div>
    );
}
