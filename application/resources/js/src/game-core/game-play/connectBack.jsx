import {unstable_batchedUpdates} from "react-dom";
import {useDisconnectionStore} from "./useDisconnectionStore.jsx";

export const connectBack = (user) => {
    unstable_batchedUpdates(() => useDisconnectionStore.getState().removeDisconnection(user.name));
}
