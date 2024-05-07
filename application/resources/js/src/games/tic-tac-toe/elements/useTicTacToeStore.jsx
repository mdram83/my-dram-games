import {create} from "zustand";

export const useTicTacToeStore = create((set, get) => ({

    board: undefined,
    setBoard: (board) => set(() => ({ board: board })),

    moving: false,
    setMoving: (moving) => set(() => ({ moving: moving })),

    winningFields: [],
    setWinningFields: (fields) => set(() => ({winningFields: fields})),

}));
