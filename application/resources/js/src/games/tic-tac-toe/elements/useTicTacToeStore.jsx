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

    winningFields: [],
    setWinningFields: (fields) => set(() => ({winningFields: fields})),

}));
