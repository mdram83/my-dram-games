import React from "react";
import {useMediaQuery} from "../../../../customHooks/useMediaQuery.jsx";

export const Attribute = ({className, children, sizeVh = 4, sizeVhSm = 7, additionalStyles = {}}) => {

    console.log('Attribute', sizeVh, sizeVhSm);

    const isSm = useMediaQuery('(min-width: 640px)');

    const classDivBase = className
        + ' aspect-square '
        + ' flex justify-center items-center '
        + ' bg-white/50 '
        + ' border-solid rounded-full '
        + ' font-sans font-semibold leading-none ';

    const styles = {
        height: `${isSm ? sizeVhSm : sizeVh}vh`,
        borderWidth: `${isSm ? sizeVhSm * 0.08 : sizeVh * 0.12}vh`,
        fontSize: `${isSm ? sizeVhSm * 0.6 : sizeVh * 0.7}vh`,
        ...additionalStyles,
    }

    return (
        <div className={classDivBase} style={styles}>
            <div className= ' pb-[10%] sm:pb-[6%] '>
                {children}
            </div>
        </div>
    );
}
