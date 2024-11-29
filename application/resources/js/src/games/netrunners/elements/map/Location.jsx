import React, {useEffect, useRef, useState} from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {animated, useSpring} from "@react-spring/web";
import {Rotation} from "./Rotation.jsx";
import {LocationSelection} from "./LocationSelection.jsx";
import {submitMove} from "../../submitMove.jsx";
import {Coordinates} from "./Coordinates.jsx";
import {LocationEncounter} from "../encounters/LocationEncounter.jsx";
import {LocationPlayers} from "../players/LocationPlayers.jsx";

export const Location = ({row, column}) => {

    console.log('Location', row, column);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const setMoveData = useNetrunnersStore(state => state.setMoveData);
    const setPlayerInfoScreen = useNetrunnersStore(state => state.setPlayerInfoScreen);
    const setRechargeInfoScreen = useNetrunnersStore(state => state.setRechargeInfoScreen);

    const hasNode = useNetrunnersStore(state => state.locationsMap[row][column].hasNode);
    const nodeKey = useNetrunnersStore(state => state.locationsMap[row][column].nodeKey);
    const nodeRotation = useNetrunnersStore(state => state.locationsMap[row][column].nodeRotation);
    const hasEncounter = useNetrunnersStore(state => state.locationsMap[row][column].hasEncounter);
    const playersCount = useNetrunnersStore(state => state.locationsMap[row][column].playersCount);

    const actionableLocation = useNetrunnersStore(state => state.locationsMap[row][column].actionableLocation);
    const yourActionableLocation = useNetrunnersStore(state => state.locationsMap[row][column].yourActionableLocation);
    const actionablePhaseKey = useNetrunnersStore(state => state.locationsMap[row][column].actionablePhaseKey);
    const activePlayerChargerLocation = useNetrunnersStore(state => state.locationsMap[row][column].activePlayerChargerLocation);

    const hasPlayers = playersCount > 0;

    const [rotation, setRotation] = useState(nodeRotation * 30);

    const handleRotate = () => {

        const newRotation = rotation + 90;
        setRotation((prev) => prev + 90);

        if (yourActionableLocation) {
            setMoveData({
                payload: {row: row, column: column, direction: ((newRotation / 30) % 12)},
                phase: actionablePhaseKey,
            });
        }
    }


    const opponentRotationInterval = useRef(undefined);
    const opponentRotationTimeout = useRef(undefined);

    useEffect(() => {
        if (actionablePhaseKey === 'direction' && !yourActionableLocation) {
            opponentRotationInterval.current = setInterval(() => {
                opponentRotationTimeout.current = setTimeout(() => {
                    for (let i = 0; i <= Math.floor(Math.random() * 2); i++) {
                        handleRotate();
                    }
                }, Math.floor(Math.random() * 20) * 100);
            }, 2000);
        } else {
            clearInterval(opponentRotationInterval.current);
            clearTimeout(opponentRotationTimeout.current);
            setRotation(nodeRotation * 30);
        }

        if (actionablePhaseKey === 'direction' && yourActionableLocation) {
            setMoveData({
                payload: {row: row, column: column, direction: nodeRotation},
                phase: actionablePhaseKey,
            });
        }

    }, [actionablePhaseKey, yourActionableLocation]);


    const springRotation = useSpring({
        transform: `rotate(${rotation}deg)`,
        config: {tension: 170, friction: 20},
    });

    const style = {
        backgroundImage: hasNode ? configNetrunners.covers.location[nodeKey] : null,
        filter: ` saturate(${hasPlayers ? 160 : 100}%) brightness(${hasPlayers ? 140 : 100}%) `,
        ...springRotation
    };


    const classDivCommon = ' bg-cover bg-center bg-no-repeat w-full h-full rounded-md ';
    const classDivAction = classDivCommon
        + (actionableLocation && (' border-solid border-[2px] -mt-[2px] -ml-[2px] '
            + (yourActionableLocation
                ? ' border-orange-500 cursor-pointer shadow-actionSm hover:shadow-actionLg '
                : ' border-cyan-500 shadow-actionSmOp ')));


    const onClick = () => {

        if (!actionableLocation || !yourActionableLocation) {
            return;
        }

        switch (actionablePhaseKey) {

            case 'location':
                if (activePlayerChargerLocation) {
                    setPlayerInfoScreen(false);
                    setRechargeInfoScreen(true);
                } else {
                    submitMove({row: row, column: column}, gamePlayId , setMessage, actionablePhaseKey);
                }
                return;

            case 'direction':
                handleRotate();
                return;

            default:
                return;
        }
    }

    const render = () => {

        if (!hasNode && !actionableLocation) {
            return;
        }

        if (!hasNode && actionableLocation) {
            return <LocationSelection classDivCommon={classDivCommon} classDivAction={classDivAction} row={row} column={column} onClick={() => onClick()}/>;
        }

        return (
            <animated.div className={classDivAction} style={style} onClick={() => onClick()}>
                {yourActionableLocation && actionablePhaseKey === 'direction' && <Rotation />}
                <Coordinates row={row} column={column} rotation={rotation} />
                {hasEncounter && <LocationEncounter row={row} column={column} parentRotation={rotation} />}
                {hasPlayers && <LocationPlayers row={row} column={column} parentRotation={rotation} hasEncounter={hasEncounter} />}
            </animated.div>
        );
    }

    return render();
}
