import React, {useRef} from "react";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {Location} from "./Location.jsx";
import {TransformComponent, TransformWrapper} from "react-zoom-pan-pinch";
import {Controls} from "./Controls.jsx";

export const GameMap = () => {

    console.log('GameMap');

    const phaseKey = useNetrunnersStore(state => state.situation.phase.key);
    const isPhaseCharacterSelection = phaseKey === 'character';
    const setFollowActivePlayer = useNetrunnersStore(state => state.setFollowActivePlayer);

    const mapSize = {
            rows: useNetrunnersStore(state => state.mapSize.rows),
            columns: useNetrunnersStore(state => state.mapSize.columns),
            startingRow: useNetrunnersStore(state => state.mapSize.startingRow),
            startingColumn: useNetrunnersStore(state => state.mapSize.startingColumn),
    };

    const styleGrid = {
        gridTemplateColumns: `repeat(${mapSize.columns}, minmax(0, 1fr))`,
    };

    const transformComponentRef = useRef(null);
    const panningStartPositionX = useRef(null);
    const panningStartPositionY = useRef(null);

    const startMapPanning = (transformComponentRef) => {
        panningStartPositionX.current = transformComponentRef.state.positionX;
        panningStartPositionY.current = transformComponentRef.state.positionY;
    }

    const stopMapPanning = (transformComponentRef) => {
        if (
            Math.abs(transformComponentRef.state.positionX - panningStartPositionX.current) > 10
            || Math.abs(transformComponentRef.state.positionY - panningStartPositionY) > 10
        ) {
            setFollowActivePlayer(false);
        }
    }

    const locations = () => {

        const locations = [];

        for (let row = mapSize.startingRow; row < (mapSize.startingRow + mapSize.rows); row++) {
            for (let column = mapSize.startingColumn; column < (mapSize.startingColumn + mapSize.columns); column++) {

                const coordinates = `${row}.${column}`;

                locations.push(
                    <div key={coordinates} id={coordinates} className='w-[64px] sm:w-[128px] h-[64px] sm:h-[128px]'>
                        <Location row={row} column={column} />
                    </div>
                );

            }
        }

        return (
            <div className=' text-white animate-fadein h-full w-full flex items-center justify-center '>
                <div className='grid gap-2 shrink-0' style={styleGrid}>
                    {locations}
                </div>
            </div>
        );
    }

    const render = () => {
        if (isPhaseCharacterSelection) {
            return;
        }
        return (
            <TransformWrapper
                limitToBounds={false}
                minScale={0.2}
                maxScale={2}
                smooth={false}
                wheel={{step: 0.1}}
                doubleClick={{disabled: true}}
                onWheelStart={() => setFollowActivePlayer(false)}
                onPanningStart={(transformComponentRef) => startMapPanning(transformComponentRef)}
                onPanningStop={(transformComponentRef) => stopMapPanning(transformComponentRef)}
                onPinchingStart={() => setFollowActivePlayer(false)}
                ref={transformComponentRef}
            >
                <Controls />
                <TransformComponent wrapperStyle={{height: "100%", width: "100%",}} contentStyle={{height: "100%", width: "100%",}}>

                    {locations()}

                </TransformComponent>
            </TransformWrapper>
        );
    }

    return render();
}
