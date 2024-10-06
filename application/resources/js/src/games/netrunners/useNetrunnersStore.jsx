import {create} from "zustand";

export const useNetrunnersStore = create((set, get) => ({

    situation: {},
    setSituation: (situation) => set(() => ({ situation: situation })),

    playerInfoScreen: {
        display: false,
        playerName: undefined,
        characterPriority: false,
    },
    setPlayerInfoScreen: (display, playerName = undefined, characterPriority = false) =>
        set(() => ({ playerInfoScreen: {
            display: display,
            playerName: playerName,
            characterPriority: characterPriority,
        }})),

}));
