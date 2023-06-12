<template>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 color1">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100 ">
                    <span href="#" class="mx-auto p-2" >
                        <img src="images/icono.png" style="width: 100px;" alt="logo">
                    </span>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                        <li class="nav-item ">
                            <router-link :to="{ name: 'dashboard' }" class="nav-link align-middle px-0">
                                <i class="fs-2 bi bi-house color2"></i> <span
                                    class="ms-1 d-none d-sm-inline text3">Home</span>
                            </router-link>
                        </li>
                        <li class="nav-item ">
                            <router-link :to="{ name: 'cuentas' }" class="nav-link align-middle px-0">
                                <i class="fs-2 bi bi-cash-coin color2"></i>
                                <span class="ms-1 d-none d-sm-inline text3">Cuentas</span>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{name:'tarjetas'}" class="nav-link align-middle px-0">
                                <i class="fs-2 bi bi-credit-card-2-back color2"></i>
                                <span class="ms-1 d-none d-sm-inline text3">Tarjetas</span>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link align-middle px-0">
                                <i class="fs-2 bi bi-currency-bitcoin color2"></i> <span
                                    class="ms-1 d-none d-sm-inline text3">Crypto</span>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown pb-4">
                        <a href="#" class="d-flex align-items-center text3 text-decoration-none dropdown-toggle"
                            id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/user1.png" alt="Profile Picture" width="25" height="26">
                            <span class="d-none d-sm-inline mx-1 text-wrap ">{{ this.user.nombres }} {{ this.user.apellidos
                            }}</span>
                        </a>
                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)" @click="logout">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col py-3">
                <div class="mt-3">
                    <router-view></router-view>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Auth from "@/store/auth.js";
import Swal from 'sweetalert2/dist/sweetalert2';
export default {
    name: "default-layout",
    data() {
        return {
            user: this.auth.user,
            timer: null
        }
    },
    mounted() {
        /*this.timer = setInterval(() => {
            console.log("Hola");
        }, 540000);*/

        this.timer = setInterval(() => {
            let timerInterval
            Swal.fire({
                title: '¿Desea Extender la Sesión?',
                icon: "question",
                timer: 24000,
                html: "<p>Su sesión se cerrara automaticamente si no la extiende</p>",
                timerProgressBar: true,
                allowOutsideClick: false,
                confirmButtonText: "Extender Sesión",
                willClose: () => {
                    clearInterval(timerInterval)
                }
            }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                    // TODO: Implementar Logout
                    //this.logout();
                } else if (result.isConfirmed) {
                    axios.get('cliente/sesion/renovar').then((response) => {
                        console.log(response);
                        let tokenResponse = response.data.data;
                        Auth.renewToken(tokenResponse);
                    }).catch(({ response }) => {
                        console.error(response.data);
                        let mensajeError = response.data.message;
                        if (response.status === 401 || response.status === 422) {
                            this.validationErrors = response.data.data
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: mensajeError,
                            });
                        } else {
                            this.validationErrors = {}
                            Swal.fire({
                                icon: 'error',
                                title: 'Error ah ocurrido algo inesperado',
                                text: mensajeError,
                                footer: 'Estamos trabajando para resolverlo'
                            });
                        }
                    });
                }
            })
        }, 180000);
    },
    beforeUnmount() {
        clearInterval(this.timer)
    },
    methods: {
        async logout() {
            // TODO: Hacer el metodo en axios de revocar el token, cliente/sesion/renovar
            await axios.post('cliente/sesion/logout').then((response) => {
                console.log(response);
                Auth.logout();
            }).catch(({ response }) => {
                console.error(response.data);
                let mensajeError = response.data.message;
                if (response.status === 401 || response.status === 422) {
                    this.validationErrors = response.data.data
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: mensajeError,
                    });
                } else {
                    this.validationErrors = {}
                    Swal.fire({
                        icon: 'error',
                        title: 'Error ah ocurrido algo inesperado',
                        text: mensajeError,
                        footer: 'Estamos trabajando para resolverlo'
                    });
                }
            });
        }
    }
}
</script>

<style></style>
