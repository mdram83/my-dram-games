import {create} from "zustand";

export const useTicTacToeStore = create((set) => ({

    gamePlayId: undefined,
    // TODO adjust below method so it is not getting overwritten (can only be set once)
    setGamePlayId: (gamePlayId) => set((state) => ({ gamePlayId: gamePlayId })),

    activePlayer: undefined,
    setActivePlayer: (playerName) => set((state) => ({ activePlayer: playerName })),

    board: undefined,
    setBoard: (board) => set((state) => ({ board: board })),

    // TODO add connection status of both players (starting false)

}));
