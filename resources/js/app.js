import { createApp } from 'vue/dist/vue.esm-bundler';
import './bootstrap';
import '../sass/app.scss'
import Router from "./router/index.js";
import store from "@/store/index.js";
import Auth from "@/store/auth.js";
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const app = createApp({});
app.config.globalProperties.auth = Auth
app.use(Router)
app.use(store)
app.use(VueSweetalert2)
app.mount('#app');
