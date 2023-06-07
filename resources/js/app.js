import { createApp } from 'vue/dist/vue.esm-bundler';
import './bootstrap';
import '../sass/app.scss'
import Router from "./router/index.js";
import store from "@/store/index.js";

const app = createApp({});
app.use(Router)
app.use(store)
app.mount('#app');
