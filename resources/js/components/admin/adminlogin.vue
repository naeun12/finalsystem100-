<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <div class="card border-0 shadow-lg mt-5 py-4 px-3 mx-auto"
        style="width: 700px; border-radius: 25px; background: #ffffff;">
        <div class="card-body">
            <h1 class="text-center mb-2 mt-3 fw-bold text-primary">👋 Hello Admin</h1>
            <p class="text-center mb-4 text-muted fs-6">
                Welcome back, Admin! Please log in to continue managing your properties. </p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="adminLogin">
            <div class="row px-4">
                <!-- Email -->
                <div class="mt-3">
                    <label for="username" class="form-label fw-semibold">Username</label>
                    <input type="username" name="username" id="username" class="form-control p-3" v-model="username"
                        placeholder="Enter your username" style="border: 2px solid #4edce2; border-radius: 12px;">
                    <span v-if="errors.username" class="text-danger small">
                        {{ errors.username[0] }}
                    </span>
                </div>

                <!-- Password -->
                <div class="mt-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" v-model="password" class="form-control p-3"
                            placeholder="Enter your password" style="border: 2px solid #4edce2; border-radius: 12px;">
                    </div>
                    <span v-if="errors.password" class="text-danger small">
                        {{ errors.password[0] }}
                    </span>
                </div>

                <!-- Show Password -->
                <div class="mt-3 d-flex align-items-center">
                    <input type="checkbox" id="show-password" name="show-password" class="form-check-input me-2"
                        style="border: 2px solid #4edce2;" @click="toggleShowPassword">
                    <label for="show-password" class="form-label m-0">Show Password</label>
                </div>

                <!-- Sign In Button -->
                <div class="container d-flex justify-content-center">
                    <div class="w-75 mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 shadow-sm"
                            style="background: linear-gradient(135deg, #4edce2, #1fb6ff); border: none; font-weight: 600; transition: all 0.3s;">
                            Sign In
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Signup Link -->
       
    </div>


</template>
<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';

export default {
    components: {
        Toastcomponents,
        Loader

    },
    data() {
        return {
            username: "",
            password: "",
            errors: {},
            linkSignup: '',
        };

    },
    methods: {
        async adminLogin() {
            this.$refs.loader.loading = true;

            const formData = new FormData();
            formData.append('username', this.username);
            formData.append('password', this.password);

            try {
                const response = await axios.post('/login-admin', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    const userId = response.data.admin.id;
                    window.location.href = `/admin/dashboard/${userId}`;
                    this.$refs.loader.loading = false;;
                    this.errors = {};
                    this.fill();

                    return true;
                }
            }
            catch (error) {
                this.$refs.loader.loading = false;
                if (error.response) {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.message;

                    }
                    if (error.response.status === 401) {
                        console.log("Validation failed:", error.response.data.message);
                        this.errors = error.response.data.message;
                        this.$refs.toast.showToast(error.response.data.message, 'danger');
                        this.errors = {};
                    }
                }
            }

        },

        fill() {
            this.username = "";
            this.password = "";
            this.messageToaste = "";
        },
        toggleShowPassword() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
        },
     


    },

}

</script>