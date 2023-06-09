<template>
    <div class="container py-2 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-xl-12">
                <div class="card rounded-1 text-black">
                    <div class="row g-0">
                        <div class="col-lg-6">
                            <div class="card-body p-md-5 mx-md-4">

                                <div class="text-center">
                                    <img src="images/sinslogan.png" style="width: 185px;" alt="logo">
                                </div>
                                <form v-if="mensajeEnviado === false" action="javascript:void(0)" method="post">
                                    <div class="text-center text">
                                        <p class="fs-5">Inicia Sesión Aquí</p>
                                    </div>
                                    <div class="col-12" v-if="Object.keys(validationErrors).length > 0 && typeof validationErrors === 'object'">
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <li v-for="(value, key) in validationErrors" :key="key">
                                                    {{ value[0] }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-12" v-if="Object.keys(validationErrors).length > 0 && typeof validationErrors === 'string'">
                                        <div class="alert alert-danger">
                                            {{validationErrors}}
                                        </div>
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label for="email" class="form-label text2">Correo</label>
                                        <input type="email" class="form-control" v-model="auth.email">
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label for="password" class="form-label text2">Contraseña</label>
                                        <input type="password" class="form-control" v-model="auth.password">
                                    </div>
                                    <div class="text-center pt-1 mb-5 pb-1">
                                        <button class="btn btn-block fa-lg gradient-custom-2 mb-3 mx-3
                                                        text-white" :disabled="processing" @click="login">
                                            {{ processing ? "Espere" : "Login" }}
                                        </button>
                                    </div>
                                    <div class="col-12 text-center mt-3 text2">
                                        <label>¿No tienes una cuenta?
                                            <router-link :to="{name:'register'}">Registrate Ahora</router-link>
                                        </label>
                                    </div>
                                </form>

                                <form v-if="mensajeEnviado === true" action="javascript:void(0)" method="post">
                                    <div class="col-12" v-if="Object.keys(validationErrorsSms).length > 0 && typeof validationErrorsSms === 'object'">
                                        <div class="alert">
                                            <ul class="mb-0">
                                                <li v-for="(value, key) in validationErrorsSms" :key="key">
                                                    {{ value[0] }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-12" v-if="Object.keys(validationErrorsSms).length > 0 && typeof validationErrorsSms === 'string'">
                                        <div class="alert">
                                            {{validationErrorsSms}}
                                        </div>
                                    </div>

                                    <div class="my-3">
                                        <p>
                                            Se ha enviado un codigo de 6 digitos al numero {{this.usuario.telefono_secret}}
                                        </p>
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label for="email" class="form-label">Codigo</label>
                                        <input type="text" class="form-control" v-model="usuario.code">
                                    </div>
                                    <div class="text-center pt-1 mb-5 pb-1">
                                        <button class="btn btn-block fa-lg gradient-custom-2 mb-3 mx-3
                                                        text-white" :disabled="processing" @click="validarTwillo">
                                            {{ processing ? "Espere" : "Validar" }}
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <div class="col-lg-6 d-flex align-items-center gradient-custom-2 rounded-1">
                            <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                <h4 class=" text mb-4">SEGURIDAD Y FINANZAS DE LAS MANOS 🔐</h4>
                                <p class=" text2 fst-italic mb-0">
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
import {mapActions} from 'vuex'

export default {
    name: "login",
    data() {
        return {
            auth: {
                email: "",
                password: ""
            },
            usuario: {
                code: "",
                email: "",
                password: "",
                verification_sid: "",
                telefono_secret: ""
            },
            validationErrors: {},
            validationErrorsSms: {},
            processing: false,
            mensajeEnviado: false,

        }
    },
    methods: {
        ...mapActions({
            signIn: 'auth/login'
        }),
        async login() {
            this.processing = true
            await axios.post('http://127.0.0.1:8000/api/usuario/sms', this.auth).then((response) => {
                //this.signIn()
                console.log(response);
                this.usuario.verification_sid = response.data.data.verification_sid;
                this.usuario.telefono_secret = response.data.data.telefono_secret;
                this.usuario.email = this.auth.email;
                this.usuario.password = this.auth.password;

                // Muestra el form de validacion
                this.mensajeEnviado = true;
            }).catch(({response}) => {
                if (response.status === 422) {
                    this.validationErrors = response.data.data;
                    alert(response.data.message);
                } else {
                    this.validationErrors = {}
                    alert(response.data.message);
                }
            }).finally(() => {
                this.processing = false
            });
        },
        async validarTwillo() {
            this.processing = true;
            await axios.post('http://127.0.0.1:8000/api/usuario/login',this.usuario).then((response)=>{
                this.signIn()
                console.log(response);
            }).catch(({response})=>{
                console.error(response.data);
                if(response.status===401 || response.status === 422){
                    this.validationErrorsSms = response.data.data;
                    alert(response.data.message);
                }else{
                    this.validationErrorsSms = {}
                    alert(response.data.message);
                }
            }).finally(()=>{
                this.processing = false
            });
        }
    }
}
</script>
