import React, {useEffect, useRef, useState} from "react";

export const GlitchEffect = ({img, classAdd = ''}) => {

    console.log('GlitchEffect');

    const [glitch, setGlitch] = useState(false);

    const interval = useRef(undefined);
    const timeout = useRef(undefined);

    useEffect(() => {

        interval.current = setInterval(() => {
            setGlitch(true);
            timeout.current = setTimeout(() => {
                setGlitch(false);
            }, Math.floor(Math.random() * 10) * 100);
        }, 1000);


        return () => {
            clearInterval(interval.current);
            clearTimeout(timeout.current);
        }

    }, []);

    const styles = {backgroundImage: img};

    const classDiv = classAdd
        + ' size-full bg-cover bg-center  '
        + (glitch ? ' animate-glitch ' : ' ');

    return <div className={classDiv} style={styles}></div>;
}
