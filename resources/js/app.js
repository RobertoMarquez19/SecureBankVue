import './bootstrap';
import { createApp } from 'vue';
import axios from 'axios'

axios.defaults.baseURL = 'http://localhost:8000/api/';
axios.defaults.headers['Authorization'] = `Bearer ${localStorage.getItem('token')}`;

import App from './App.vue';
import router from "./router/index.js";

const app = createApp(App);
app.use(router)
app.mount('#app');
