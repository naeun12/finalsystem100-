<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <div class="card border-0 shadow-lg mt-5 py-4 px-3 mx-auto"
        style="width: 700px; border-radius: 25px; background: #ffffff;">
        <div class="card-body">
            <h1 class="text-center mb-2 mt-3 fw-bold text-primary">👋 Hello Landlord</h1>
            <p class="text-center mb-4 text-muted fs-6">
                Welcome back! Please log in to manage your properties and connect with tenants.
            </p>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="LandlordLogin" novalidate>
            <div class="row px-4">
                <!-- Email -->
                <div class="mt-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" id="email" class="form-control p-3" v-model="email"
                        placeholder="Enter your email" style="border: 2px solid #4edce2; border-radius: 12px;">
                    <span v-if="errors.email" class="text-danger small">
                        {{ errors.email[0] }}
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
        <p class="text-center mt-4 mb-2 text-muted">
            Don’t have an account?
            <a @click="clickSignupLink" style="cursor: pointer; font-weight: 600;"
                class="text-decoration-none text-primary">
                Sign up here
            </a>
        </p>
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
            email: '',
            password: '',
            errors: {},
            linkSignup: '',
        };

    },
    methods: {
        async LandlordLogin() {

            const formData = new FormData();
            formData.append('email', this.email);
            formData.append('password', this.password);
            if (!this.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                this.errors.email = ["Please enter a valid email."]
                this.$refs.loader.loading = false
                return
            }


            try {
                this.$refs.loader.loading = true;

                const response = await axios.post('/loginLandlord', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    const userId = response.data.landlord.id;
                    const userName = response.data.landlord.firstname;
                    localStorage.setItem('landlord_id', userId);
                    localStorage.setItem('landlord_name', userName);
                    window.location.href = `/landlordDashboard/${userId}`;
                    this.$refs.loader.loading = false;;
                    this.errors = {};
                    this.fill();

                    return true;
                }
            }
            catch (error) {
                this.$refs.loader.loading = false;
                this.errors = {}; // clear previous errors
                if (error.response) {
                    if (error.response.status === 422) {
                        // Validation errors
                        this.errors = error.response.data.errors || {};
                    } else if (error.response.status === 401) {
                        this.$refs.toast.showToast(error.response.data.message, 'danger');
                    } else if (error.response.status === 403) {
                        this.$refs.toast.showToast(error.response.data.message, 'warning');
                    }
                }
            }

            finally { 
                this.$refs.loader.loading = false;

            }


        },

        fill() {
            this.email = "";
            this.password = "";
            this.messageToaste = "";
        },
        toggleShowPassword() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
        },
        clickSignupLink() {

            window.location.href = "/landlordregister";
        }


    },

}

</script>