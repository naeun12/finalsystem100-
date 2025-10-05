<template>
    <div class="card border-0 shadow-lg mt-5 py-4 px-3 mx-auto"
        style="width: 700px; border-radius: 25px; background: #ffffff;">

        <!-- Header -->
        <div class="card-body">
            <h1 class="text-center mb-2 mt-3 fw-bold text-primary">🏠 Hello Tenant</h1>
            <p class="text-center mb-4 text-muted fs-6">
                Welcome back! Please log in to explore available dorms and manage your bookings.
            </p>
        </div>

        <!-- Tenant Login Form -->
            <div class="row px-4">
                <!-- Email -->
                <div class="mt-3">
                    <label for="email" class="form-label fw-semibold text-dark">
                        Email Address
                    </label>
                    <input type="email" name="email" id="email" class="form-control p-3 shadow-sm rounded border-2"
                        v-model="email" placeholder="Enter your email" style="border-color: #4edce2;" />
                    <span v-if="errors.email" class="text-danger small mt-1 d-block">
                        {{ errors.email[0] }}
                    </span>
                </div>

                <!-- Password -->
                <div class="mt-3">
                    <label for="password" class="form-label fw-semibold text-dark">
                        Password
                    </label>
                    <div class="input-group shadow-sm rounded border-2" style="border: 2px solid #4edce2;">
                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                            v-model="password" class="form-control border-0 p-3" placeholder="Enter your password" />
                    </div>
                    <span v-if="errors.password" class="text-danger small mt-1 d-block">
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
                        <button @click="TenantLogin" class="btn btn-primary rounded-pill w-100 py-2 shadow-sm"
                            style="background: linear-gradient(135deg, #4edce2, #1fb6ff); border: none; font-weight: 600; transition: all 0.3s;"
                            @mouseover="event.target.style.opacity = '0.9'"
                            @mouseout="event.target.style.opacity = '1'">
                            Sign In
                        </button>
                    </div>
                </div>
            </div>

        <!-- Signup Link -->
        <p class="text-center mt-4 mb-2 text-muted">
            Don’t have an account?
            <a @click="clickSignupLink" style="cursor: pointer; font-weight: 600;"
                class="text-decoration-none text-primary">
                Sign up here
            </a>
        </p>

        <!-- Toast Notification -->
        <div :class="['container-toast mt-4', { show: toaster }]" v-show="toaster">
            <div :class="['toast-child', `bg-${toastColor}`]" style="border-radius: 12px; overflow: hidden;">
                <div class="toast-body d-flex justify-content-between align-items-center text-white fw-bold py-3 px-4">
                    <span class="text-wrap">{{ this.messageToaster }}</span>
                    <button type="button" class="btn-close btn-close-white ms-3" @click="ExitToaster"
                        aria-label="Close"></button>
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
            email: "",
            password: "",
            messageToaster: "",
            errors: {},
            toaster: false,
            toastColor: ""
        };

    },
    methods: {
        async TenantLogin() {
            if (this.loginValidation()) {

                const formData = new FormData();
                formData.append('email', this.email);
                formData.append('password', this.password);

                try {
                    const response = await axios.post('/tenant-login', formData, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }

                    });
                    if (response.data.status === "success") {
                        const userId = response.data.tenant.id;

                        const userName = response.data.tenant.firstname;
                        localStorage.setItem('tenant', JSON.stringify(response.data.tenant));
                        window.location.href = `/homepage/${userId}`;
                        return true;
                    }
                }

                catch (error) {
                    this.toaster = true;

                    if (error.response) {
                        if (error.response.status === 422) {
                            // Validation errors
                            this.toastColor = "danger";
                            this.messageToaster = "Please check your email and password.";
                        }
                        else if (error.response.status === 401) {
                            // Wrong credentials
                            this.toastColor = "danger";
                            this.messageToaster = error.response.data.message || "Invalid login credentials.";
                        }
                        else if (error.response.status === 403) {
                            // Deactivated account
                            this.toastColor = "warning";
                            this.messageToaster = error.response.data.message || "Your account is deactivated.";
                        }
                        else {
                            // Other errors
                            this.toastColor = "danger";
                            this.messageToaster = error.response.data.message || "An error occurred during login.";
                        }
                    } else {
                        this.toastColor = "danger";
                        this.messageToaster = "Network error. Please try again.";
                    }

                    this.toasterTimeOut();
                }


            }
            else {
                this.toaster = true;
                this.toastColor = "danger";
                this.messageToaster = "Check Your Fields";
                this.toasterTimeOut();
            }

        },
        clickSignupLink() {
            window.location.href = `/tenantRegister`;

        },

        fill() {
            this.email = "";
            this.password = "";
            this.messageToaste = "";
        },
        loginValidation() {
            this.errors = {};
            if (!this.email) this.errors.email = ['Please enter your email address.'];
            if (!this.password) this.errors.password = ['Please enter your password.'];

            return Object.keys(this.errors).length === 0;

        },
        toggleShowPassword() {
            const passwordField = document.getElementById('password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
        },
        ExitToaster() {
            this.toaster = false;
            this.messageToaster = "";
        },
        toasterTimeOut() {
            setTimeout(() => {
                this.toaster = false;
                this.toastColor = "";
                this.messageToaster = "";

            }, 5000

            );
        }

    },

}

</script>