// https://commons.wikimedia.org/wiki/Category:SVG_playing_cards_5

import React from "react";

export const minicardsSvg = {

    // Spades

    '2-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-2" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 4 l -2 2 h -4 v 2 h 6 v 2 h -8 v -4 l 2 -2 h 4 v -4 h -4 v 2 h -2 v -2 z"></path>
            </symbol>
            <symbol id="index-2-S" width="57" height="88">
                <use xlinkHref="#rank-2" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>

            <g style={{ fill: '#000'}}>
                <use xlinkHref="#index-2-S"></use>
                <use xlinkHref="#index-2-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(22.5, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="rotate(180, 28.5, 44) translate(22.5, 11) scale(1.5)"></use>
            </g>
        </svg>
    ),
    '3-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-3" width="8" height="12">
                <path
                    d="M 2 0 h 4 l 2 2 v 8 l -2 2 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -3 h -2 v -2 h 2 v -3 h -4 v 2 h -2 v -2 z"></path>
            </symbol>
            <symbol id="index-3-S" width="57" height="88">
                <use xlinkHref="#rank-3" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>

            <g style={{fill: '#000'}}>
                <use xlinkHref="#index-3-S"></use>
                <use xlinkHref="#index-3-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(22.5, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="translate(22.5, 36.5) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="rotate(180, 28.5, 44) translate(22.5, 11) scale(1.5)"></use>
            </g>
        </svg>
    ),
    '4-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-4" width="8" height="12">
                <path d="M 5 0 h 2 v 6 h 1 v 2 h -1 v 4 h -2 v -4 h -5 v -2 z m 0 3 l -3 3 h 3 z"></path>
            </symbol>
            <symbol id="index-4-S" width="57" height="88">
                <use xlinkHref="#rank-4" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>

            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-4-S"></use>
                <use xlinkHref="#index-4-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="translate(29, 11) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-S" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '5-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-5" width="8" height="12">
                <path d="M 0 0 h 8 v 2 h -6 v 2 h 4 l 2 2 v 4 l -2 2 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -4 h -6 z"></path>
            </symbol>
            <symbol id="index-5-S" width="57" height="88">
                <use xlinkHref="#rank-5" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>

            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-5-S"></use>
                <use xlinkHref="#index-5-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="translate(29, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="translate(22.5, 36.5) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-S" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '6-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-6" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 2 h -2 v -2 h -4 v 3 h 4 l 2 2 v 3 l -2 2 h -4 l -2 -2 v -8 z m 0 7 v 3 h 4 v -3 z"></path>
            </symbol>
            <symbol id="index-6-S" width="57" height="88">
                <use xlinkHref="#rank-6" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>

            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-6-S"></use>
                <use xlinkHref="#index-6-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="translate(29, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="translate(16, 36.5) scale(1.5)"></use>
                <use xlinkHref="#suit-S" transform="translate(29, 36.5) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-S" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '7-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-7" width="8" height="12">
                <path d="M 0 0 h 8 v 4 l -3 3 v 5 h -2 v -6 l 3 -3 v -1 h -6 z"></path>
            </symbol>
            <symbol id="index-7-S" width="57" height="88">
                <use xlinkHref="#rank-7" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-7-S"></use>
                <use xlinkHref="#index-7-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-S" transform="translate(31.4, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-S" transform="translate(23.7, 24.5) scale(1.2)"></use>
                <use xlinkHref="#suit-S" transform="translate(16, 38) scale(1.2)"></use>
                <use xlinkHref="#suit-S" transform="translate(31.4, 38) scale(1.2)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-S" transform="translate(31.4, 11) scale(1.2)"></use>
                </g>
            </g>
        </svg>
    ),
    '8-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-8" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 3 l -1 1 l 1 1 v 3 l -2 2 h -4 l -2 -2 v -3 l 1 -1 l -1 -1 v -3 z m 0 2 v 2 l 1 1 h 2 l 1 -1 v -2 z m 0 6 v 2 h 4 v -2 l -1 -1 h -2 z"></path>
            </symbol>
            <symbol id="index-8-S" width="57" height="88">
                <use xlinkHref="#rank-8" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-8-S"></use>
                <use xlinkHref="#index-8-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-S" transform="translate(31.4, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-S" transform="translate(23.7, 24.5) scale(1.2)"></use>
                <use xlinkHref="#suit-S" transform="translate(16, 38) scale(1.2)"></use>
                <use xlinkHref="#suit-S" transform="translate(31.4, 38) scale(1.2)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-S" transform="translate(16, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-S" transform="translate(31.4, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit" transform="translate(23.7, 24.5) scale(1.2)"></use>
                </g>
            </g>
        </svg>
    ),
    '9-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-9" width="8" height="12">
                <path d="M 6 12 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -3 h -4 l -2 -2 v -3 l 2 -2 h 4 l 2 2 v 8 z m 0 -7 v -3 h -4 v 3 z"></path>
            </symbol>
            <symbol id="index-9-S" width="57" height="88">
                <use xlinkHref="#rank-9" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-9-S"></use>
                <use xlinkHref="#index-9-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(17, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-S" transform="translate(32.8, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-S" transform="translate(17, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-S" transform="translate(32.8, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-S" transform="translate(24.9, 39.5) scale(0.9)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-S" transform="translate(17, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-S" transform="translate(32.8, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-S" transform="translate(32.8, 30) scale(0.9)"></use>
                    <use xlinkHref="#suit-S" transform="translate(17, 30) scale(0.9)"></use>
                </g>
            </g>
        </svg>
    ),
    '10-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-10" width="10" height="12">
                <rect width="2" height="12"></rect>
                <path d="M 6 0 h 2 l 2 2 v 8 l -2 2 h -2 l -2 -2 v -8 z m 0 2 v 8 h 2 v -8 z"></path>
            </symbol>
            <symbol id="index-10-S" width="57" height="88">
                <use xlinkHref="#rank-10" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-10-S"></use>
                <use xlinkHref="#index-10-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-S" transform="translate(17, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-S" transform="translate(32.8, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-S" transform="translate(24.9, 20.5) scale(0.9)"></use>
                <use xlinkHref="#suit-S" transform="translate(17, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-S" transform="translate(32.8, 30) scale(0.9)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-S" transform="translate(17, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-S" transform="translate(32.8, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-S" transform="translate(24.9, 20.5) scale(0.9)"></use>
                    <use xlinkHref="#suit-S" transform="translate(32.8, 30) scale(0.9)"></use>
                    <use xlinkHref="#suit-S" transform="translate(17, 30) scale(0.9)"></use>
                </g>
            </g>
        </svg>
    ),
    'J-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-J" width="8" height="12">
                <path d="M 0 0 h 8 v 10 l -2 2 h -4 l -2 -2 v -3 h 2 v 3 h 4 v -8 h -6 z"></path>
            </symbol>
            <symbol id="special-J" width="10" height="12">
                <path d="M 4 0 h 1 v 2 l 2 -2 l 1 2 v 3 l -1 2 l -2 -2 v 7 h -1 v -8 h -2 v -1 h 2 z"></path>
            </symbol>
            <symbol id="index-J-S" width="57" height="88">
                <use xlinkHref="#rank-J" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-J-S"></use>
                <use xlinkHref="#index-J-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-J" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'Q-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-Q" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 6 l -1 1 l 1 1 v 2 h -2 v -2 h -4 l -2 -2 v -6 z m 0 2 v 6 h 4 v -6 z"></path>
            </symbol>
            <symbol id="special-Q" width="10" height="12">
                <path d="M 1 0 l 2 3 l 2 -3 l 2 3 l 2 -3 v 10 h -8 z"></path>
                <rect x="1" y="11" width="8" height="1"></rect>
            </symbol>
            <symbol id="index-Q-S" width="57" height="88">
                <use xlinkHref="#rank-Q" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-Q-S"></use>
                <use xlinkHref="#index-Q-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-Q" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'K-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-K" width="8" height="12">
                <path d="M 0 0 h 2 v 5 l 4 -5 h 2 l -4 5 l 4 7 h -2 l -3 -5 h -1 v 5 h -2 z"></path>
            </symbol>
            <symbol id="special-K" width="10" height="12">
                <path d="M 1 2 l 2 3 l 2 -3 l 2 3 l 2 -3 v 7 h -8 z"></path>
                <path id="crown-dot" d="M 1 0 l 1 1 l -1 1 l -1 -1 z"></path>
                <use xlinkHref="#crown-dot" transform="translate(4, 0)"></use>
                <use xlinkHref="#crown-dot" transform="translate(8, 0)"></use>
                <rect x="1" y="10" width="8" height="2"></rect>
            </symbol>
            <symbol id="index-K-S" width="57" height="88">
                <use xlinkHref="#rank-K" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-K-S"></use>
                <use xlinkHref="#index-K-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-K" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'A-S': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-S" width="8" height="10">
                <path d="M 4 0 l 4 5 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-A" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 10 h -2 v -5 h -4 v 5 h -2 v -10 z m 0 2 v 3 h 4 v -3 z"></path>
            </symbol>
            <symbol id="index-A-S" width="57" height="88">
                <use xlinkHref="#rank-A" x="6" y="6"></use>
                <use xlinkHref="#suit-S" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-A-S"></use>
                <use xlinkHref="#index-A-S" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit" transform="translate(20.5, 34) scale(2)"></use>
            </g>
        </svg>
    ),

    // Hearts

    '2-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-2" width="8" height="12">
            <path
                    d="M 2 0 h 4 l 2 2 v 4 l -2 2 h -4 v 2 h 6 v 2 h -8 v -4 l 2 -2 h 4 v -4 h -4 v 2 h -2 v -2 z"></path>
            </symbol>
            <symbol id="index-2-H" width="57" height="88">
                <use xlinkHref="#rank-2" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00'}}>
                <use xlinkHref="#index-2-H"></use>
                <use xlinkHref="#index-2-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(22.5, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="rotate(180, 28.5, 44) translate(22.5, 11) scale(1.5)"></use>
            </g>
        </svg>
    ),
    '3-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-3" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 8 l -2 2 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -3 h -2 v -2 h 2 v -3 h -4 v 2 h -2 v -2 z"></path>
            </symbol>
            <symbol id="index-3-H" width="57" height="88">
                <use xlinkHref="#rank-3" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-3-H"></use>
                <use xlinkHref="#index-3-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(22.5, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="translate(22.5, 36.5) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="rotate(180, 28.5, 44) translate(22.5, 11) scale(1.5)"></use>
            </g>
        </svg>
    ),
    '4-H' : (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-4" width="8" height="12">
                <path d="M 5 0 h 2 v 6 h 1 v 2 h -1 v 4 h -2 v -4 h -5 v -2 z m 0 3 l -3 3 h 3 z"></path>
            </symbol>
            <symbol id="index-4-H" width="57" height="88">
                <use xlinkHref="#rank-4" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-4-H"></use>
                <use xlinkHref="#index-4-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="translate(29, 11) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-H" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '5-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-5" width="8" height="12">
                <path d="M 0 0 h 8 v 2 h -6 v 2 h 4 l 2 2 v 4 l -2 2 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -4 h -6 z"></path>
            </symbol>
            <symbol id="index-5-H" width="57" height="88">
                <use xlinkHref="#rank-5" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-5-H"></use>
                <use xlinkHref="#index-5-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="translate(29, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="translate(22.5, 36.5) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-H" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '6-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-6" width="8" height="12">
                <path
                    d="M 2 0 h 4 l 2 2 v 2 h -2 v -2 h -4 v 3 h 4 l 2 2 v 3 l -2 2 h -4 l -2 -2 v -8 z m 0 7 v 3 h 4 v -3 z"></path>
            </symbol>
            <symbol id="index-6-H" width="57" height="88">
                <use xlinkHref="#rank-6" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-6-H"></use>
                <use xlinkHref="#index-6-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="translate(29, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="translate(16, 36.5) scale(1.5)"></use>
                <use xlinkHref="#suit-H" transform="translate(29, 36.5) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-H" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '7-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-7" width="8" height="12">
                <path d="M 0 0 h 8 v 4 l -3 3 v 5 h -2 v -6 l 3 -3 v -1 h -6 z"></path>
            </symbol>
            <symbol id="index-7-H" width="57" height="88">
                <use xlinkHref="#rank-7" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-7-H"></use>
                <use xlinkHref="#index-7-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-H" transform="translate(31.4, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-H" transform="translate(23.7, 24.5) scale(1.2)"></use>
                <use xlinkHref="#suit-H" transform="translate(16, 38) scale(1.2)"></use>
                <use xlinkHref="#suit-H" transform="translate(31.4, 38) scale(1.2)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-H" transform="translate(31.4, 11) scale(1.2)"></use>
                </g>
            </g>
        </svg>
    ),
    '8-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-8" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 3 l -1 1 l 1 1 v 3 l -2 2 h -4 l -2 -2 v -3 l 1 -1 l -1 -1 v -3 z m 0 2 v 2 l 1 1 h 2 l 1 -1 v -2 z m 0 6 v 2 h 4 v -2 l -1 -1 h -2 z"></path>
            </symbol>
            <symbol id="index-8-H" width="57" height="88">
                <use xlinkHref="#rank-8" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-8-H"></use>
                <use xlinkHref="#index-8-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-H" transform="translate(31.4, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-H" transform="translate(23.7, 24.5) scale(1.2)"></use>
                <use xlinkHref="#suit-H" transform="translate(16, 38) scale(1.2)"></use>
                <use xlinkHref="#suit-H" transform="translate(31.4, 38) scale(1.2)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-H" transform="translate(16, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-H" transform="translate(31.4, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-H" transform="translate(23.7, 24.5) scale(1.2)"></use>
                </g>
            </g>
        </svg>
    ),
    '9-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-9" width="8" height="12">
                <path d="M 6 12 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -3 h -4 l -2 -2 v -3 l 2 -2 h 4 l 2 2 v 8 z m 0 -7 v -3 h -4 v 3 z"></path>
            </symbol>
            <symbol id="index-9-H" width="57" height="88">
                <use xlinkHref="#rank-9" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-9-H"></use>
                <use xlinkHref="#index-9-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(17, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-H" transform="translate(32.8, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-H" transform="translate(17, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-H" transform="translate(32.8, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-H" transform="translate(24.9, 39.5) scale(0.9)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-H" transform="translate(17, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-H" transform="translate(32.8, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-H" transform="translate(32.8, 30) scale(0.9)"></use>
                    <use xlinkHref="#suit-H" transform="translate(17, 30) scale(0.9)"></use>
                </g>
            </g>
        </svg>
    ),
    '10-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-10" width="10" height="12">
                <rect width="2" height="12"></rect>
                <path d="M 6 0 h 2 l 2 2 v 8 l -2 2 h -2 l -2 -2 v -8 z m 0 2 v 8 h 2 v -8 z"></path>
            </symbol>
            <symbol id="index-10-H" width="57" height="88">
                <use xlinkHref="#rank-10" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-10-H"></use>
                <use xlinkHref="#index-10-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(17, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-H" transform="translate(32.8, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-H" transform="translate(24.9, 20.5) scale(0.9)"></use>
                <use xlinkHref="#suit-H" transform="translate(17, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-H" transform="translate(32.8, 30) scale(0.9)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-H" transform="translate(17, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-H" transform="translate(32.8, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-H" transform="translate(24.9, 20.5) scale(0.9)"></use>
                    <use xlinkHref="#suit-H" transform="translate(32.8, 30) scale(0.9)"></use>
                    <use xlinkHref="#suit-H" transform="translate(17, 30) scale(0.9)"></use>
                </g>
            </g>
        </svg>
    ),
    'J-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-J" width="8" height="12">
                <path d="M 0 0 h 8 v 10 l -2 2 h -4 l -2 -2 v -3 h 2 v 3 h 4 v -8 h -6 z"></path>
            </symbol>
            <symbol id="special-J" width="10" height="12">
                <path d="M 4 0 h 1 v 2 l 2 -2 l 1 2 v 3 l -1 2 l -2 -2 v 7 h -1 v -8 h -2 v -1 h 2 z"></path>
            </symbol>
            <symbol id="index-J-H" width="57" height="88">
                <use xlinkHref="#rank-J" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-J-H"></use>
                <use xlinkHref="#index-J-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-J" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'Q-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-Q" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 6 l -1 1 l 1 1 v 2 h -2 v -2 h -4 l -2 -2 v -6 z m 0 2 v 6 h 4 v -6 z"></path>
            </symbol>
            <symbol id="special-Q" width="10" height="12">
                <path d="M 1 0 l 2 3 l 2 -3 l 2 3 l 2 -3 v 10 h -8 z"></path>
                <rect x="1" y="11" width="8" height="1"></rect>
            </symbol>
            <symbol id="index-Q-H" width="57" height="88">
                <use xlinkHref="#rank-Q" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-Q-H"></use>
                <use xlinkHref="#index-Q-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-Q" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'K-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-K" width="8" height="12">
                <path d="M 0 0 h 2 v 5 l 4 -5 h 2 l -4 5 l 4 7 h -2 l -3 -5 h -1 v 5 h -2 z"></path>
            </symbol>
            <symbol id="special-K" width="10" height="12">
                <path d="M 1 2 l 2 3 l 2 -3 l 2 3 l 2 -3 v 7 h -8 z"></path>
                <path id="crown-dot" d="M 1 0 l 1 1 l -1 1 l -1 -1 z"></path>
                <use xlinkHref="#crown-dot" transform="translate(4, 0)"></use>
                <use xlinkHref="#crown-dot" transform="translate(8, 0)"></use>
                <rect x="1" y="10" width="8" height="2"></rect>
            </symbol>
            <symbol id="index-K-H" width="57" height="88">
                <use xlinkHref="#rank-K" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-K-H"></use>
                <use xlinkHref="#index-K-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-K" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'A-H': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-H" width="8" height="10">
                <path d="M 2 0 l 2 2 l 2 -2 l 2 2 v 3 l -4 5 l -4 -5 v -3 z"></path>
            </symbol>
            <symbol id="rank-A" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 10 h -2 v -5 h -4 v 5 h -2 v -10 z m 0 2 v 3 h 4 v -3 z"></path>
            </symbol>
            <symbol id="index-A-H" width="57" height="88">
                <use xlinkHref="#rank-A" x="6" y="6"></use>
                <use xlinkHref="#suit-H" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-A-H"></use>
                <use xlinkHref="#index-A-H" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-H" transform="translate(20.5, 34) scale(2)"></use>
            </g>
        </svg>
    ),

    // Clubs

    '2-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-2" width="8" height="12">
                <path
                    d="M 2 0 h 4 l 2 2 v 4 l -2 2 h -4 v 2 h 6 v 2 h -8 v -4 l 2 -2 h 4 v -4 h -4 v 2 h -2 v -2 z"></path>
            </symbol>
            <symbol id="index-2-C" width="57" height="88">
                <use xlinkHref="#rank-2" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000'}}>
                <use xlinkHref="#index-2-C"></use>
                <use xlinkHref="#index-2-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(22.5, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="rotate(180, 28.5, 44) translate(22.5, 11) scale(1.5)"></use>
            </g>
        </svg>
    ),
    '3-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-3" width="8" height="12">
                <path
                    d="M 2 0 h 4 l 2 2 v 8 l -2 2 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -3 h -2 v -2 h 2 v -3 h -4 v 2 h -2 v -2 z"></path>
            </symbol>
            <symbol id="index-3-C" width="57" height="88">
                <use xlinkHref="#rank-3" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-3-C"></use>
                <use xlinkHref="#index-3-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(22.5, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="translate(22.5, 36.5) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="rotate(180, 28.5, 44) translate(22.5, 11) scale(1.5)"></use>
            </g>
        </svg>
    ),
    '4-C' : (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-4" width="8" height="12">
                <path d="M 5 0 h 2 v 6 h 1 v 2 h -1 v 4 h -2 v -4 h -5 v -2 z m 0 3 l -3 3 h 3 z"></path>
            </symbol>
            <symbol id="index-4-C" width="57" height="88">
                <use xlinkHref="#rank-4" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-4-C"></use>
                <use xlinkHref="#index-4-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="translate(29, 11) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-C" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '5-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-5" width="8" height="12">
                <path d="M 0 0 h 8 v 2 h -6 v 2 h 4 l 2 2 v 4 l -2 2 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -4 h -6 z"></path>
            </symbol>
            <symbol id="index-5-C" width="57" height="88">
                <use xlinkHref="#rank-5" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-5-C"></use>
                <use xlinkHref="#index-5-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="translate(29, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="translate(22.5, 36.5) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-C" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '6-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-6" width="8" height="12">
                <path
                    d="M 2 0 h 4 l 2 2 v 2 h -2 v -2 h -4 v 3 h 4 l 2 2 v 3 l -2 2 h -4 l -2 -2 v -8 z m 0 7 v 3 h 4 v -3 z"></path>
            </symbol>
            <symbol id="index-6-C" width="57" height="88">
                <use xlinkHref="#rank-6" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-6-C"></use>
                <use xlinkHref="#index-6-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="translate(29, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="translate(16, 36.5) scale(1.5)"></use>
                <use xlinkHref="#suit-C" transform="translate(29, 36.5) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-C" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '7-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-7" width="8" height="12">
                <path d="M 0 0 h 8 v 4 l -3 3 v 5 h -2 v -6 l 3 -3 v -1 h -6 z"></path>
            </symbol>
            <symbol id="index-7-C" width="57" height="88">
                <use xlinkHref="#rank-7" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-7-C"></use>
                <use xlinkHref="#index-7-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-C" transform="translate(31.4, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-C" transform="translate(23.7, 24.5) scale(1.2)"></use>
                <use xlinkHref="#suit-C" transform="translate(16, 38) scale(1.2)"></use>
                <use xlinkHref="#suit-C" transform="translate(31.4, 38) scale(1.2)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-C" transform="translate(31.4, 11) scale(1.2)"></use>
                </g>
            </g>
        </svg>
    ),
    '8-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-8" width="8" height="12">
                <path
                    d="M 2 0 h 4 l 2 2 v 3 l -1 1 l 1 1 v 3 l -2 2 h -4 l -2 -2 v -3 l 1 -1 l -1 -1 v -3 z m 0 2 v 2 l 1 1 h 2 l 1 -1 v -2 z m 0 6 v 2 h 4 v -2 l -1 -1 h -2 z"></path>
            </symbol>
            <symbol id="index-8-C" width="57" height="88">
                <use xlinkHref="#rank-8" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-8-C"></use>
                <use xlinkHref="#index-8-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-C" transform="translate(31.4, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-C" transform="translate(23.7, 24.5) scale(1.2)"></use>
                <use xlinkHref="#suit-C" transform="translate(16, 38) scale(1.2)"></use>
                <use xlinkHref="#suit-C" transform="translate(31.4, 38) scale(1.2)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-C" transform="translate(16, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-C" transform="translate(31.4, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-C" transform="translate(23.7, 24.5) scale(1.2)"></use>
                </g>
            </g>
        </svg>
    ),
    '9-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-9" width="8" height="12">
                <path
                    d="M 6 12 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -3 h -4 l -2 -2 v -3 l 2 -2 h 4 l 2 2 v 8 z m 0 -7 v -3 h -4 v 3 z"></path>
            </symbol>
            <symbol id="index-9-C" width="57" height="88">
                <use xlinkHref="#rank-9" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-9-C"></use>
                <use xlinkHref="#index-9-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(17, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-C" transform="translate(32.8, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-C" transform="translate(17, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-C" transform="translate(32.8, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-C" transform="translate(24.9, 39.5) scale(0.9)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-C" transform="translate(17, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-C" transform="translate(32.8, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-C" transform="translate(32.8, 30) scale(0.9)"></use>
                    <use xlinkHref="#suit-C" transform="translate(17, 30) scale(0.9)"></use>
                </g>
            </g>
        </svg>
    ),
    '10-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-10" width="10" height="12">
                <rect width="2" height="12"></rect>
                <path d="M 6 0 h 2 l 2 2 v 8 l -2 2 h -2 l -2 -2 v -8 z m 0 2 v 8 h 2 v -8 z"></path>
            </symbol>
            <symbol id="index-10-C" width="57" height="88">
                <use xlinkHref="#rank-10" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-10-C"></use>
                <use xlinkHref="#index-10-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(17, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-C" transform="translate(32.8, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-C" transform="translate(24.9, 20.5) scale(0.9)"></use>
                <use xlinkHref="#suit-C" transform="translate(17, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-C" transform="translate(32.8, 30) scale(0.9)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-C" transform="translate(17, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-C" transform="translate(32.8, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-C" transform="translate(24.9, 20.5) scale(0.9)"></use>
                    <use xlinkHref="#suit-C" transform="translate(32.8, 30) scale(0.9)"></use>
                    <use xlinkHref="#suit-C" transform="translate(17, 30) scale(0.9)"></use>
                </g>
            </g>
        </svg>
    ),
    'J-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-J" width="8" height="12">
                <path d="M 0 0 h 8 v 10 l -2 2 h -4 l -2 -2 v -3 h 2 v 3 h 4 v -8 h -6 z"></path>
            </symbol>
            <symbol id="special-J" width="10" height="12">
                <path d="M 4 0 h 1 v 2 l 2 -2 l 1 2 v 3 l -1 2 l -2 -2 v 7 h -1 v -8 h -2 v -1 h 2 z"></path>
            </symbol>
            <symbol id="index-J-C" width="57" height="88">
                <use xlinkHref="#rank-J" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-J-C"></use>
                <use xlinkHref="#index-J-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-J" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'Q-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-Q" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 6 l -1 1 l 1 1 v 2 h -2 v -2 h -4 l -2 -2 v -6 z m 0 2 v 6 h 4 v -6 z"></path>
            </symbol>
            <symbol id="special-Q" width="10" height="12">
                <path d="M 1 0 l 2 3 l 2 -3 l 2 3 l 2 -3 v 10 h -8 z"></path>
                <rect x="1" y="11" width="8" height="1"></rect>
            </symbol>
            <symbol id="index-Q-C" width="57" height="88">
                <use xlinkHref="#rank-Q" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-Q-C"></use>
                <use xlinkHref="#index-Q-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-Q" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'K-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-K" width="8" height="12">
                <path d="M 0 0 h 2 v 5 l 4 -5 h 2 l -4 5 l 4 7 h -2 l -3 -5 h -1 v 5 h -2 z"></path>
            </symbol>
            <symbol id="special-K" width="10" height="12">
                <path d="M 1 2 l 2 3 l 2 -3 l 2 3 l 2 -3 v 7 h -8 z"></path>
                <path id="crown-dot" d="M 1 0 l 1 1 l -1 1 l -1 -1 z"></path>
                <use xlinkHref="#crown-dot" transform="translate(4, 0)"></use>
                <use xlinkHref="#crown-dot" transform="translate(8, 0)"></use>
                <rect x="1" y="10" width="8" height="2"></rect>
            </symbol>
            <symbol id="index-K-C" width="57" height="88">
                <use xlinkHref="#rank-K" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-K-C"></use>
                <use xlinkHref="#index-K-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-K" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'A-C': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-C" width="8" height="10">
                <path
                    d="M 3 0 h 2 l 1 1 v 2 l -1 1 h 2 l 1 1 v 2 l -1 1 h -1 l -1 -1 l 1 3 h -4 l 1 -3 l -1 1 h -1 l -1 -1 v -2 l 1 -1 h 2 l -1 -1 v -2 z"></path>
            </symbol>
            <symbol id="rank-A" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 10 h -2 v -5 h -4 v 5 h -2 v -10 z m 0 2 v 3 h 4 v -3 z"></path>
            </symbol>
            <symbol id="index-A-C" width="57" height="88">
                <use xlinkHref="#rank-A" x="6" y="6"></use>
                <use xlinkHref="#suit-C" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#000' }}>
                <use xlinkHref="#index-A-C"></use>
                <use xlinkHref="#index-A-C" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-C" transform="translate(20.5, 34) scale(2)"></use>
            </g>
        </svg>
    ),

    // Diamonds

    '2-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-2" width="8" height="12">
                <path
                    d="M 2 0 h 4 l 2 2 v 4 l -2 2 h -4 v 2 h 6 v 2 h -8 v -4 l 2 -2 h 4 v -4 h -4 v 2 h -2 v -2 z"></path>
            </symbol>
            <symbol id="index-2-D" width="57" height="88">
                <use xlinkHref="#rank-2" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00'}}>
                <use xlinkHref="#index-2-D"></use>
                <use xlinkHref="#index-2-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(22.5, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="rotate(180, 28.5, 44) translate(22.5, 11) scale(1.5)"></use>
            </g>
        </svg>
    ),
    '3-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-3" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 8 l -2 2 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -3 h -2 v -2 h 2 v -3 h -4 v 2 h -2 v -2 z"></path>
            </symbol>
            <symbol id="index-3-D" width="57" height="88">
                <use xlinkHref="#rank-3" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-3-D"></use>
                <use xlinkHref="#index-3-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(22.5, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="translate(22.5, 36.5) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="rotate(180, 28.5, 44) translate(22.5, 11) scale(1.5)"></use>
            </g>
        </svg>
    ),
    '4-D' : (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-4" width="8" height="12">
                <path d="M 5 0 h 2 v 6 h 1 v 2 h -1 v 4 h -2 v -4 h -5 v -2 z m 0 3 l -3 3 h 3 z"></path>
            </symbol>
            <symbol id="index-4-D" width="57" height="88">
                <use xlinkHref="#rank-4" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-4-D"></use>
                <use xlinkHref="#index-4-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="translate(29, 11) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-D" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '5-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-5" width="8" height="12">
                <path d="M 0 0 h 8 v 2 h -6 v 2 h 4 l 2 2 v 4 l -2 2 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -4 h -6 z"></path>
            </symbol>
            <symbol id="index-5-D" width="57" height="88">
                <use xlinkHref="#rank-5" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-5-D"></use>
                <use xlinkHref="#index-5-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="translate(29, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="translate(22.5, 36.5) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-D" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '6-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-6" width="8" height="12">
                <path
                    d="M 2 0 h 4 l 2 2 v 2 h -2 v -2 h -4 v 3 h 4 l 2 2 v 3 l -2 2 h -4 l -2 -2 v -8 z m 0 7 v 3 h 4 v -3 z"></path>
            </symbol>
            <symbol id="index-6-D" width="57" height="88">
                <use xlinkHref="#rank-6" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-6-D"></use>
                <use xlinkHref="#index-6-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="translate(29, 11) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="translate(16, 36.5) scale(1.5)"></use>
                <use xlinkHref="#suit-D" transform="translate(29, 36.5) scale(1.5)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.5)"></use>
                    <use xlinkHref="#suit-D" transform="translate(29, 11) scale(1.5)"></use>
                </g>
            </g>
        </svg>
    ),
    '7-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-7" width="8" height="12">
                <path d="M 0 0 h 8 v 4 l -3 3 v 5 h -2 v -6 l 3 -3 v -1 h -6 z"></path>
            </symbol>
            <symbol id="index-7-D" width="57" height="88">
                <use xlinkHref="#rank-7" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-7-D"></use>
                <use xlinkHref="#index-7-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-D" transform="translate(31.4, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-D" transform="translate(23.7, 24.5) scale(1.2)"></use>
                <use xlinkHref="#suit-D" transform="translate(16, 38) scale(1.2)"></use>
                <use xlinkHref="#suit-D" transform="translate(31.4, 38) scale(1.2)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-D" transform="translate(31.4, 11) scale(1.2)"></use>
                </g>
            </g>
        </svg>
    ),
    '8-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-8" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 3 l -1 1 l 1 1 v 3 l -2 2 h -4 l -2 -2 v -3 l 1 -1 l -1 -1 v -3 z m 0 2 v 2 l 1 1 h 2 l 1 -1 v -2 z m 0 6 v 2 h 4 v -2 l -1 -1 h -2 z"></path>
            </symbol>
            <symbol id="index-8-D" width="57" height="88">
                <use xlinkHref="#rank-8" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-8-D"></use>
                <use xlinkHref="#index-8-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-D" transform="translate(31.4, 11) scale(1.2)"></use>
                <use xlinkHref="#suit-D" transform="translate(23.7, 24.5) scale(1.2)"></use>
                <use xlinkHref="#suit-D" transform="translate(16, 38) scale(1.2)"></use>
                <use xlinkHref="#suit-D" transform="translate(31.4, 38) scale(1.2)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-D" transform="translate(16, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-D" transform="translate(31.4, 11) scale(1.2)"></use>
                    <use xlinkHref="#suit-D" transform="translate(23.7, 24.5) scale(1.2)"></use>
                </g>
            </g>
        </svg>
    ),
    '9-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-9" width="8" height="12">
                <path d="M 6 12 h -4 l -2 -2 v -2 h 2 v 2 h 4 v -3 h -4 l -2 -2 v -3 l 2 -2 h 4 l 2 2 v 8 z m 0 -7 v -3 h -4 v 3 z"></path>
            </symbol>
            <symbol id="index-9-D" width="57" height="88">
                <use xlinkHref="#rank-9" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-9-D"></use>
                <use xlinkHref="#index-9-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(17, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-D" transform="translate(32.8, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-D" transform="translate(17, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-D" transform="translate(32.8, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-D" transform="translate(24.9, 39.5) scale(0.9)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-D" transform="translate(17, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-D" transform="translate(32.8, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-D" transform="translate(32.8, 30) scale(0.9)"></use>
                    <use xlinkHref="#suit-D" transform="translate(17, 30) scale(0.9)"></use>
                </g>
            </g>
        </svg>
    ),
    '10-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-10" width="10" height="12">
                <rect width="2" height="12"></rect>
                <path d="M 6 0 h 2 l 2 2 v 8 l -2 2 h -2 l -2 -2 v -8 z m 0 2 v 8 h 2 v -8 z"></path>
            </symbol>
            <symbol id="index-10-D" width="57" height="88">
                <use xlinkHref="#rank-10" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-10-D"></use>
                <use xlinkHref="#index-10-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(17, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-D" transform="translate(32.8, 11) scale(0.9)"></use>
                <use xlinkHref="#suit-D" transform="translate(24.9, 20.5) scale(0.9)"></use>
                <use xlinkHref="#suit-D" transform="translate(17, 30) scale(0.9)"></use>
                <use xlinkHref="#suit-D" transform="translate(32.8, 30) scale(0.9)"></use>
                <g transform="rotate(180, 28.5, 44)">
                    <use xlinkHref="#suit-D" transform="translate(17, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-D" transform="translate(32.8, 11) scale(0.9)"></use>
                    <use xlinkHref="#suit-D" transform="translate(24.9, 20.5) scale(0.9)"></use>
                    <use xlinkHref="#suit-D" transform="translate(32.8, 30) scale(0.9)"></use>
                    <use xlinkHref="#suit-D" transform="translate(17, 30) scale(0.9)"></use>
                </g>
            </g>
        </svg>
    ),
    'J-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-J" width="8" height="12">
                <path d="M 0 0 h 8 v 10 l -2 2 h -4 l -2 -2 v -3 h 2 v 3 h 4 v -8 h -6 z"></path>
            </symbol>
            <symbol id="special-J" width="10" height="12">
                <path d="M 4 0 h 1 v 2 l 2 -2 l 1 2 v 3 l -1 2 l -2 -2 v 7 h -1 v -8 h -2 v -1 h 2 z"></path>
            </symbol>
            <symbol id="index-J-D" width="57" height="88">
                <use xlinkHref="#rank-J" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-J-D"></use>
                <use xlinkHref="#index-J-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-J" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'Q-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-Q" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 6 l -1 1 l 1 1 v 2 h -2 v -2 h -4 l -2 -2 v -6 z m 0 2 v 6 h 4 v -6 z"></path>
            </symbol>
            <symbol id="special-Q" width="10" height="12">
                <path d="M 1 0 l 2 3 l 2 -3 l 2 3 l 2 -3 v 10 h -8 z"></path>
                <rect x="1" y="11" width="8" height="1"></rect>
            </symbol>
            <symbol id="index-Q-D" width="57" height="88">
                <use xlinkHref="#rank-Q" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-Q-D"></use>
                <use xlinkHref="#index-Q-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-Q" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'K-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-K" width="8" height="12">
                <path d="M 0 0 h 2 v 5 l 4 -5 h 2 l -4 5 l 4 7 h -2 l -3 -5 h -1 v 5 h -2 z"></path>
            </symbol>
            <symbol id="special-K" width="10" height="12">
                <path d="M 1 2 l 2 3 l 2 -3 l 2 3 l 2 -3 v 7 h -8 z"></path>
                <path id="crown-dot" d="M 1 0 l 1 1 l -1 1 l -1 -1 z"></path>
                <use xlinkHref="#crown-dot" transform="translate(4, 0)"></use>
                <use xlinkHref="#crown-dot" transform="translate(8, 0)"></use>
                <rect x="1" y="10" width="8" height="2"></rect>
            </symbol>
            <symbol id="index-K-D" width="57" height="88">
                <use xlinkHref="#rank-K" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-K-D"></use>
                <use xlinkHref="#index-K-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#special-K" transform="translate(18.5, 32) scale(2)"></use>
            </g>
        </svg>
    ),
    'A-D': (
        <svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink">
            <symbol id="suit-D" width="8" height="10">
                <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
            </symbol>
            <symbol id="rank-A" width="8" height="12">
                <path d="M 2 0 h 4 l 2 2 v 10 h -2 v -5 h -4 v 5 h -2 v -10 z m 0 2 v 3 h 4 v -3 z"></path>
            </symbol>
            <symbol id="index-A-D" width="57" height="88">
                <use xlinkHref="#rank-A" x="6" y="6"></use>
                <use xlinkHref="#suit-D" x="6" y="20"></use>
            </symbol>

            <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
            <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
            <g style={{ fill: '#f00' }}>
                <use xlinkHref="#index-A-D"></use>
                <use xlinkHref="#index-A-D" transform="rotate(180, 28.5, 44)"></use>
                <use xlinkHref="#suit-D" transform="translate(20.5, 34) scale(2)"></use>
            </g>
        </svg>
    ),
    // 'A-D': (
    //     `<svg width="57" height="88" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    //         <symbol id="suit-D" width="8" height="10">
    //             <path d="M 4 0 l 4 5 l -4 5 l -4 -5 z"></path>
    //         </symbol>
    //         <symbol id="rank-A" width="8" height="12">
    //             <path d="M 2 0 h 4 l 2 2 v 10 h -2 v -5 h -4 v 5 h -2 v -10 z m 0 2 v 3 h 4 v -3 z"></path>
    //         </symbol>
    //         <symbol id="index-A-D" width="57" height="88">
    //             <use xlink:href="#rank-A" x="6" y="6"></use>
    //             <use xlink:href="#suit-D" x="6" y="20"></use>
    //         </symbol>
    //
    //         <rect x="1.5" y="1.5" width="54" height="85" fill="#fff"></rect>
    //         <path d="M 2 0 h 53 l 2 2 v 84 l -2 2 h -53 l -2 -2 v -84 z m 0 2 v 84 h 53 v -84 z"></path>
    //         <g style="fill: #f00;">
    //             <use xlink:href="#index-A-D"></use>
    //             <use xlink:href="#index-A-D" transform="rotate(180, 28.5, 44)"></use>
    //             <use xlink:href="#suit-D" transform="translate(20.5, 34) scale(2)"></use>
    //         </g>
    //     </svg>`
    // ),

}
