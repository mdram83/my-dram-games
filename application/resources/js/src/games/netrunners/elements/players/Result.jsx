import React from "react";
import {ResultDetails} from "./ResultDetails.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const Result = () => {

    const isFinished = useNetrunnersStore(state => state.situation.isFinished);

    return isFinished && <ResultDetails />;
}
