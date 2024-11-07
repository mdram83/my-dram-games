import React from "react";

export const Battery = ({points, targetPoints = undefined}) => {

    console.log('Battery', points, targetPoints);

    return (
        <div className='flex items-center justify-center size-full'>

            <div className='grid grid-cols-12 items-center w-[80%] h-[80%]'>

                {/*BLOCK*/}
                <div className='flex items-center justify-center col-span-11 rounded-[1vh] h-full border-[0.5vh] border-solid border-white'>

                    {/*POWER POINTS*/}
                    <div className='grid grid-cols-5 gap-[1.4vh] items-center w-[90%] h-[80%]'>
                        <div className='rounded-[0.5vh] bg-green-500 size-full'></div>
                        <div className='rounded-[0.5vh] bg-green-500 size-full'></div>
                        <div className='rounded-[0.5vh] bg-green-500 size-full'></div>
                        <div className='rounded-[0.5vh] bg-green-500 size-full'></div>
                        <div className='rounded-[0.5vh] bg-green-500 size-full'></div>
                    </div>

                </div>

                {/*CATHODE*/}
                <div className='flex items-center justify-start h-full'>
                    <div className='h-[35%] w-[20%] rounded-r-[0.5vh] border-[0.5vh] border-solid border-white bg-white'></div>
                </div>

            </div>

        </div>
    );
}
