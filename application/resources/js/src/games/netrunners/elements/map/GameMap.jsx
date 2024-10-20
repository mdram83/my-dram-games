import React from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {Location} from "./Location.jsx";

export const GameMap = () => {

    console.log('GameMap');

    const phaseKey = useNetrunnersStore(state => state.situation.phase.key);
    const isPhaseCharacterSelection = phaseKey === 'character';

    const mapSize = {
            rows: useNetrunnersStore(state => state.mapSize.rows),
            columns: useNetrunnersStore(state => state.mapSize.columns),
            startingRow: useNetrunnersStore(state => state.mapSize.startingRow),
            startingColumn: useNetrunnersStore(state => state.mapSize.startingColumn),
    };

    const styleGrid = {
        gridTemplateColumns: `repeat(${mapSize.columns}, minmax(0, 1fr))`,
    };

    const locations = () => {

        const locations = [];

        for (let row = mapSize.startingRow; row < (mapSize.startingRow + mapSize.rows); row++) {
            for (let column = mapSize.startingColumn; column < (mapSize.startingColumn + mapSize.columns); column++) {

                locations.push(
                    <div key={row + '.' + column} className='w-[80px] h-[80px]'>
                        <Location row={row} column={column} />
                    </div>
                );

            }
        }

        return locations;
    }

    const render = () => {
        if (isPhaseCharacterSelection) {
            return;
        }
        return (
            <div className=' text-white animate-fadein h-full w-full flex items-center justify-center'>
                <div className='grid gap-2' style={styleGrid}>
                    {locations()}
                </div>
            </div>
        );
    }

    return render();
}
