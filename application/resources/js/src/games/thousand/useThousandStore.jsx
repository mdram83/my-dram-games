import {create} from "zustand";

export const useThousandStore = create((set, get) => ({

    situation: {},
    setSituation: (situation) => set(() => ({ situation: situation })),

    activeCardKey: null,
    setActiveCardKey: (activeCardKey) => set(() => ({ activeCardKey: activeCardKey })),

    stockDistribution: {},
    setStockDistribution: (playerName, cardKey) => {

        const updatedPlayer = {};
        updatedPlayer[playerName] = cardKey;

        set((state) => ({ stockDistribution: {...state.stockDistribution, ...updatedPlayer} }));
    },

}));
