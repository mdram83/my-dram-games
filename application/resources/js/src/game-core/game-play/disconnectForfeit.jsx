import axios from "axios";

export const disconnectForfeit = (user, gamePlayId) => {
    axios
        .post(window.MyDramGames.routes['ajax.gameplay.disconnect-forfeit'](gamePlayId), {disconnected: user.name})
        .then(() => {})
        .catch(() => {});
}
