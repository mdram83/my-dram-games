import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {ConflictModal} from "./ConflictModal.jsx";

export const ConflictController = () => {

    console.log('ConflictController');

    const isValidPhase = useNetrunnersStore(state => state.isPhaseConflictOrBattle);

    return (
        <>{isValidPhase && <ConflictModal />}</>
    );
}