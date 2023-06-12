import { createWebHistory, createRouter } from 'vue-router'
import store from '@/store'
import Auth from "@/store/auth.js";

// Los componentes importados abajos cuentan con Lazy Loading
// Agregar de la misma manera por cuestiones de rendimiento

/* Guest Component */
const Login = () => import('@/components/Login.vue')
const Register = () => import('@/components/Register.vue')
/* Guest Component */

/* Layouts */
const DahboardLayout = () => import('@/components/layouts/Default.vue')
/* Layouts */

/* Authenticated Component */
const Dashboard = () => import('@/components/Dashboard.vue')
/* Authenticated Component */

/* Cuentas Componente */
const Cuentas = () => import('@/views/Cuentas.vue')
/* Cuentas Componente */

/* Cuentas Componente */
const Tarjetas = () => import('@/views/Tarjetas.vue')
/* Cuentas Componente */


const routes = [
    {
        name: "login",
        path: "/login",
        component: Login,
        meta: {
            middleware: "guest",
            title: `Login`
        }
    },
    {
        name: "register",
        path: "/register",
        component: Register,
        meta: {
            middleware: "guest",
            title: `Register`
        }
    },
    {
        path: "/",
        component: DahboardLayout,
        meta: {
            middleware: "auth"
        },
        children: [
            {
                name: "dashboard",
                path: '/',
                component: Dashboard,
                meta: {
                    title: `Dashboard`
                }
            },
            {
                name: "cuentas",
                path: '/',
                component: Cuentas,
                meta: {
                    title: `Cuentas`
                }
            },
            {
                name: "tarjetas",
                path: '/',
                component: Tarjetas,
                meta: {
                    title: `Tarjetas`
                }
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes, // short for `routes: routes`
})

router.beforeEach((to, from, next) => {
    document.title = to.meta.title
    /*if (to.meta.middleware === "guest") {
        if (Auth.check()) {
            next({ name: "dashboard" })
        }
        next()
    } else {
        if (Auth.check()) {
            next()
        } else {
            next({ name: "login" })
        }
    }*/
    if (to.meta.middleware === "auth") {
        if (Auth.check()) {
            next();
            return;
        } else {
            router.push({
                name: 'login',
                path: from.fullPath,
                replace: true,
            });
        }
    } else {
        next();
    }
})

export default router
