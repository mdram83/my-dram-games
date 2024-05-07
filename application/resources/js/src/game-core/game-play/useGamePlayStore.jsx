import {create} from "zustand";

export const useGamePlayStore = create((set, get) => ({

    gamePlayId: undefined,
    setGamePlayId: (gamePlayId) => {
        if (get().gamePlayId === undefined) {
            set(() => ({gamePlayId: gamePlayId}));
        }
    },

    players: {},
    setPlayer: (playerName, status) => {

        const updatedPlayer = {};
        updatedPlayer[playerName] = status;

        set((state) => ({ players: {...state.players, ...updatedPlayer} }));
    },

    activePlayer: undefined,
    setActivePlayer: (playerName) => set(() => ({ activePlayer: playerName })),

    message: {
        content: null,
        isError: false,
        timeout: 3
    },
    setMessage: (content, isError = false, timeout = 3) => set(() => ({ message: {
            content: content,
            isError: isError,
            timeout: timeout,
        }})),

    isFinished: false,
    setFinished: () => set(() => ({isFinished: true, activePlayer: undefined})),

    disconnections: {},
    addDisconnection: (name, timeoutCallback, timeoutSeconds) => {

        if (get().disconnections.hasOwnProperty(name)) {
            get().disconnections[name].forEach((timeoutID) => clearTimeout(timeoutID));
        }

        const timeoutID = setTimeout(timeoutCallback, timeoutSeconds * 1000);

        const disconnections = get().disconnections;
        disconnections[name] = [timeoutID];

        set(() => ({disconnections: disconnections}));
    },
    removeDisconnection: (name) => {

        if (get().disconnections.hasOwnProperty(name)) {
            get().disconnections[name].forEach((timeoutID) => clearTimeout(timeoutID));

            const disconnections = get().disconnections;
            delete disconnections[name];

            set(() => ({disconnections: disconnections}));
        }
    },

}));
