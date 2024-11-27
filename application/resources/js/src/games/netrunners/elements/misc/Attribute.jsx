import React from "react";

export const Attribute = ({className, children, sizeVh = 4, sizeVhSm = 7}) => {

    console.log('Attribute', sizeVh, sizeVhSm);

    const sizeLg = Math.round(sizeVh).toString();
    const sizeSm = Math.round(sizeVhSm).toString();
    const borderLg = (sizeVh * 0.12).toPrecision(1);
    const borderSm = (sizeVhSm * 0.16).toPrecision(1);
    const textLg = (sizeVh * 0.65).toPrecision(1);
    const textSm = (sizeVhSm * 0.5).toPrecision(1);

    const classDivFromSize = ' aspect-square '
        + ` h-[${sizeLg}vh] border-[${borderLg}vh] text-[${textLg}vh] `
        + ` sm:h-[${sizeSm}vh] sm:border-[${borderSm}vh] sm:text-[${textSm}vh] `;

    const classDivBase = className + classDivFromSize
        + ' flex justify-center items-center '
        + ' bg-white/50 '
        + ' border border-solid rounded-full border-orange-500 '
        + ' text-orange-500 font-sans font-semibold leading-none ';

    console.log(classDivBase);

    const classContent = ' pb-[10%] sm:pb-[6%] ';

    return (
        <div className={classDivBase}>
            <div className={classContent}>
                {children}
            </div>
        </div>
    );
}
