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
        const locationsMap = {};
        const yourTurn = MyDramGames.player.name === situation.activePlayer;

        for (let row = mapSize.startingRow; row < (mapSize.startingRow + mapSize.rows); row++) {

            Object.defineProperty(locationsMap, row, {value: {}});

            for (let column = mapSize.startingColumn; column < (mapSize.startingColumn + mapSize.columns); column++) {

                const isLocation = situation.map[row][column] !== null;
                const hasNode = isLocation && situation.map[row][column].hasOwnProperty('node') && situation.map[row][column].node !== null;
                const nodeRotation = hasNode ? situation.map[row][column].nodeRotation : null;

                const allowedTargetLocation = isLocation && situation.map[row][column].hasOwnProperty('allowedTargetLocation') && situation.map[row][column].allowedTargetLocation;
                const setDirectionLocation = hasNode && nodeRotation === null;

                const actionableLocation = allowedTargetLocation || setDirectionLocation;
                const actionablePhaseKey = actionableLocation ? situation.phase.key : null;

                Object.defineProperty(locationsMap[row], column, {value: {
                    isLocation: isLocation,
                    hasNode: hasNode,
                    nodeKey: hasNode ? situation.map[row][column].node.key : null,
                    nodeRotation: nodeRotation ?? 0,
                    actionableLocation: actionableLocation,
                    yourActionableLocation: actionableLocation && yourTurn,
                    actionablePhaseKey: actionablePhaseKey,
                }});
            }
        }

        return {
            situation: situation,
            mapSize: mapSize,
            locationsMap: locationsMap,
        };
    }),

    moveData: {
        payload: {},
        phase: null,
    },
    setMoveData: (moveData) => set(() => ({moveData: moveData})),

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

    followActivePlayer: true,
    setFollowActivePlayer: (follow) => set(() => ({ followActivePlayer: follow})),

}));
