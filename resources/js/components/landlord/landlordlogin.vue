<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <div class="card border-primary shadow-lg mt-5 py-3" style="width: 700px; height:auto;">
        <div class="card-body ">
            <h1 class="text-center mb-2 mt-4 text-create">Hello Landlord</h1>
            <p class="text-center mb-4 text-muted">Welcome back! Please log in to manage your properties and connect
                with
                tenants.</p>
        </div>
        <form @submit.prevent="LandlordLogin">

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


        <p class="text-center mt-5">
            Hello Landlord! Don't have an account?
            <a @click="clickSignupLink" style="cursor: pointer;" class="text-primary">Sign up here.</a>
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
            email: "",
            password: "",
            errors: {},
            linkSignup: '',
        };

    },
    methods: {
        async LandlordLogin() {
            this.$refs.loader.loading = true;

            const formData = new FormData();
            formData.append('email', this.email);
            formData.append('password', this.password);

            try {
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
                    window.location.href = "/landlordDashboard";
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
                        this.$refs.toast.showToast(error.response.data.message, 'danger');
                        this.errors = {};
                    }
                }
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