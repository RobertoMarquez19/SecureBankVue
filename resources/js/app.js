import { createApp } from 'vue/dist/vue.esm-bundler';
import './bootstrap';
import '../sass/app.scss'
import Router from "./router/index.js";
import store from "@/store/index.js";
import Auth from "@/store/auth.js";

const app = createApp({});
app.config.globalProperties.auth = Auth
app.use(Router)
app.use(store)
app.mount('#app');
