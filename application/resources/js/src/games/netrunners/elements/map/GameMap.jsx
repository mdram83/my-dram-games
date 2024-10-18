import {useNetrunnersStore} from "../../useNetrunnersStore.jsx";

export const GameMap = () => {

    console.log('GameMap');

    const phaseKey = useNetrunnersStore(state => state.situation.phase.key);
    const isPhaseCharacterSelection = phaseKey === 'character';

    const mapSize = useNetrunnersStore(state => state.mapSize);
    console.log(mapSize);

    const render = () => {
        if (isPhaseCharacterSelection) {
            return;
        }
        return <div className=' text-white animate-fadein '>Map</div>
    }

    return render();
}
