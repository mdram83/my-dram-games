import React from "react";
import {useEffect} from "react";

export const FlashMessage = ({message, timeoutInSeconds = 0, isError = false, onHide = undefined}) => {

    const divClass = 'fixed bottom-3 right-3 ml-3 opacity-90 py-2 px-4 rounded-md hover:cursor-pointer border z-10' + (isError ? ' bg-red-200' : ' bg-blue-200');
    const pClass = 'p-0 m-0 text-sm font-semibold font-sans';
    const pColor = isError ? 'rgb(127 29 29)' : 'rgb(30 58 138)';

    const [fade, setFade] = React.useState(false);
    const [hidden, setHidden] = React.useState(false);

    const hide = () => {
        setHidden(true);
        if (onHide !== undefined) {
            onHide();
        }
    }

    useEffect(() => {
        if (timeoutInSeconds > 0) {
            setTimeout(() => setFade(true), (timeoutInSeconds * 1000));
        }
    });

    useEffect(() => {
        if (fade) {
            setTimeout(() => hide(), 430);
        }
    }, [fade]);

    return (
        <div className={divClass + (hidden ? ' hidden ' : (fade ? ' animate-fadeout ' : ''))}
             onClick={() => setFade(true)} >

            <p className={pClass} style={{ color: pColor }}>{message}</p>

        </div>
    );
}
