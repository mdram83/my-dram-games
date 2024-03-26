import {create} from "zustand";

export const useTicTacToeStore = create((set, get) => ({

    gamePlayId: undefined,
    setGamePlayId: (gamePlayId) => {
        if (get().gamePlayId === undefined) {
            set(() => ({gamePlayId: gamePlayId}));
        }
    },

    activePlayer: undefined,
    setActivePlayer: (playerName) => set(() => ({ activePlayer: playerName })),

    board: undefined,
    setBoard: (board) => set(() => ({ board: board })),

    moving: false,
    setMoving: (moving) => set(() => ({ moving: moving })),

    errorMessage: undefined,
    setErrorMessage: (message) => set(() => ({ errorMessage: message})),

}));
