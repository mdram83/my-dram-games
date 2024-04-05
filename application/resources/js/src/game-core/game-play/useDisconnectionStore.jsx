import {create} from "zustand";

export const useDisconnectionStore = create((set, get) => ({

    disconnections: {},

    addDisconnection: (name, timeoutCallback, timeoutSeconds) => {

        if (get().disconnections.hasOwnProperty(name)) {
            get().disconnections[name].forEach((timeoutID) => clearTimeout(timeoutID));
        }

        const timeoutID = setTimeout(timeoutCallback, timeoutSeconds * 1000);

        const disconnections = get().disconnections;
        disconnections[name] = [timeoutID];

        set(() => ({disconnections: disconnections}));
    },

    removeDisconnection: (name) => {

        if (get().disconnections.hasOwnProperty(name)) {
            get().disconnections[name].forEach((timeoutID) => clearTimeout(timeoutID));

            const disconnections = get().disconnections;
            delete disconnections[name];

            set(() => ({disconnections: disconnections}));
        }
    },

}));
