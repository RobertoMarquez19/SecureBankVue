<template>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <span href="#"
                          class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-5 d-none d-sm-inline">Secure Bank</span>
                    </span>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start"
                        id="menu">
                        <li class="nav-item">
                            <router-link :to="{name:'dashboard'}" class="nav-link align-middle px-0">
                                <i class="fs-4 bi bi-house"></i> <span class="ms-1 d-none d-sm-inline">Home</span>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link :to="{name:'cuentas'}" class="nav-link align-middle px-0">
                                <i class="fs-4 bi bi-cash-coin"></i> <span
                                class="ms-1 d-none d-sm-inline">Cuentas</span>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link align-middle px-0">
                                <i class="fs-4 bi bi-credit-card-2-back"></i> <span class="ms-1 d-none d-sm-inline">Tarjetas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link align-middle px-0">
                                <i class="fs-4 bi bi-currency-bitcoin"></i> <span
                                class="ms-1 d-none d-sm-inline">Crypto</span>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown pb-4">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                           id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="images/profile.png" alt="Profile Picture" width="30" height="30"
                                 class="rounded-circle">
                            <span
                                class="d-none d-sm-inline mx-1 text-wrap">{{ this.user.nombres }} {{ this.user.apellidos }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
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
                timer: 5000,
                html: "<p>Su sesion se cerrara automaticamente si no la extiende</p>",
                timerProgressBar: true,
                allowOutsideClick: false,
                confirmButtonText: "Extender Sesión",
                willClose: () => {
                    clearInterval(timerInterval)
                }
            }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                    this.logout();
                } else if (result.isConfirmed) {
                    console.log("Nuevo Token")
                }
            })
        }, 6000);
    },
    beforeUnmount() {
        clearInterval(this.timer)
    },
    methods: {
        logout() {
            Auth.logout()
        }
    }
}
</script>

<style>

</style>
