import {useEffect, useRef, useState} from "react";

export const useTypewriter = (text, speed = 50, delay = 0, reset = true) => {

    const [displayText, setDisplayText] = useState('');

    const typingDelay = useRef(undefined);
    const typingInterval = useRef(undefined);

    useEffect(() => {

        typingDelay.current = setTimeout(
            () => {
                let i = 0;

                if (reset) {
                    setDisplayText('');
                }

                typingInterval.current = setInterval(() => {
                    if (i < text.length) {
                        setDisplayText(prevText => prevText + text.charAt(i));
                        i++;
                    } else {
                        clearInterval(typingInterval.current);
                        clearTimeout(typingDelay.current);
                    }
                }, speed);
            }, delay);

        return () => {
            clearInterval(typingInterval.current);
            clearTimeout(typingDelay.current)
        };
    }, [text, speed]);

    return displayText;
};
