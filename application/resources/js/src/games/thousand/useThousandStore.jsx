import {create} from "zustand";

export const useThousandStore = create((set, get) => ({

    situation: {},
    setSituation: (situation) => set(() => ({ situation: situation })),

}));
