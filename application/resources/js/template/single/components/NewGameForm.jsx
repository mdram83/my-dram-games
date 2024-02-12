import axios from "axios";
import React from 'react';
import {SiteButton} from "../../components/SiteButton.jsx";
import {InputError} from "../../components/InputError.jsx";

export const NewGameForm = (props) => {

    const [createButtonIcon, setCreateButtonIcon] = React.useState('fa-angle-double-right');
    const [createOn, setCreateOn] = React.useState(true);
    const [cancelOn, setCancelOn] = React.useState(true);
    const [errorMessage, setErrorMessage] = React.useState(undefined);

    const toggleButtons = (on) => {
        setCreateOn(on);
        setCancelOn(on);
        setCreateButtonIcon(on ? 'fa-angle-double-right' : 'fa-cog fa-spin');
    }

    const {slug} = props;
    const {numberOfPlayers} = props;
    const numberOfPlayersOptions = numberOfPlayers.map((number, index) => {
        const id = 'numberOfPlayers-' + number;
        return (
            <div className="mx-2 flex items-center" key={'div' + id}>
                <input type="radio" id={id} name="numberOfPlayers" value={number} key={'radio' + id} required defaultChecked={index === 0} />
                <label className="text-white font-medium font-semibold px-2 mb-0" htmlFor={id} key={'label' + id} >{number} players</label>
            </div>
        );
    });

    const submit = () => {
        toggleButtons(false);
        axios
            .post(window.MyDramGames.routes['ajax.game-invites.store'], {
                numberOfPlayers: document.querySelector('input[name="numberOfPlayers"]:checked').value,
                slug: slug,
            })
            .then(response => {
                props.onCreate(response.data.game);
            })
            .catch(error => {
                setErrorMessage(
                    error.response.status === 400
                        ? 'Something went wrong. ' + (error.response.data.message ?? '')
                        : 'Sorry, something went wrong'
                );
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

            {(errorMessage !== undefined) && <InputError message={errorMessage} />}

            <div className="w-full sm:w-auto flex justify-center sm:justify-start">
                <SiteButton value="Create" onClick={() => submit()} className="mr-2" faClassName={createButtonIcon} disabled={!createOn} />
                <SiteButton value="Cancel" onClick={props.onCancel} faClassName="fa-times" disabled={!cancelOn}  />
            </div>

        </div>
    );
}
