import React from "react";

export const InputError = ({message}) => {
    return (
        <ul className='text-sm text-red-600 dark:text-red-400 space-y-1 list-none mb-2'>
            <li>{message}</li>
        </ul>
    );
}
