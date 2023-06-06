<template>
    <div class="container py-2 h-100">
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
                                    <p class="fs-5">Inicia Sesion Aqui</p>
                                </div>
                                <form v-on:submit.prevent="onSubmit">
                                    <form v-on:submit.prevent="onSubmit">
                                        <!--<h1>Login</h1> -->
                                        <div class="form-outline mb-4">
                                            <label for="email" class="form-label">Correo</label>
                                            <input type="email" class="form-control" v-model="user.email">
                                        </div>
                                        <div class="form-outline mb-4">
                                            <label for="password" class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" v-model="user.password">
                                        </div>
                                        <button class="btn btn-outline-primary" type="submit">Login</button>
                                    </form>
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
import axios from 'axios';
export default {
    data() {
        return {
            user: {
                email: '',
                password: '',
            }
        };
    },
    methods: {
        onSubmit(e) {
            console.log(this.user);
            const submit = async () => {
                await axios
                    .post('http://127.0.0.1:8000/api/usuario/login', this.user)
                    .then((response) => {
                        console.log(response);
                    })
                    .catch((error) => {
                        console.error(error)
                        //validation.value = error.response.data;
                    });
            }

            submit();
        }
    }
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
