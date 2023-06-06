<template>
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-xl-12">
                <div class="card rounded-1 text-black">
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="card-body p-md-5 mx-md-4">

                                <div class="text-center">
                                    <img
                                        src="images/Icono.png"
                                        style="width: 185px;" alt="logo">
                                    <h4 class="mt-1 mb-2 pb-1">Secure Bank</h4>
                                </div>
                                <div class="text-center">
                                    <p class="fs-5">Crea tu cuenta aquí</p>
                                </div>
                                <form v-on:submit.prevent="onSubmit">
                                    <div class="row">
                                        <div class="col form-outline mb-4">
                                            <label class="form-label" for="dui">DUI:</label>
                                            <input type="text" id="dui" name="dui" class="form-control"
                                                   required v-model="user.dui"/>
                                        </div>

                                        <div class="col form-outline mb-4">
                                            <label class="form-label" for="nit">NIT:</label>
                                            <input type="text" id="nit" name="nit" class="form-control"
                                                   required v-model="user.nit"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col form-outline mb-4">
                                            <label class="form-label" for="nombres">Nombres:</label>
                                            <input type="text" id="nombres" name="nombres" class="form-control"
                                                   required v-model="user.nombres"/>
                                        </div>

                                        <div class="col form-outline mb-4">
                                            <label class="form-label" for="apellidos">Apellido:</label>
                                            <input type="text" id="apellidos" name="apellidos" class="form-control"
                                                   required v-model="user.apellidos"/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col form-outline mb-4">
                                            <label class="form-label" for="email">Correo Electronico:</label>
                                            <input type="email" id="email" name="email" class="form-control"
                                                   required v-model="user.email"/>
                                        </div>

                                        <div class="col form-outline mb-4">
                                            <label class="form-label" for="fecha_nacimiento">Fecha Nacimiento:</label>
                                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                                                   class="form-control"
                                                   required v-model="user.fecha_nacimiento"/>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col form-outline mb-4">
                                            <label class="form-label" for="telefono">Telefono:</label>
                                            <input type="tel" id="telefono" name="telefono"
                                                   class="form-control" required v-model="user.telefono"/>
                                        </div>

                                        <div class="col form-outline mb-4">
                                            <label class="form-label" for="telefono_trabajo">Telefono Trabajo:</label>
                                            <input type="tel" id="telefono_trabajo" name="telefono_trabajo"
                                                   class="form-control" v-model="user.telefono_trabajo"/>
                                        </div>
                                    </div>


                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="direccion">Direccion:</label>
                                        <textarea class="form-control" id="direccion" name="direccion" required
                                                  v-model="user.direccion">
                                        </textarea>
                                    </div>


                                    <div class="row">
                                        <div class="col form-outline mb-4">
                                            <label class="form-label">Genero:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="genero"
                                                       v-model="user.genero"
                                                       id="rMasculino"
                                                       required
                                                       value="M">
                                                <label class="form-check-label" for="rMasculino">
                                                    Hombre
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="genero"
                                                       id="rFemenino"
                                                       v-model="user.genero"
                                                       required
                                                       value="F">
                                                <label class="form-check-label" for="rFemenino">
                                                    Mujer
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col form-outline mb-4">
                                            <label class="form-label">Estado Civil:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="estado_civi"
                                                       id="rSoltero" value="Soltero"
                                                       v-model="user.estado_civil"
                                                       required>
                                                <label class="form-check-label" for="rSoltero">
                                                    Soltero
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="estado_civi"
                                                       v-model="user.estado_civil"
                                                       id="rCasado"
                                                       value="Casado"
                                                       required>
                                                <label class="form-check-label" for="rCasado">
                                                    Casado
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="estado_civi"
                                                       id="rDivorciado" value="Divorciado"
                                                       v-model="user.estado_civil"
                                                       required>
                                                <label class="form-check-label" for="rDivorciado">
                                                    Divorciado
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="estado_civi"
                                                       id="rViudo"
                                                       value="Viudo"
                                                       v-model="user.estado_civil"
                                                       required>
                                                <label class="form-check-label" for="rViudo">
                                                    Viudo
                                                </label>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="text-center pt-1 mb-5 pb-1">
                                        <button class="btn btn-block fa-lg gradient-custom-2 mb-3 mx-3
                                                        text-white"
                                                type="submit">
                                            Registrarse
                                        </button>
                                        <button class="btn btn-outline-secondary btn-block fa-lg mb-3"
                                                type="Reset">
                                            Limpiar
                                        </button>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-center pb-4">
                                        <p class="mb-0 me-2">¿Ya tienes una cuenta?</p>
                                        <button type="button" class="btn btn-outline-danger">Login</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                        <div class="col-lg-6 d-flex align-items-center gradient-custom-2 rounded-1">
                            <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                <h4 class="mb-4">SEGURIDAD Y FINANZAS DE LAS MANOS 🔐</h4>
                                <p class="fst-italic mb-0">
                                    Preocupate en administrar tu dinero a tu estilo 💸 mientras nosotros
                                    nos aseguraremos en que tus transacciones sean lo mas seguras, rapidas y eficientes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            user: {
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
            }
        };
    },
    methods: {
        onSubmit(e) {
            console.log(this.user);
            /*this.axios.post('http://127.0.0.1:8000/api/register', this.user)
                .then(({data}) => {
                    //this.$router.push('/login');
                    console.log("Llego")
                })
                .catch((error) => {
                    console.log(error);
                });*/
        }
    },
}
</script>
<style>

@import url('https://fonts.googleapis.com/css2?family=Bangers&display=swap');

.gradient-custom-2 {
    /* fallback for old browsers */
    background: #fccb90;

    /* Chrome 10-25, Safari 5.1-6 */
    background: -webkit-linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);

    /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
    background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
}

@media (min-width: 768px) {
    .gradient-form {
        height: 100vh !important;
    }
}

@media (min-width: 769px) {
    .gradient-custom-2 {
        border-top-right-radius: .3rem;
        border-bottom-right-radius: .3rem;
    }
}
</style>
