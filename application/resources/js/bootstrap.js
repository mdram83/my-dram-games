/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.interceptors.response.use(response => response, async error => {
    if (error.response && 419 === error.response.status) {
        await axios.get('/ajax/csrf-token');
        return axios(error.response.config);
    }
    return Promise.reject(error);
});


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echoAuthEndpoint = () => {
    const cookieName = import.meta.env.VITE_PLAYER_HASH_COOKIE_NAME;
    const authEndpointBase = `/broadcasting/auth?${cookieName}=`;
    const cookieValue = (name) => {
        const regex = new RegExp(`(^| )${name}=([^;]+)`)
        const match = document.cookie.match(regex)
        if (match) {
            return match[2];
        }
    }
    return authEndpointBase + cookieValue(cookieName);
}

window.Echo = new Echo({
    broadcaster: 'pusher',
    authEndpoint: echoAuthEndpoint(),
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    forceTLS: true,
    disableStats: true,
    enabledTransports: ['wss', 'ws'],
});
