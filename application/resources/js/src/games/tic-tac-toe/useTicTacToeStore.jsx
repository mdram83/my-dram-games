import {create} from "zustand";

export const useTicTacToeStore = create((set) => ({

    activePlayer: undefined,
    setActivePlayer: (playerName) => set((state) => ({ activePlayer: playerName})),

    board: undefined,
    setBoard: (board) => set((state) => ({ board: board})),

    // TODO add connection status of both players (starting false)

}));
