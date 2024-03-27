import React from "react";
import {useTicTacToeStore} from "./useTicTacToeStore.jsx";
import {FlashMessage} from "../../../template/components/FlashMessage.jsx";
import {unstable_batchedUpdates} from "react-dom";

export const FlashMessageTicTacToe = () => {

    const content = useTicTacToeStore((state) => state.message.content);
    const isError = useTicTacToeStore((state) => state.message.isError);
    const timeout = useTicTacToeStore((state) => state.message.timeout);

    return (
        <>
            {
                (content !== null) &&
                <FlashMessage message={content}
                              timeoutInSeconds={timeout}
                              isError={isError}
                              onHide={() => unstable_batchedUpdates(() => {
                                  useTicTacToeStore.getState().setMessage(null, isError);
                              })} />
            }
        </>
    );
}
