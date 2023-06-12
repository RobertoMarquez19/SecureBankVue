<script>
import Auth from "@/store/auth.js";
import Swal from 'sweetalert2/dist/sweetalert2';

export default {
    name: "default-layout",
    data() {
        return {
            user: this.auth.user,
            cuentas: [],
            cuentaSeleccionada: null,
            transaccion: {
                id_cuenta: "",
                cuenta_destino: "",
                monto: "",
                concepto: ""
            },
            pagoFactura: {
                id_cuenta: "",
                npe: ""
            },
            movimientos: [],
            spinnerCuentas: false,
            spinnerMovimientos: false,
        }
    },
    async mounted() {
        await this.obtenerCuentas();
    },
    methods: {
        async crearCuenta() {
            await axios.post('cliente/cuentas').then(response => {
                this.obtenerCuentas();
            }).catch(({response}) => {
                let mensajeError = response.data.data;
                Swal.fire({
                    icon: 'error',
                    title: 'Error al crear la cuenta',
                    text: mensajeError
                });
            });
        },
        async obtenerCuentas() {
            this.spinnerCuentas = true;
            await axios.get('cliente/cuentas').then(response => {
                this.cuentas = response.data.data;
                this.spinnerCuentas = false;
            }).catch(({response}) => {
                let mensajeError = response.data.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensajeError
                }).finally(()=> {
                    this.spinnerCuentas = false
                })
            });
        },

        async validarCuentaDestino() {
            this.transaccion.id_cuenta = this.cuentaSeleccionada.id
            await axios.post('cliente/transferencia/cuenta', {cuenta_destino: this.transaccion.cuenta_destino}).then(response => {
                const swalWithCustomButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-block me-2 fa-lg gradient-custom-2 text-white',
                        cancelButton: 'btn btn-secondary btn-block fa-lg'
                    },
                    buttonsStyling: false
                })

                swalWithCustomButtons.fire({
                    icon: 'question',
                    title: 'Confirmacion',
                    text: `Desea transferir $${this.transaccion.monto} a ${response.data.data.nombre} ${response.data.data.apellidos}?`,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar',
                    showCancelButton: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios.post('cliente/transferencia/transferir', this.transaccion).then((response) => {
                            return response
                        }).catch(error => {
                            Swal.showValidationMessage(
                                error.response.data.data
                            )
                        })
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        swalWithCustomButtons.fire({
                            icon: 'success',
                            title: "Exito",
                            text: result.value.data.message,
                            confirmButtonText: 'Aceptar'
                        })
                        this.obtenerCuentas()
                    }
                });
            }).catch(({response}) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.data.data
                });
            });
        },

        async validarNpe() {
            this.pagoFactura.id_cuenta = this.cuentaSeleccionada.id
            await axios.post('cliente/facturas', {npe: this.pagoFactura.npe}).then(response => {
                const swalWithCustomButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-block me-2 fa-lg gradient-custom-2 text-white',
                        cancelButton: 'btn btn-secondary btn-block fa-lg'
                    },
                    buttonsStyling: false
                })

                swalWithCustomButtons.fire({
                    icon: 'question',
                    title: 'Confirmacion',
                    text: `Desea pagar $${response.data.data.monto} a ${response.data.data.colector}?`,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar',
                    showCancelButton: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return axios.post('cliente/cuentas/facturas/pago', this.pagoFactura).then((response) => {
                            return response
                        }).catch(error => {
                            Swal.showValidationMessage(
                                error.response.data.data
                            )
                        })
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        swalWithCustomButtons.fire({
                            icon: 'success',
                            title: "Exito",
                            text: result.value.data.message,
                            confirmButtonText: 'Aceptar'
                        })
                        this.obtenerCuentas()
                    }
                });
            }).catch(({response}) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.data.data
                });
            });
        },

        async obtenerMovimientos(cuenta){
            this.spinnerMovimientos = true;
            await axios.post('cliente/cuentas/transferencias',{id_cuenta:cuenta.id}).then(response => {
                this.movimientos=response.data.data
                this.spinnerMovimientos = false;
            }).catch(({response}) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.data.data
                });
            }).finally(() => {
                this.spinnerMovimientos = false
            })
        },


        clearFormTransaccion() {
            this.transaccion = {
                id_cuenta: "",
                cuenta_destino: "",
                monto: "",
                concepto: ""
            }
        },

        clearFormPago() {
            this.pagoFactura = {
                id_cuenta: "",
                npe: ""
            }
        },

        clearMovimientos() {
            this.movimientos = []
        }
    }
}
</script>

