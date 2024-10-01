import {create} from "zustand";

export const useNetrunnersStore = create((set, get) => ({

    situation: {},
    setSituation: (situation) => set(() => ({ situation: situation })),

}));
