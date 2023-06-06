<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <RouterLink class="nav-link" to="/">Home</RouterLink>
                    </li>
                    <!--<li class="nav-item">
                        <RouterLink to="/about">About</RouterLink>
                    </li> -->
                    <li class="nav-item">
                        <RouterLink class="nav-link" to="/login">Login</RouterLink>
                    </li>
                    <li class="nav-item">
                        <RouterLink class="nav-link" to="/register">Register</RouterLink>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
import { useRouter } from 'vue-router';
export default {
    setup() {
        const router = useRouter();
        const Logout = () => {
            localStorage.clear();
            router.push({ path: '/login' });
        };
        return { Logout };
    },
};
</script>
