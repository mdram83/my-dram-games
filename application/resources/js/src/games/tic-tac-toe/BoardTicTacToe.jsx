import React from "react";
import {FieldTicTacToe} from "./FieldTicTacToe";
import {configTicTacToe} from "./configTicTacToe.jsx";
import {useTicTacToeStore} from "./useTicTacToeStore.jsx";

export const BoardTicTacToe = () => {

    const board = useTicTacToeStore((state) => state.board);

    const renderFields = (board) => {
        const fields = [];
        for (const [fieldKey, fieldValue] of Object.entries(board)) {
            fields.push(
                <FieldTicTacToe fieldKey={fieldKey}
                                fieldValue={fieldValue === null ? fieldValue : configTicTacToe[fieldValue].avatar}
                                key={fieldKey} />
            );
        }
        return fields;
    }

    return (
        <div className="flex justify-center items-center w-full h-full">
            <div className="grid grid-cols-3">

                {renderFields(board)}

            </div>
        </div>
    );
}
