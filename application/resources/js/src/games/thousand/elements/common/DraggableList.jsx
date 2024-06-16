import React, {useEffect, useRef} from 'react';
import {animated, useSprings} from '@react-spring/web';
import {useDrag} from 'react-use-gesture';

const itemsEqual = (arr1, arr2) => {

    const items1 = arr1.map((item) => item.props.cardKey);
    const items2 = arr2.map((item) => item.props.cardKey);

    if (items1.length !== items2.length) return false;

    const sortedArr1 = [...items1].sort();
    const sortedArr2 = [...items2].sort();

    return sortedArr1.every((value, index) => value === sortedArr2[index]);
};

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

function DraggableList({ items, parentWidth, callback = undefined }) {

    const singleWidth = parentWidth / (items.length + 1);

    const order = useRef(items.map((_, index) => index));
    const [springs, api] = useSprings(items.length, index => createSpringConfig(order.current, singleWidth)(index));

    const bind = useDrag(({ args: [originalIndex], active, movement: [x] }) => {
        const curIndex = order.current.indexOf(originalIndex);
        const curRow = clamp(Math.round((curIndex * singleWidth + x) / singleWidth), 0, items.length - 1);
        const newOrder = swap(order.current, curIndex, curRow);

        api.start(createSpringConfig(newOrder, singleWidth, active, originalIndex, curIndex, x));

        if (!active && order.current.join() !== newOrder.join()) {

            order.current = newOrder;

            if (callback) {
                callback(order.current, items.map((element) => element.props.cardKey));
            }
        }
    });

    useEffect(() => {
        const newSingleWidth = parentWidth / (items.length + 1);
        order.current = items.map((_, index) => index);
        api.start(index => createSpringConfig(order.current, newSingleWidth)(index));
    }, [parentWidth, items, api]);

    return (
        <div className="flex relative items-center -ml-[88%]">
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

export default React.memo(DraggableList, (prevProps, nextProps) => {
    return itemsEqual(prevProps.items, nextProps.items) && prevProps.parentWidth === nextProps.parentWidth;
});
