<script>
import Auth from "@/store/auth.js";
import Swal from 'sweetalert2/dist/sweetalert2';

export default {
    name: "tarjetas",
    data() {
        return {
            user: this.auth.user,
            tarjetasCredito: [],
            tarjetaCreditoSeleccionada: null,
            pagoFactura: {
                numero_tarjeta: "",
                npe: ""
            },
            movimientos: [],
            spinnerTarjetas: false,
            spinnerMovimientos: false,
        }
    },
    async mounted() {
        await this.obtenerTarjetasCredito();
    },
    methods: {
        async crearTarjetaCredito() {
            await axios.post('cliente/tarjetascredito').then(response => {
                this.obtenerTarjetasCredito();
            }).catch(({response}) => {
                let mensajeError = response.data.data;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensajeError
                });
            });
        },
        async obtenerTarjetasCredito() {
            this.spinnerTarjetas = true;
            await axios.get('cliente/tarjetascredito').then(response => {
                this.tarjetasCredito = response.data.data;
                this.spinnerTarjetas = false;
            }).catch(({response}) => {
                let mensajeError = response.data.data;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensajeError
                }).finally(()=> {
                    this.spinnerTarjetas = false
                })
            });
        },

        async validarNpe() {
            this.pagoFactura.numero_tarjeta = this.tarjetaCreditoSeleccionada.numero_tarjeta
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
                        return axios.post('cliente/tarjetascredito/facturas/pago', this.pagoFactura).then((response) => {
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
                        this.obtenerTarjetasCredito()
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

        async obtenerMovimientos(tarjeta){
            this.spinnerMovimientos = true;
            await axios.post('cliente/tarjetascredito/transferencias',{numero_tarjeta:tarjeta.numero_tarjeta}).then(response => {
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


        clearFormPago() {
            this.pagoFactura = {
                numero_tarjeta: "",
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
    <h1 class="text-center">  <i class="bi bi-credit-card-2-back"></i> Tarjetas de credito</h1>

    <!-- Modal de Pago de Facturas -->
    <div class="modal fade" id="pagoModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Pago de servicio</h1>
                </div>
                <form v-if="tarjetaCreditoSeleccionada!=null" ref="pagoForm" @submit.prevent="validarNpe()">
                    <div class="modal-body">
                        <label for="cuenta_origen" class="form-label mt-2">Tarjeta</label>
                        <input class="form-control" type="text" placeholder="Disabled input"
                               aria-label="Disabled input example" :value="tarjetaCreditoSeleccionada.numero_tarjeta" disabled
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

    <!-- Modal de Movimientos de Tarjeta -->
    <div class="modal fade " id="movimientosModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Historial de transacciones</h1>
                </div>
                <div class="modal-body">
                    <div v-if="movimientos.length === 0" class="text-center">
                        <p class="fs-5"><i class="bi bi-currency-exchange"></i> No posee historial de transacciones con esta tarjeta</p>
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

    <form @submit.prevent="crearTarjetaCredito()" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="col-12">
            <span class="align-middle">¿No tienes una tarjeta de credito? Puedes solicitarla aqui</span>
        </div>
        <div class="col-12">
            <button type="submit" class="btn gradient-custom-2 btn col m-1 text-white">Solicitar tarjeta</button>
        </div>
    </form>

    <hr class="my-4">

    <!-- Mensaje de ninguna cuenta Inactiva Y Spinner de Carga -->
    <div v-if="tarjetasCredito.length === 0" class="text-center">
        <p class="fs-5"><i class="bi bi-credit-card-2-back"></i> No posee tarjetas de credito</p>
    </div>

    <div v-if="spinnerTarjetas" class="mx-auto text-center mt-5">
        <div class="spinner-grow" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-sm vertical-scrollable">
        <div class="row">
            <div class="col-sm-6 mt-3 mb-3 mb-sm-0" v-for="(tarjeta, index) in tarjetasCredito" :key="index">
                <div class="card" data-bs-toggle="collapse" :data-bs-target="`#collapse-tarjeta-`+index"
                     aria-expanded="false" :aria-controls="`collapse-tarjeta-`+index">
                    <div class="card-header fw-bold">
                        <i class="bi bi-credit-card"></i> {{ tarjeta.numero_tarjeta }}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold">${{ tarjeta.monto }}</h5>
                        <p class="card-text">
                            Saldo Disponible
                        <p>{{tarjeta.tipo.marca}} {{tarjeta.tipo.tipo}}</p>
                        <p>{{tarjeta.fecha_vencimiento}}</p>
                        </p>
                    </div>

                    <div class="collapse" :id="`collapse-tarjeta-`+index">
                        <div class="container text-center">
                            <hr>
                                <button type="button" class="gradient-custom-2 btn col m-1 text-white"
                                        data-bs-toggle="modal" data-bs-target="#pagoModal"
                                        @click="tarjetaCreditoSeleccionada = tarjeta"><i class="bi bi-receipt"></i>
                                    Pagar servicio
                                </button>
                                <button type="button" class="gradient-custom-2 btn col m-1 text-white"
                                        data-bs-toggle="modal" data-bs-target="#movimientosModal"
                                        @click="obtenerMovimientos(tarjeta)"><i
                                    class="bi bi-card-checklist"></i> Movimientos
                                </button>
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
