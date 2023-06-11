<script>
import Auth from "@/store/auth.js";
import Swal from 'sweetalert2/dist/sweetalert2';
export default {
    name: "default-layout",
    data() {
        return {
            user: this.auth.user,
            cuentas: [],
        }
    },
    async mounted() {
        await this.obtenerCuentas();
        console.log(this.cuentas);
    },
    methods: {
        logout() {
            Auth.logout()
        },
        async crearCuenta() {
            await axios.post('cliente/cuentas',this.auth.token).then(response => {
                console.log(response);
                this.obtenerCuentas();
            }).catch(({response}) => {
                let mensajeError = response.data.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Error al crear la cuenta',
                    text: mensajeError
                });
            });
        },
        async obtenerCuentas() {
            await axios.get('cliente/cuentas',this.auth.token).then(response => {
                console.log(response);
                this.cuentas = response.data.data;
            }).catch(({response}) => {
                let mensajeError = response.data.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensajeError
                });
            });
        },

        showTransferenciaModal(cuenta){

        }
    }
}
</script>

<template>
    <h1 class="text-center">Cuentas</h1>

    <form @submit.prevent="crearCuenta()" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="col-12">
            <span class="align-middle">¿No tienes una cuenta? Puedes crearla aqui</span>
        </div>
        <div class="col-12">
            <button type="submit" class="btn gradient-custom-2 btn col m-1 text-white">Crear Cuenta</button>
        </div>
    </form>

    <hr class="my-4">

    <div class="container-sm">
        <div class="row">
            <div class="col-sm-6 mt-3 mb-3 mb-sm-0" v-for="(cuenta, index) in cuentas" :key="cuenta.id">
                <div class="card" data-bs-toggle="collapse" :data-bs-target="`#collapse-cuenta-`+cuenta.id" aria-expanded="false" :aria-controls="`collapse-cuenta-`+cuenta.id">
                    <div class="card-header fw-bold">
                        <i class="bi bi-piggy-bank"></i> {{cuenta.numero_cuenta}}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">${{cuenta.monto}}</h5>
                        <p class="card-text">
                            Saldo Disponible
                            <p>N° {{cuenta.numero_cuenta}}</p>
                        </p>
                    </div>

                    <div class="collapse" :id="`collapse-cuenta-`+cuenta.id">
                        <div class="container text-center">
                            <hr>
                            <div class="row align-items-center justify-content-center m-1">
                                <button type="button" class="gradient-custom-2 btn col m-1 text-white" v-on:click="showTransferenciaModal(cuenta)"><i class="bi bi-cash-coin"></i> Transferencia</button>
                                <button type="button" class="gradient-custom-2 btn col m-1 text-white"><i class="bi bi-receipt"></i> Pagar servicio</button>
                                <button type="button" class="gradient-custom-2 btn col m-1 text-white"><i class="bi bi-card-checklist"></i> Movimientos</button>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>
