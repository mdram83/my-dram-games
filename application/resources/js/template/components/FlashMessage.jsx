import React from "react";
import {useEffect} from "react";

export const FlashMessage = ({message, timeoutInSeconds = 0, isError = false}) => {

    const divClass = 'fixed bottom-3 right-3 ml-3 opacity-90 py-2 px-4 rounded-md hover:cursor-pointer border z-10' + (isError ? ' bg-red-200' : ' bg-blue-200');
    const pClass = 'p-0 m-0 text-sm font-semibold';
    const pColor = isError ? 'rgb(127 29 29)' : 'rgb(30 58 138)';

    const [fade, setFade] = React.useState(false);
    const [hidden, setHidden] = React.useState(false);

    useEffect(() => {
        if (timeoutInSeconds > 0) {
            setTimeout(function () {
                setFade(true);
            }, (timeoutInSeconds * 1000));
        }
    });

    return (
        <div className={divClass + (hidden ? ' hidden ' : (fade ? ' fade ' : ''))}
             onClick={() => setFade(true)}
             onTransitionEnd={() => setHidden(true)}
        >
            <p className={pClass} style={{ color: pColor }}>
                {message}
            </p>
        </div>
    );
}
