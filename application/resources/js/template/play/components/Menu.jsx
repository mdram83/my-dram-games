import React from "react";

export const Menu = ({gameInvite}) => {

    const renderTitle = (title) => {
        if (title.length === 1) {
            return title;
        }

        const hasManyWords = title.includes(' ');
        const parts = title.split(hasManyWords ? ' ' : '');

        if (hasManyWords) {
            return <>{parts.slice(0, -1).join(' ')} <span className="text-red-600">{parts.slice(-1)}</span></>;
        }

        if (parts.length < 5) {
            return title;
        }

        return <>{parts.slice(0, -2).join('')}<span className="text-red-600">{parts.slice(-2).join('')}</span></>;
    }

    return (

        <div className="flex justify-between items-center w-full h-full text-gray-200 font-sans font-semibold text-2xl sm:text-4xl">

            <div className="ml-6 sm:ml-12">{renderTitle(gameInvite.name)}</div>

            <div className="mr-6 sm:mr-12">

                <a className="text-gray-200"
                   href={window.MyDramGames.routes["game-invites.join"](gameInvite.slug, gameInvite.gameInviteId)}
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"
                         className="feather feather-power"
                    >
                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                        <line x1="12" y1="2" x2="12" y2="12"></line>
                    </svg>
                </a>

            </div>

        </div>
    );
}
