import React from "react";

export const Coordinates = ({row, column, rotation}) => {

    console.log('Coordinates', row, column, rotation);

    const style = {
        transform: `rotate(${-rotation}deg)`,
    };

    return (
        <div className='fixed top-0 left-0 w-full h-full ' style={style}>
            <div className='pl-1 text-lime-500 text-[12px]'>
                {row}.{column}
            </div>
        </div>
    );
}
