import React from "react";

export const Menu = ({gameInvite}) => {

    const quitLink = () => window.MyDramGames.routes["game-invites.join"](gameInvite.slug, gameInvite.gameInviteId);

    const renderTitle = (title) => {
        if (title.length === 1) {
            return title;
        }

        const hasManyWords = title.includes(' ');
        const parts = title.split(hasManyWords ? ' ' : '');

        if (hasManyWords) {
            return <>{parts.slice(0, -1).join(' ')} <span className="text-red-500">{parts.slice(-1)}</span></>;
        }

        if (parts.length < 5) {
            return title;
        }

        return <>{parts.slice(0, -2).join('')}<span className="text-red-500">{parts.slice(-2).join('')}</span></>;
    }

    return (

        <div className="flex justify-between items-center w-full h-full text-gray-200 font-sans font-semibold text-[4vh] sm:text-[6vh]">

            <div className="ml-[3vh] sm:ml-[8vh]">{renderTitle(gameInvite.name)}</div>

            <div className="mr-[3vh] sm:mr-[8vh]">
                <a className="text-gray-200" href={quitLink()}><i className="fa fa-power-off"></i></a>
            </div>

        </div>
    );
}
