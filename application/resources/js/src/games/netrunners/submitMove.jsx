import axios from "axios";

export const submitMove = (data, gamePlayId, setMessage, phase) => {
    axios
        .post(window.MyDramGames.routes['ajax.gameplay.move'](gamePlayId), {
            move: {data: data, phase: phase}
        })
        .then(() => {})
        .catch(error => {
            console.log(error.response);
            setMessage(error.response.data.message ?? 'Unexpected error', true);
        });
}
