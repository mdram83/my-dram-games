import React from "react";

export const Rotation = () => {

    console.log('Rotation');

    return (
        <div className='fixed top-0 left-0 w-full h-full text-orange-500'>
            <div className='flex items-center justify-center h-full w-full'>

                <svg xmlns="http://www.w3.org/2000/svg" width="70%" height="70%" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"
                     className="feather feather-rotate-cw">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                </svg>

            </div>
        </div>

    );
}
