<template>

    <form @submit.prevent="TenantLogin">

        <div class="row  m-2">
            <div class="mt-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" v-model="email"
                    placeholder="Email Address" style="border: 2px solid #4edce2;">
                <span v-if="errors.email" class="text-danger">
                    {{ errors.email[0] }}
                </span>
            </div>
            <div class="mt-2">

                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" v-model="password" class="form-control"
                        placeholder="Password" style="border: 2px solid #4edce2;">

                </div>
                <span v-if="errors.password" class="text-danger">{{ errors.password[0] }}</span>

            </div>
            <div class="mt-2 ">

                <label for="show-password" class="form-label  me-1">Show Password</label>

                <input type="checkbox" id="show-password" name="show-password" class="form-check-input"
                    style="border: 2px solid #4edce2;" @click="toggleShowPassword">

            </div>
            <div class="container d-flex justify-content-center">
                <div class=" gap-2  w-50 mt-5">
                    <button type="submit"
                        class="btn rounded-pill d-flex justify-content-center align-items-center w-100">Sign
                        In</button>
                </div>
            </div>
        </div>


    </form>
    <div :class="['container-toast mt-5', { show: toaster }]" v-show="toaster">
        <!-- Toast Container -->
        <div :class="['toast-child', `bg-${toastColor}`]">
            <div class="toast-body d-flex justify-content-between align-items-center text-white fw-bold py-3 px-4">
                <span class="text-wrap">{{ this.messageToaster }}</span>
                <button type="button" class="btn-close btn-close-white ms-3" @click="ExitToaster"
                    aria-label="Close"></button>
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
                    this.toastColor = "danger";

                    if (error.response && error.response.data && error.response.data.message) {
                        this.messageToaster = error.response.data.message;
                    } else {
                        this.messageToaster = "An error occurred during login.";
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