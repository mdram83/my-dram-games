import React from "react";

export const minicardsSuitsSvg = {

    'S': (
        <svg width="16" height="20" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <g style={{fill: '#000'}}>
                <use xlinkHref="#suit-S" transform="scale(2.0)"></use>
            </g>
        </svg>
    ),

    'H': (
        <svg width="16" height="20" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#suit-H" transform="scale(2)"></use>
            </g>
        </svg>
    ),

    'C': (
        <svg width="16" height="20" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#suit-C" transform="scale(2)"></use>
            </g>
        </svg>
    ),

    'D': (
        <svg width="16" height="20" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#suit-D" transform="scale(2)"></use>
            </g>
        </svg>
    ),
}
