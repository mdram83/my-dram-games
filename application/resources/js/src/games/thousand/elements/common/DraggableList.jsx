import React, {useEffect, useRef} from 'react';
import {animated, useSprings} from '@react-spring/web';
import {useDrag} from 'react-use-gesture';

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

export default function DraggableList({ items, parentWidth, callback = undefined }) {

    console.log('call DraggableList with width = ', parentWidth);

    const singleWidth = parentWidth / (items.length + 1);

    const order = useRef(items.map((_, index) => index));
    const [springs, api] = useSprings(items.length, index => createSpringConfig(order.current, singleWidth)(index));

    const bind = useDrag(({ args: [originalIndex], active, movement: [x] }) => {
        const curIndex = order.current.indexOf(originalIndex);
        const curRow = clamp(Math.round((curIndex * singleWidth + x) / singleWidth), 0, items.length - 1);
        const newOrder = swap(order.current, curIndex, curRow);

        api.start(createSpringConfig(newOrder, singleWidth, active, originalIndex, curIndex, x));

        if (!active && order.current.join() !== newOrder.join()) {

            console.log('apply sorting');

            order.current = newOrder;

            // TODO temp
            console.log('new order:', order.current);
            console.log('current keys:', items.map((element) => element.props.cardKey));

            if (callback) {
                callback(order.current, items.map((element) => element.props.cardKey));
            }
        }
    });

    useEffect(() => {
        const newSingleWidth = parentWidth / (items.length + 1);

        console.log(order.current, items.map((_, index) => index));

        order.current = items.map((_, index) => index);
        api.start(index => createSpringConfig(order.current, newSingleWidth)(index));
    // }, [parentWidth, items.length, api]); // this doesn work immediately now when hearing from backend
    }, [parentWidth, items, api]); // this work immediately but with strange effect double sorting or so...
    // TODO For items, I should trigger re-rended when LENGTH or ORDER has changed...

    return (
        <div className="flex relative items-center sm:-ml-[100%] -ml-[120%]">
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
