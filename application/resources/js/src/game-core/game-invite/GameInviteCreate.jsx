import axios from "axios";
import React from 'react';
import {SiteButton} from "../../../template/components/SiteButton.jsx";
import {FlashMessage} from "../../../template/components/FlashMessage";

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

    const numberOfPlayersOptions = props.gameSetup.numberOfPlayers.map((number, index) => {
        const id = 'numberOfPlayers-' + number;
        return (
            <div className="mx-2 flex items-center" key={'div' + id}>
                <input type="radio" id={id} name="numberOfPlayers" value={number} required defaultChecked={index === 0} />
                <label className="text-white font-medium font-semibold px-2 mb-0" htmlFor={id}>{number} players</label>
            </div>
        );
    });

    const submit = () => {
        toggleButtons(false);
        axios
            .post(window.MyDramGames.routes['ajax.game-invites.store'], {
                options: {
                    numberOfPlayers: document.querySelector('input[name="numberOfPlayers"]:checked').value,
                    autostart: false,
                },
                slug: props.slug,
            })
            .then(response => {
                props.onStore(response.data.gameInvite);
            })
            .catch(error => {
                setErrorMessage(error.response.data.message.message ?? 'Unexpected error');
                toggleButtons(true);
            });
    }

    return (
        <div>

            <h4 className="font-bold text-white font-sans mb-4">Game Settings</h4>

            <div className="flex items-center mb-4">
                <i className="fa fa-users text-white mr-2 content-center"></i>
                {numberOfPlayersOptions}
            </div>

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
