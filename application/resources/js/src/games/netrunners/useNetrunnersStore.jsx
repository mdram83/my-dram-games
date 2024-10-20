import {create} from "zustand";

export const useNetrunnersStore = create((set, get) => ({

    situation: {},
    mapSize: {},
    locationsMap: {},
    setSituation: (situation) => set(() => {

        const rows = Object.keys(situation.map);
        const columns = Object.keys(situation.map[100]);
        const mapSize = {
            rows: rows.length,
            columns: columns.length,
            startingRow: parseInt(rows.find(() => true)),
            startingColumn: parseInt(columns.find(() => true)),
        };

        let locationsMap = {};
        for (let row = mapSize.startingRow; row < (mapSize.startingRow + mapSize.rows); row++) {

            Object.defineProperty(locationsMap, row, {value: {}});

            for (let column = mapSize.startingColumn; column < (mapSize.startingColumn + mapSize.columns); column++) {

                const isLocation = situation.map[row][column] !== null;
                const cellContent = {
                    isLocation: isLocation,
                    hasNode: isLocation && situation.map[row][column].hasOwnProperty('node') && situation.map[row][column].node !== null,
                    allowedTargetLocation: isLocation && situation.map[row][column].hasOwnProperty('allowedTargetLocation') && situation.map[row][column].allowedTargetLocation,
                };
                Object.defineProperty(locationsMap[row], column, {value: cellContent});
            }
        }

        return {
            situation: situation,
            mapSize: mapSize,
            locationsMap: locationsMap,
        };
    }),

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
