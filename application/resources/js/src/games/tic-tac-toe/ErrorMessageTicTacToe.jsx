import React from "react";
import {useTicTacToeStore} from "./useTicTacToeStore.jsx";
import {FlashMessage} from "../../../template/components/FlashMessage.jsx";
import {unstable_batchedUpdates} from "react-dom";

export const ErrorMessageTicTacToe = () => {

    const errorMessage = useTicTacToeStore((state) => state.errorMessage);

    return (
        <>
            {
                (errorMessage !== undefined) &&
                <FlashMessage message={errorMessage}
                              timeoutInSeconds={5}
                              isError={true}
                              onHide={() => unstable_batchedUpdates(() => {
                                  useTicTacToeStore.getState().setErrorMessage(undefined);
                              })} />
            }
        </>
    );
}
