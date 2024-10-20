import {create} from "zustand";

export const useNetrunnersStore = create((set, get) => ({

    situation: {},
    mapSize: {
        rows: 0,
        columns: 0,
        startingRow: undefined,
        startingColumn: undefined,
    },
    setSituation: (situation) => set(() => ({
        situation: situation,
        mapSize: {
            rows: Object.keys(situation.map).length,
            columns: Object.keys(situation.map[100]).length,
            startingRow: parseInt(Object.keys(situation.map).find(() => true)),
            startingColumn: parseInt(Object.keys(situation.map[100]).find(() => true)),
        }
    })),

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
