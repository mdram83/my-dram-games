import React, { useRef } from 'react'
import { useSprings, animated } from '@react-spring/web'
import { useDrag } from 'react-use-gesture'

import styles from './styles.module.css'

const fn =
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

    const singleWidth = parentWidth / items.length

    const order = useRef(items.map((_, index) => index))
    const [springs, api] = useSprings(items.length, fn(order.current, singleWidth))

    const clamp = (value, min, max) => Math.min(Math.max(value, min), max);
    const swap = (array, fromIndex, toIndex) => {
        const newArray = array.slice() // Create a copy of the array to avoid mutating the original
        const [movedElement] = newArray.splice(fromIndex, 1) // Remove the element from the original position
        newArray.splice(toIndex, 0, movedElement) // Insert the element at the new position
        return newArray
    }

    const bind = useDrag(({ args: [originalIndex], active, movement: [x] }) => {
        const curIndex = order.current.indexOf(originalIndex)
        const curRow = clamp(Math.round((curIndex * singleWidth + x) / singleWidth), 0, items.length - 1)
        const newOrder = swap(order.current, curIndex, curRow)

        api.start(fn(newOrder, singleWidth, active, originalIndex, curIndex, x))

        if (!active) {
            order.current = newOrder
            // TODO here make axios call with new order to backend to save it (or call external function in case you will reuse it with some other dragging component)
        }
    })

    return (
        <div className={styles.content}>
            {springs.map(({ zIndex, x, scale }, i) => (
                <animated.div
                    {...bind(i)}
                    key={i}
                    style={{
                        zIndex,
                        x,
                        scale,
                    }}
                    children={items[i]}
                />
            ))}
        </div>
    )
}
