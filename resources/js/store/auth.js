import axios from 'axios';
import router from "@/router/index.js";

class Auth {
    constructor() {
        this.token = window.sessionStorage.getItem('token');
        let userData = window.sessionStorage.getItem('user');
        this.user = userData ? JSON.parse(userData) : null;
        if (this.token) {
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
        }
    }
// cliente/sesion/logout -> post, paso el token
// cliente/sesion/renovar -> post, paso el token
    login(token, user) {
        window.sessionStorage.setItem('token', token);
        window.sessionStorage.setItem('user', JSON.stringify(user));
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        this.token = token;
        this.user = user;
        router.push({name: 'dashboard'});
    }

    renewToken(token) {
        window.sessionStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        this.token = token;
    }

    check() {
        return !!this.token;
    }

    logout() {
        // window.sessionStorage.clear();
        window.sessionStorage.removeItem('token');
        window.sessionStorage.removeItem('user');
        this.user = null;
        this.token = null;
        router.push({
            path: '/login',
            replace: true,
        });
    }
}

export default new Auth();
