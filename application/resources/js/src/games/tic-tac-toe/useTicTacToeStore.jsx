import {create} from "zustand";

export const useTicTacToeStore = create((set, get) => ({

    gamePlayId: undefined,
    setGamePlayId: (gamePlayId) => {
        if (get().gamePlayId === undefined) {
            set((state) => ({gamePlayId: gamePlayId}));
        }
    },

    activePlayer: undefined,
    setActivePlayer: (playerName) => set((state) => ({ activePlayer: playerName })),

    board: undefined,
    setBoard: (board) => set((state) => ({ board: board })),

    moving: false,
    setMoving: (moving) => set((state) => ({ moving: moving })),

    errorMessage: undefined,
    setErrorMessage: (message) => set((state) => ({ errorMessage: message})),

    // TODO add connection status of both players (starting false)

}));