<template>
    <h1 class="text-center">Cuentas</h1>

    <!-- Modal de Transferencias -->
    <div class="modal fade" id="transferenciaModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Transferencia cuenta</h1>
                </div>
                <form v-if="cuentaSeleccionada!=null" ref="transaccionForm" @submit.prevent="validarCuentaDestino()">
                    <div class="modal-body">
                        <label for="cuenta_origen" class="form-label mt-2">Cuenta</label>
                        <input class="form-control" type="text" placeholder="Disabled input"
                               aria-label="Disabled input example" :value="cuentaSeleccionada.numero_cuenta" disabled
                               readonly>
                        <label for="cuenta_origen" class="form-label mt-2">Cuenta destino</label>
                        <input v-model="transaccion.cuenta_destino" class="form-control" type="text" minlength="20"
                               maxlength="20" placeholder="Ingrese el numero de cuenta de destino">
                        <label for="cuenta_origen" class="form-label mt-2">Monto</label>
                        <input v-model="transaccion.monto" class="form-control" type="number" min="0.01" step="0.01"
                               placeholder="$0.0">
                        <label for="cuenta_origen" class="form-label mt-2">Concepto</label>
                        <input v-model="transaccion.concepto" class="form-control" type="text"
                               placeholder="Ingrese el concepto de la transferencia">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="gradient-custom-2 btn text-white">Transferir</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                @click="clearFormTransaccion">Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Pago de Facturas -->
    <div class="modal fade" id="pagoModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Pago de servicio</h1>
                </div>
                <form v-if="cuentaSeleccionada!=null" ref="pagoForm" @submit.prevent="validarNpe()">
                    <div class="modal-body">
                        <label for="cuenta_origen" class="form-label mt-2">Cuenta</label>
                        <input class="form-control" type="text" placeholder="Disabled input"
                               aria-label="Disabled input example" :value="cuentaSeleccionada.numero_cuenta" disabled
                               readonly>
                        <label for="cuenta_origen" class="form-label mt-2">NPE</label>
                        <input v-model="pagoFactura.npe" class="form-control" type="text" minlength="34" maxlength="34"
                               placeholder="Ingrese el NPE">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="gradient-custom-2 btn text-white">Pagar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                @click="clearFormPago">Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Movimientos de Cuenta -->
    <div class="modal fade " id="movimientosModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Historial de transacciones</h1>
                </div>
                <div class="modal-body">
                    <div v-if="movimientos.length === 0" class="text-center">
                        <p class="fs-5"><i class="bi bi-currency-exchange"></i> No posee historial de transacciones con esta cuenta</p>
                    </div>
                    <div v-if="spinnerMovimientos" class="mx-auto text-center mt-5">
                        <div class="spinner-grow" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="col mb-2" v-for="(movimiento, index) in movimientos" :key="movimiento.id_transaccion">
                        <div class="card" data-bs-toggle="collapse">
                            <div v-if="movimiento.cuenta_destino!=null" class="card-header fw-bold">
                                <i class="bi bi-piggy-bank"></i> {{movimiento.cuenta_destino}}
                            </div>
                            <div v-if="movimiento.cuenta_recibido!=null" class="card-header fw-bold">
                                <i class="bi bi-piggy-bank"></i> {{movimiento.cuenta_recibido}}
                            </div>

                            <div v-if="movimiento.operacion==='factura'" class="card-header fw-bold">
                                <i class="bi bi-receipt"></i> {{movimiento.colector}}
                            </div>
                            <div class="card-body">
                                <h5 v-if="movimiento.operacion==='salida'" class="card-title fw-bold text-danger">${{movimiento.monto}}</h5>
                                <h5 v-if="movimiento.operacion==='entrada'" class="card-title fw-bold text-success">${{movimiento.monto}}</h5>
                                <h5 v-if="movimiento.operacion==='factura'" class="card-title fw-bold text-danger">${{movimiento.monto}}</h5>
                                <h5 class="card-title fw-bold text-secondary">${{movimiento.monto_despues}}</h5>
                                <h6 class="card-title fw-bold text-primary">{{movimiento.fecha_operacion_string}}</h6>
                                <p>{{movimiento.concepto}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="clearMovimientos">Salir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form @submit.prevent="crearCuenta()" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="col-12">
            <span class="align-middle">¿No tienes una cuenta? Puedes crearla aqui</span>
        </div>
        <div class="col-12">
            <button type="submit" class="btn gradient-custom-2 btn col m-1 text-white">Crear Cuenta</button>
        </div>
    </form>

    <hr class="my-4">

    <!-- Mensaje de ninguna cuenta Inactiva Y Spinner de Carga -->
    <div v-if="cuentas.length === 0" class="text-center">
        <p class="fs-5"><i class="bi bi-bank2"></i> No posee cuentas activas</p>
    </div>

    <div v-if="spinnerCuentas" class="mx-auto text-center mt-5">
        <div class="spinner-grow" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-sm vertical-scrollable">
        <div class="row">
            <div class="col-sm-6 mt-3 mb-3 mb-sm-0" v-for="(cuenta, index) in cuentas" :key="cuenta.id">
                <div class="card" data-bs-toggle="collapse" :data-bs-target="`#collapse-cuenta-`+cuenta.id"
                     aria-expanded="false" :aria-controls="`collapse-cuenta-`+cuenta.id">
                    <div class="card-header fw-bold">
                        <i class="bi bi-piggy-bank"></i> {{ cuenta.numero_cuenta }}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">${{ cuenta.monto }}</h5>
                        <p class="card-text">
                            Saldo Disponible
                        <p>N° {{ cuenta.numero_cuenta }}</p>
                        </p>
                    </div>

                    <div class="collapse" :id="`collapse-cuenta-`+cuenta.id">
                        <div class="container text-center">
                            <hr>
                            <div class="row align-items-center justify-content-center m-1">
                                <button type="button" class="gradient-custom-2 btn col m-1 text-white"
                                        data-bs-toggle="modal" data-bs-target="#transferenciaModal"
                                        @click="cuentaSeleccionada = cuenta"><i class="bi bi-cash-coin"></i>
                                    Transferencia
                                </button>
                                <button type="button" class="gradient-custom-2 btn col m-1 text-white"
                                        data-bs-toggle="modal" data-bs-target="#pagoModal"
                                        @click="cuentaSeleccionada = cuenta"><i class="bi bi-receipt"></i>
                                    Pagar servicio
                                </button>
                                <button type="button" class="gradient-custom-2 btn col m-1 text-white"
                                        data-bs-toggle="modal" data-bs-target="#movimientosModal"
                                        @click="obtenerMovimientos(cuenta)"><i
                                    class="bi bi-card-checklist"></i> Movimientos
                                </button>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>

<style>
.vertical-scrollable>.row {
    position: absolute;
    top: 175px;
    bottom: 100px;
    width: 80%;
    overflow-y: scroll;
}
</style>
