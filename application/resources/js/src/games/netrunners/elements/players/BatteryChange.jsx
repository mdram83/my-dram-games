import React, {useEffect, useState} from "react";
import {animated, useTransition} from "@react-spring/web";
import {Battery} from "../misc/Battery.jsx";
import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const BatteryChange = ({playerName}) => {

    console.log('BatteryChange');

    const points = useNetrunnersStore(state => state.situation.players[playerName].battery);

    const [prevPoints, setPrevPoints] = useState(points);
    const [targetPoints, setTargetPoints] = useState(points);
    const [display, setDisplay] = useState(false);

    const transitions = useTransition(display, {
        from: { opacity: 0 },
        enter: { opacity: 1 },
        leave: { opacity: 0 },
        config: { duration: 200 },
    });

    useEffect(() => {
        setPrevPoints(targetPoints);
        setTargetPoints(points);
        setDisplay(true);

        const timeout = setTimeout(() => {
            setDisplay(false);
        }, 3000);

        return () => clearTimeout(timeout);
    }, [points]);

    return transitions((styles, item) =>

        item && (
            <animated.div style={styles} className="absolute size-[80%]">
                <div className='flex items-center justify-center size-full'>

                    <div className='w-full h-[60%] aspect-square '>
                        <Battery points={prevPoints} targetPoints={targetPoints} smallSize={true}/>
                    </div>

                </div>
            </animated.div>
        )

    );
}
