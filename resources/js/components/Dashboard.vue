<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <img src="images/sinslogan2.png" style="width: 180px;" alt="logo">
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Iniciaste Sesion como: <b>{{ this.auth.user.correoElectronico }}</b></p>
                    </div>
                </div>
                <br>
                <div class="col-xxl-11 mx-auto p-2">
                    <div class="card shadow-lg">
                        <div class="card-body ">
                            <img src="images/img2.png" alt="logo" class="rounded float-start img-thumbnail">
                            <img src="images/img1.png" alt="logo" class="rounded float-end img-thumbnail">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

    </div>
</template>

<script>
export default {
    name: "dashboard",
    data() {
        return {
            user: this.auth.user
        }
    },
    created() {
        console.log(this.auth.user);
    }
}
</script>
