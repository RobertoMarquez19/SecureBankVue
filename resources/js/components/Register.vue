<template>
    <div class="box align-items-center text-center gradient-custom">
        <br>
        <div>
            <h4 class="text  text-light"><img src="images/icono.png" style="width: 50px;" alt="logo">
                Creá una cuenta</h4>
        </div>
    </div>
    <div class="container py-3 h-100 ">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-xl-12">
                <div class="card mx-4 mx-md-5 shadow-5-strong sombra">
                    <div class="row g-0">
                        <div class="card-body p-md-5 mx-md-4">
                            <form action="javascript:void(0)" @submit="registerCliente" class="row text2"
                                  method="post">
                                <h5 class="text text-center ">Formulario de registro</h5>
                                <p class="text2 my-5 text-center">Crea una cuenta y disfruta de todos nuestros
                                    beneficios.</p>
                                <div class="col-12"
                                     v-if="Object.keys(validationErrors).length > 0">
                                    <div class="alert alert-danger">
                                        <ul>
                                            <li v-for="value in validationErrors">
                                                {{ value }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="dui">DUI:</label>
                                        <input type="text" id="dui" name="dui" placeholder="00000000-0" class="form-control" required
                                               v-model="cliente.dui" pattern="[0-9]{8}-[0-9]{1}" title="Numero de DUI con Guiones"/>
                                    </div>

                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="nit">NIT:</label>
                                        <input type="text" id="nit" name="nit" placeholder="0000-000000-000-0" class="form-control" required
                                               v-model="cliente.nit" pattern="[0-9]{4}-[0-9]{6}-[0-9]{3}-[0-9]{1}" title="Numero de NIT con Guiones"/>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="nombres">Nombres:</label>
                                        <input type="text" id="nombres" name="nombres" placeholder="Tus Nombres" class="form-control" required
                                               v-model="cliente.nombres" pattern="[A-Za-z\s]*$" title="Primer y Segundo Nombre"/>
                                    </div>

                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="apellidos">Apellido:</label>
                                        <input type="text" id="apellidos" name="apellidos" placeholder="Tus Apellidos" class="form-control"
                                               required v-model="cliente.apellidos" pattern="[A-Za-z\s]*$" title="Primer y Segundo Apellido"/>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="email">Correo Electronico:</label>
                                        <input type="email" id="email" name="email" placeholder="Tu Correo Electronico" class="form-control" required
                                               v-model="cliente.email" title="Correo Electronico Valido"/>
                                    </div>

                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="fecha_nacimiento">Fecha Nacimiento:</label>
                                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                                               class="form-control" required v-model="cliente.fecha_nacimiento"/>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="telefono">Teléfono:</label>
                                        <input type="tel" id="telefono" name="telefono" placeholder="00000000" class="form-control"
                                               required v-model="cliente.telefono" pattern="[0-9]{8}" title="Numero Telefonico sin guiones"/>
                                    </div>

                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="telefono_trabajo">Teléfono Trabajo:</label>
                                        <input type="tel" id="telefono_trabajo" placeholder="00000000" name="telefono_trabajo"
                                               class="form-control" v-model="cliente.telefono_trabajo" pattern="[0-9]{8}"
                                               title="Numero Telefonico sin guiones"/>
                                    </div>
                                </div>


                                <div class="form-outline mb-4">
                                    <label class="form-label" for="direccion">Dirección:</label>
                                    <textarea class="form-control" id="direccion" name="direccion" required
                                              v-model="cliente.direccion" placeholder="Tu Direccion">
                                                    </textarea>
                                </div>


                                <div class="row">
                                    <div class="col form-outline mb-4">
                                        <label class="form-label">Genero:</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="genero"
                                                   v-model="cliente.genero" id="rMasculino" required value="M">
                                            <label class="form-check-label" for="rMasculino">
                                                Hombre
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="genero"
                                                   id="rFemenino" v-model="cliente.genero" required value="F">
                                            <label class="form-check-label" for="rFemenino">
                                                Mujer
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col form-outline mb-4">
                                        <label class="form-label">Estado Civil:</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civi"
                                                   id="rSoltero" value="Soltero" v-model="cliente.estado_civil"
                                                   required>
                                            <label class="form-check-label" for="rSoltero">
                                                Soltero
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civi"
                                                   v-model="cliente.estado_civil" id="rCasado" value="Casado" required>
                                            <label class="form-check-label" for="rCasado">
                                                Casado
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civi"
                                                   id="rDivorciado" value="Divorciado" v-model="cliente.estado_civil"
                                                   required>
                                            <label class="form-check-label" for="rDivorciado">
                                                Divorciado
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="estado_civi"
                                                   id="rViudo" value="Viudo" v-model="cliente.estado_civil" required>
                                            <label class="form-check-label" for="rViudo">
                                                Viudo
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="password">Contraseña:</label>
                                        <input type="password" id="password" placeholder="••••••••••" name="password" class="form-control"
                                               required title="8 caracteres minimo"
                                               v-model="cliente.password"/>
                                    </div>

                                    <div class="col form-outline mb-4">
                                        <label class="form-label" for="password_confirm">Repetir Contraseña:</label>
                                        <input type="password" id="password_confirm" placeholder="••••••••••" name="password_confirm"
                                               class="form-control" required v-model="cliente.password_confirmation" title="8 caracteres minimo"/>
                                    </div>
                                </div>


                                <div class="text-center pt-1 mb-5 pb-1">
                                    <button class="btn btn-block fa-lg gradient-custom-2 mb-3 mx-3
                                                                    text-white" :disabled="processing">
                                        {{ processing ? "Espere" : "Registrarse" }}
                                    </button>
                                    <button class="btn gradient-custom-2 text-light btn-block fa-lg mb-3" type="Reset">
                                        Limpiar
                                    </button>
                                </div>

                                <div class="d-flex align-items-center justify-content-center pb-4">
                                    <label>¿Ya tienes una cuenta?
                                        <router-link :to="{name:'login'}">Login</router-link>
                                    </label>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2/dist/sweetalert2';

export default {
    name: 'register',
    data() {
        return {
            cliente: {
                dui: '',
                nit: '',
                nombres: '',
                apellidos: '',
                email: '',
                telefono: '',
                telefono_trabajo: '',
                direccion: '',
                genero: '',
                estado_civil: '',
                password: '',
                password_confirmation: ''
            },
            validationErrors: {},
            processing: false
        }
    },
    methods: {
        // TODO: Mostrar una SweetAlert una ves el usuario se registre correctamente, diciendole que se ha enviado un correo para confirmar su cuenta
        // TODO: Limpiar campos y redirigir al Login una ves se de aceptar en la SweetAlert
        // TODO: Agregar mascaras a los campos, con placeholders
        async registerCliente() {
            this.processing = true
            console.log(this.cliente);
            await axios.post('usuario/register', this.cliente).then(response => {
                this.validationErrors = {}
                console.log(response);
                let mensaje = response.data.message;
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: mensaje,
                    confirmButtonText: 'Entendido',
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.$router.push({name: 'login'})
                    }
                })
            }).catch(({response}) => {
                let mensajeError = response.data.message;
                if (response.status === 422) {
                    this.validationErrors = response.data.data
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: mensajeError,
                        footer: 'Revise las validaciones en el formulario'
                    });
                } else {
                    this.validationErrors = response.data.data
                    Swal.fire({
                        icon: 'error',
                        title: 'Error ah ocurrido algo inesperado',
                        text: this.validationErrors,
                        footer: 'Estamos trabajando para resolverlo'
                    });
                }
            }).finally(() => {
                this.processing = false
            })
        },
    }
}
</script>
