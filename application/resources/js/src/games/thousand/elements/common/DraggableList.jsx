import React, { useRef } from 'react';
import { useSprings, animated } from '@react-spring/web';
import { useDrag } from 'react-use-gesture';

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

const swap = (array, fromIndex, toIndex) => {
    const newArray = array.slice();
    const [movedElement] = newArray.splice(fromIndex, 1);
    newArray.splice(toIndex, 0, movedElement);
    return newArray;
}

const createSpringConfig =
    (order, singleWidth, active = false, originalIndex = 0, curIndex = 0, x = 0) =>
        index =>
            active && index === originalIndex
                ? {
                    x: curIndex * singleWidth + x,
                    scale: 1.1 + order.indexOf(index) * 0.025,
                    zIndex: order.indexOf(index),
                    immediate: key => key === 'x' || key === 'zIndex',
                }
                : {
                    to: {
                        x: order.indexOf(index) * singleWidth,
                        scale: 1,
                        zIndex: order.indexOf(index),
                        immediate: false,
                    },
                    config: { duration: 220 },
                }

export default function DraggableList({ items, parentWidth }) {

    console.log('call DraggableList with width = ', parentWidth);

    const singleWidth = parentWidth / (items.length + 1);

    const order = useRef(items.map((_, index) => index));
    const [springs, api] = useSprings(items.length, createSpringConfig(order.current, singleWidth));

    const bind = useDrag(({ args: [originalIndex], active, movement: [x] }) => {
        const curIndex = order.current.indexOf(originalIndex);
        const curRow = clamp(Math.round((curIndex * singleWidth + x) / singleWidth), 0, items.length - 1);
        const newOrder = swap(order.current, curIndex, curRow);

        api.start(createSpringConfig(newOrder, singleWidth, active, originalIndex, curIndex, x));

        if (!active) {
            order.current = newOrder;
            // TODO here make axios call with new order to backend to save it (or call external function in case you will reuse it with some other dragging component)
        }
    });

    // TODO h-full works ok, adjust or remove at all after testing in below className
    return (
        <div className="flex relative items-center h-[4vh] sm:-ml-[100%] -ml-[120%]">
            {springs.map(({ zIndex, x, scale }, i) => (
                <animated.div
                    {...bind(i)}
                    key = {i}
                    style = {{zIndex, x, scale}}
                    children = {items[i]}
                    className = ' absolute cursor-pointer touch-none '
                />
            ))}
        </div>
    );
}
