import {create} from "zustand";

export const useThousandStore = create((set, get) => ({

    situation: {},
    setSituation: (situation) => set(() => ({ situation: situation })),

    isFourPlayersGame: false,
    setIsFourPlayersGame: (isFourPlayersGame) => set(() => ({ isFourPlayersGame: isFourPlayersGame})),

    isPlayerFourPlayersDealer: false,
    setIsPlayerFourPlayersDealer: (value) => set(() => ({ isPlayerFourPlayersDealer: value })),

    activeCardKey: null,
    setActiveCardKey: (activeCardKey) => set(() => ({ activeCardKey: activeCardKey })),

    stockDistribution: {},
    setStockDistribution: (playerName, cardKey) => {

        const updatedPlayer = {};
        updatedPlayer[playerName] = cardKey;

        set((state) => ({ stockDistribution: {...state.stockDistribution, ...updatedPlayer} }));
    },
    clearStockDistribution: () => set(() => ({ stockDistribution: {} })),

    meldMarriageDecision: false,
    setMeldMarriageDecision: (decision) => set(() => ({ meldMarriageDecision: decision})),

}));
