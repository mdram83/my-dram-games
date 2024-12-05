import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {ConflictModal} from "./ConflictModal.jsx";

export const ConflictController = () => {

    console.log('ConflictController');

    const isPhaseConflict = useNetrunnersStore(state => state.isPhaseConflict);
    const isPhaseBattle = useNetrunnersStore(state => state.isPhaseBattle);

    return (
        <>{(isPhaseConflict || isPhaseBattle) && <ConflictModal />}</>
    );
}
