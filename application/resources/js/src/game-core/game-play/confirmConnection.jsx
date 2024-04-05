import axios from "axios";

export const confirmConnection = (gamePlayId) => {
    axios
        .get(window.MyDramGames.routes['ajax.gameplay.connect'](gamePlayId))
        .then(() => {})
        .catch(() => {});
}
