import {create} from "zustand";

export const useNetrunnersStore = create((set, get) => ({

    situation: {},
    setSituation: (situation) => set(() => ({ situation: situation })),

    playerDetails: {
        playerName: undefined,
        playerMode: false,
    },
    setPlayerDetails: (playerName, playerMode) =>
        set(() => ({ playerDetails: {
            playerName: playerName,
            playerMode: playerMode
        }})),

}));
