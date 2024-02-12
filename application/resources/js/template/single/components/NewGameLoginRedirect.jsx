import React from 'react';
import {SiteButton} from "../../components/SiteButton.jsx";

export const NewGameLoginRedirect = ({redirectBack = true, backUrl = undefined}) => {

    const backPath = redirectBack ? ('?redirect=' + encodeURIComponent(backUrl ?? window.location.href)) : '';
    const redirectPath = window.MyDramGames.routes['login'] + backPath;
    const loginRedirect = () => window.location.href = redirectPath;

    return <SiteButton value="New Game" onClick={() => loginRedirect()} />;
}
