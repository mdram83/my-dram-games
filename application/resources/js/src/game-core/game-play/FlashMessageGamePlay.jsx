import React from "react";
import {FlashMessage} from "../../../template/components/FlashMessage.jsx";
import {unstable_batchedUpdates} from "react-dom";
import {useGamePlayStore} from "./useGamePlayStore.jsx";

export const FlashMessageGamePlay = () => {

    const content = useGamePlayStore((state) => state.message.content);
    const isError = useGamePlayStore((state) => state.message.isError);
    const timeout = useGamePlayStore((state) => state.message.timeout);

    return (
        <>
            {
                (content !== null) &&
                <FlashMessage message={content}
                              timeoutInSeconds={timeout}
                              isError={isError}
                              onHide={() => unstable_batchedUpdates(() => {
                                  useGamePlayStore.getState().setMessage(null, isError);
                              })} />
            }
        </>
    );
}
