import {create} from "zustand";

export const useNetrunnersStore = create((set, get) => ({

    situation: {},
    mapSize: {},
    locationsMap: {},
    yourTurn: false,
    isPhaseCharacterSelection: false,
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
        const activePlayerCoordinates = situation.players[situation.activePlayer].coordinates;

        for (let row = mapSize.startingRow; row < (mapSize.startingRow + mapSize.rows); row++) {

            Object.defineProperty(locationsMap, row, {value: {}});

            for (let column = mapSize.startingColumn; column < (mapSize.startingColumn + mapSize.columns); column++) {

                const isLocation = situation.map[row][column] !== null;
                const hasNode = isLocation && situation.map[row][column].hasOwnProperty('node') && situation.map[row][column].node !== null;
                const nodeRotation = hasNode ? situation.map[row][column].nodeRotation : null;
                const hasEncounter = hasNode && !!situation.map[row][column].encounter;
                const encounter = hasEncounter ? situation.map[row][column].encounter : null;
                const playersCount = (hasNode && situation.map[row][column].hasOwnProperty('players') ? situation.map[row][column].players.length : 0);

                const allowedTargetLocation = isLocation && situation.map[row][column].hasOwnProperty('allowedTargetLocation') && situation.map[row][column].allowedTargetLocation;
                const setDirectionLocation = hasNode && nodeRotation === null;
                const activePlayerChargerLocation = situation.canRecharge && row === activePlayerCoordinates.row && column === activePlayerCoordinates.column;

                const actionableLocation = allowedTargetLocation || setDirectionLocation || activePlayerChargerLocation;
                const actionablePhaseKey = actionableLocation ? situation.phase.key : null;

                Object.defineProperty(locationsMap[row], column, {value: {
                    isLocation: isLocation,
                    hasNode: hasNode,
                    nodeKey: hasNode ? situation.map[row][column].node.key : null,
                    nodeRotation: nodeRotation ?? 0,
                    hasEncounter: hasEncounter,
                    encounter: encounter,
                    playersCount: playersCount,
                    actionableLocation: actionableLocation,
                    yourActionableLocation: actionableLocation && yourTurn,
                    actionablePhaseKey: actionablePhaseKey,
                    activePlayerChargerLocation: activePlayerChargerLocation,
                }});
            }
        }

        const updatedStoreProperties = {
            situation: situation,
            mapSize: mapSize,
            locationsMap: locationsMap,
            yourTurn: yourTurn,
            isPhaseCharacterSelection: situation.phase.key === 'character',
        };

        if (situation.canRecharge && yourTurn) {
            updatedStoreProperties['rechargeInfoScreen'] = {display: situation.players[situation.activePlayer].remainingMoves !== 4}
        }

        if (situation.phase.key === 'finish' && yourTurn) {
            updatedStoreProperties['moveData'] = {payload: {finish: true}, phase: 'finish', label: 'End Turn'}
        }

        return updatedStoreProperties;
    }),

    moveData: {
        payload: {},
        phase: null,
        label: null,
    },
    setMoveData: (moveData) => set(() => ({moveData: moveData})),
    resetMoveData: () => set(() => ({moveData: {payload: {}, phase: null, label: null}})),

    playerInfoScreen: {
        display: false,
        playerName: undefined,
        characterPriority: false,
    },
    setPlayerInfoScreen: (display, playerName = undefined, characterPriority = false) =>
        set(() => {

            const updatedStoreProperties = {
                playerInfoScreen: {
                    display: display,
                    playerName: playerName,
                    characterPriority: characterPriority,
                },
            };

            if (get().situation.canSwitchMapLocation && get().yourTurn) {
                updatedStoreProperties['moveData'] = display
                    ? {payload: {targetPlayerName: playerName}, phase: 'switch', label: 'Switch'}
                    : {payload: {}, phase: null, label: null}
            }

            if (get().rechargeInfoScreen.display) {
                updatedStoreProperties['rechargeInfoScreen'] = {display: false}
            }

            return updatedStoreProperties;
        }),

    rechargeInfoScreen: {
        display: false
    },
    setRechargeInfoScreen: (display) => set(() => ({rechargeInfoScreen: {display: display}})),


    followActivePlayer: true,
    setFollowActivePlayer: (follow) => set(() => ({ followActivePlayer: follow})),

}));
