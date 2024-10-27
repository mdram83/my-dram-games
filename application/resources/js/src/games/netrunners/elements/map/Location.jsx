import React, {useEffect, useRef, useState} from "react";
import {configNetrunners} from "../../configNetrunners.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";
import {submitMove} from "../../submitMove.jsx";
import {useGamePlayStore} from "../../../../game-core/game-play/useGamePlayStore.jsx";
import {animated, useSpring} from "@react-spring/web";
import {Rotation} from "./Rotation.jsx";

export const Location = ({row, column}) => {

    console.log('Location', row, column);

    const gamePlayId = useGamePlayStore(state => state.gamePlayId);
    const setMessage = useGamePlayStore((state) => state.setMessage);

    const setMoveData = useNetrunnersStore(state => state.setMoveData);

    const hasNode = useNetrunnersStore(state => state.locationsMap[row][column].hasNode);
    const nodeKey = useNetrunnersStore(state => state.locationsMap[row][column].nodeKey);
    const nodeRotation = useNetrunnersStore(state => state.locationsMap[row][column].nodeRotation);

    const actionableLocation = useNetrunnersStore(state => state.locationsMap[row][column].actionableLocation);
    const yourActionableLocation = useNetrunnersStore(state => state.locationsMap[row][column].yourActionableLocation);
    const actionablePhaseKey = useNetrunnersStore(state => state.locationsMap[row][column].actionablePhaseKey);

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

    // TODO add uncover when going from location to direction (similar like selecting character)
    // see CharactersGritItem lines 16-18 and generally CharacterAvatar...
    // I should probably flip only once when changing from (location w/o node w/o direction) to (location w/ node, w/o direction)
    // I should also not flip on initial render (for all locations w/ node and w/o direction).
    // so kind of useEffect on node and location-direction attributes with IF...

    const springStyle = useSpring({
        transform: `rotate(${rotation}deg)`,
        config: { tension: 170, friction: 20 },
    });

    const style = {
        backgroundImage: hasNode ? configNetrunners.covers.location[nodeKey] : configNetrunners.covers.location.imageCoverM,
        ...springStyle
    };

    const classDiv = ' bg-cover bg-center bg-no-repeat w-full h-full rounded-md '
        + (actionableLocation
            ? ' border-solid border-[2px] -mt-[2px] -ml-[2px] '
                + (yourActionableLocation
                    ? ' border-orange-500 cursor-pointer shadow-actionSm hover:shadow-actionLg '
                    : ' border-cyan-500 shadow-actionSmOp ')
            : ' ');

    const onClick = () => {
        if (!actionableLocation || !yourActionableLocation) {
            return;
        }
        switch (actionablePhaseKey) {
            case 'location':
                submitMove({row: row, column: column}, gamePlayId , setMessage, actionablePhaseKey);
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
        return (
            <animated.div className={classDiv} style={style} onClick={() => onClick()}>
                {row}.{column}
                {yourActionableLocation && actionablePhaseKey === 'direction' && <Rotation />}
            </animated.div>
        );
    }

    return render();
}
