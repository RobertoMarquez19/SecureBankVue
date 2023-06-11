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
        }
    }
}
</script>

<template>
    <h1 class="text-center">Cuentas disponibles Secure Bank</h1>

    <p>Aqui tienes todas tus cuentas disponibles con nosotros</p>

    <form @submit.prevent="crearCuenta()" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="col-12">
            <span class="align-middle">¿No tienes una cuenta? Puedes crearla aqui</span>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Crear Cuenta</button>
        </div>
    </form>

    <hr class="my-4">

    <div class="container-sm">
        <div class="row">
            <div class="col-sm-6 mt-3 mb-3 mb-sm-0" v-for="(cuenta, index) in cuentas" :key="cuenta.id">
                <div class="card" data-bs-toggle="collapse" :data-bs-target="`#collapse-cuenta-`+cuenta.id" aria-expanded="false" :aria-controls="`collapse-cuenta-`+cuenta.id">
                    <div class="card-header fw-bold">
                        <i class="bi bi-piggy-bank"></i> {{cuenta.numero_cuenta}} Cuenta de Ahorro
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">${{cuenta.monto}}</h5>
                        <p class="card-text">
                            Saldo Disponible
                            <p>N° {{cuenta.numero_cuenta}}</p>
                        </p>
                    </div>

                    <div class="collapse" :id="`collapse-cuenta-`+cuenta.id">
                        <div class="container row">
                            <button type="button" class="btn btn-primary col-4 m-3">Transferencia</button>
                            <button type="button" class="btn btn-primary col-4 m-3">Pagar servicio</button>
                            <button type="button" class="btn btn-primary col-4 m-3">Movimientos</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>
