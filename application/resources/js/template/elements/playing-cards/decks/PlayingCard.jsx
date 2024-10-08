import React, {useRef, useState} from "react";
import {minicardsSvg} from "./minicardsSvg.jsx";
import {useThousandStore} from "../../../../src/games/thousand/useThousandStore.jsx";

export const PlayingCard = ({cardKey, scale = 1}) => {

    const activeCardKey = useThousandStore(state => state.activeCardKey);
    const setActiveCardKey = useThousandStore(state => state.setActiveCardKey);
    const isActive = cardKey === activeCardKey;

    const stockDistribution = useThousandStore(state => state.stockDistribution);
    const stockDistributionCardKeys = Object.values(stockDistribution);
    const blocked = stockDistributionCardKeys.includes(cardKey);

    const toggleActive = () => {
        if (!blocked) {
            setActiveCardKey(isActive ? null : cardKey);
        }
    }

    const initialPosition = useRef({x: 0, y: 0});
    const [isDragging, setIsDragging] = useState(false);
    const dragThreshold = 10;

    const checkAndSetDragging = (distanceX, distanceY) => {
        if (distanceX > dragThreshold || distanceY > dragThreshold) {
            setIsDragging(true);
        }
    }

    const handleMouseDown = (e) => {
        initialPosition.current = {x: e.clientX, y: e.clientY};
        setIsDragging(false);
    }

    const handleMouseMove = (e) => {
        const distanceX = Math.abs(e.clientX - initialPosition.current.x);
        const distanceY = Math.abs(e.clientY - initialPosition.current.y);
        checkAndSetDragging(distanceX, distanceY);
    }

    const handleMouseUp = () => {
        if (!isDragging) {
            toggleActive();
        }
    }

    const style = {
        transform: `scale(${(scale * (isActive ? 1.12 : 1.0)).toString()}) translateY(${isActive ? -2.0 : 0.0}vh)`,
    }

    const className = blocked ? 'opacity-35' : '';

    const getCard = (cardKey) => {
        return minicardsSvg[cardKey];
    }

    return (
        <div style={style} className={className}
             onMouseDown={handleMouseDown}
             onMouseMove={handleMouseMove}
             onMouseUp={handleMouseUp}
        >
            {getCard(cardKey)}
        </div>
    );
}
