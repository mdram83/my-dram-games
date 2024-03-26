import {create} from "zustand";

export const usePlayersStatusStore = create((set) => ({

    players: {},

    setPlayer: (playerName, status) => {

        const updatedPlayer = {};
        updatedPlayer[playerName] = status;

        set((state) => ({ players: {...state.players, ...updatedPlayer} }));
    },

}));
