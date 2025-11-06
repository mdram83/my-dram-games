import { useState, useEffect } from "react";

export const useMediaQuery = (query) => {

    const [matches, setMatches] = useState(window.matchMedia(query).matches);

    useEffect(() => {
        const mediaQuery = window.matchMedia(query);
        const updateMatches = (event) => setMatches(event.matches);

        mediaQuery.addEventListener("change", updateMatches);
        setMatches(mediaQuery.matches);

        return () => mediaQuery.removeEventListener("change", updateMatches);

    }, [query]);

    return matches;
};
