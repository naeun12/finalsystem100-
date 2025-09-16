<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />

    <form @submit.prevent="nextStep">
        <!-- Steps Navigation -->
        <div class="nav-pills w-100 ">
            <ul class="nav mb-3 justify-content-center flex-wrap">
                <li class="" v-for="(step, index) in steps" :key="index">
                    <button class="btn btn-primary m-2" :class="{ active: currentStep === index }"
                        :disabled="index > currentStep">
                        {{ step }}
                    </button>
                </li>
            </ul>
        </div>
        <!-- Step Content -->
        <div class="tab-content">
            <!-- Step 1: Information -->
            <div v-if="currentStep === 0">
                <h2 class="text-center mb-4 text-create  mt-5">Create Your Landlord Account</h2>

                <div class="d-flex justify-content-center container-image ">

                    <div class="avatar-wrapper text-center">
                        <img class="profile-pic rounded-circle" :src="previewPic" alt="Profile Picture" width="150" />
                        <div class="upload-button btn btn-primary mt-3" @click="triggerProfileInput">
                            Upload Image
                            <input ref="fileInput" class="file-input" name="profile_pic" id="profile-pic" type="file"
                                accept="image/*" style="display: none;" @change="handleImageUpload" />

                        </div>


                    </div>



                </div>
                <div v-if="errors.profilePic" class="alert alert-danger text-center">
                    <span class="text-black  fs-bold">
                        {{ errors.profilePic[0] }}
                    </span>
                </div>


                <div class=" row mb-3">
                    <div class="col">
                        <label for="firstname" class="form-label">Firstname</label>
                        <input type="text" class="form-control" placeholder="Enter your firstname" v-model="firstname"
                            style="border: 2px solid #4edce2;" id="firstname" name="firstname" />

                        <span v-if="errors.firstname" class="text-danger">
                            {{ errors.firstname[0] }}
                        </span>


                    </div>
                    <div class="col">
                        <label for="lastname" class="form-label">Lastname</label>
                        <input type="text" class="form-control" placeholder="Enter your lastname" v-model="lastname"
                            style="border: 2px solid #4edce2;" name="lastname" id="lastname" />
                        <span v-if="errors.lastname" class="text-danger">
                            {{ errors.lastname[0] }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        placeholder="Enter your Email Address" v-model="email" autocomplete="email"
                        style="border: 2px solid #4edce2;" />
                    <span v-if="errors.email" class="text-danger">
                        {{ errors.email[0] }}
                    </span>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" placeholder="Enter your Password" v-model="password"
                            style="border: 2px solid #4edce2;" name="password" id="password" />
                        <span v-if="errors.password" class="text-danger">
                            {{ errors.password[0] }}
                        </span>
                    </div>
                    <div class="col">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" placeholder="Confirm Password"
                            v-model="password_confirmation" name="password_confirmation"
                            style="border: 2px solid #4edce2;" id="password_confirmation" />
                        <span v-if="errors.password_confirmation" class="text-danger">
                            {{ errors.password_confirmation[0] }}
                        </span>

                    </div>
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="showpassword" @click="showpassword" />
                    <label for="showpassword" class="mb-0">Show Password</label>
                </div>

                <div class=" mb-3">
                    <label for="phonenumber" class="form-label">Phone Number</label>
                    <input type="tel" id="phonenumber" class="form-control" placeholder="Enter your Phone Number"
                        v-model="phonenumber" style="border: 2px solid #4edce2;" name="phonenumber" />
                    <span v-if="errors.phonenumber" class="text-danger">
                        {{ errors.phonenumber[0] }}
                    </span>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" aria-label="Select Gender" name="gender" v-model="gender" id="gender"
                        style="border: 2px solid #4edce2;">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <span v-if="errors.gender" class="text-danger">
                        {{ errors.gender[0] }}
                    </span>
                </div>

            </div>
            <!-- Step 2: Government ID -->
            <!-- Step 1: Upload Government ID -->
            <div v-if="currentStep === 1">
                <h2 class="text-center mb-2 text-create">Upload Government ID</h2>

                <!-- Upload Box -->
                <div class="border border-secondary rounded-3 p-4 mb-3 text-center" style="cursor: pointer;"
                    @click="triggerGovIdInput">
                    <input ref="govIdInput" class="d-none" id="gov-id" type="file" accept="image/*"
                        @change="handleGovermentIdUpload" />

                    <!-- Icon + Text -->
                    <div class="d-flex flex-column align-items-center text-center mb-3">
                        <h5 class="text-secondary mt-2">Upload Government ID</h5>
                        <small class="text-muted">Click to browse and select an image file</small>
                    </div>
                </div>

                <!-- Image Preview -->
                <div v-if="govermentIdPicPreview" class="text-center mb-3">
                    <img :src="govermentIdPicPreview" alt="Uploaded ID" class="img-fluid rounded mb-2"
                        style="max-height: 250px;" />
                    <div>
                        <button type="button" @click="removeGovermentPermitPic" class="btn btn-sm">
                            Remove Uploaded Image
                        </button>
                    </div>
                </div>
            </div>


            <!-- Step 3: Business Permit -->
            <div v-if="currentStep === 2">
                <h2 class="text-center mb-2 text-create">Upload Business Permit</h2>

                <!-- Upload Box -->
                <div class="border border-secondary rounded-3 p-4 mb-3 text-center" style="cursor: pointer;"
                    @click="triggerBusinessPermitInput">
                    <input ref="businessPermitInput" class="d-none" id="business-permit" type="file" accept="image/*"
                        @change="handleBusinessPermitUpload" />

                    <!-- Icon + Text -->
                    <div class="d-flex flex-column align-items-center text-center mb-3">
                        <h5 class="text-secondary mt-2">Upload Business Permit</h5>
                        <small class="text-muted">Click to browse and select an image file</small>
                    </div>
                </div>

                <!-- Image Preview -->
                <div v-if="businessIdPicPreview" class="text-center mb-3">
                    <img :src="businessIdPicPreview" alt="Uploaded ID" class="img-fluid rounded mb-2"
                        style="max-height: 250px;" />
                    <div>
                        <button type="button" @click="removeBusinessPermitPic" class="btn btn-sm">
                            Remove Uploaded Image
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 4: OTP -->
            <div v-if="currentStep === 3" class="custom-modal d-flex justify-content-center align-items-center w-100 ">
                <div class="modal-wrapper text-center w-75">

                    <h2 class="mb-4 text-create">OTP Verification</h2>
                    <p class="mb-4">Please enter the verification OTP sent to your email.</p>
                    <!-- OTP Inputs -->
                    <div class="otp-inputs d-flex justify-content-center gap-2 mb-5">
                        <input v-for="(digit, index) in otpdigits" :key="index" type="text" :ref="'otpInput' + index"
                            maxlength="1" class="form-control text-center fs-4" name="codeotp"
                            v-model="otpdigits[index]" @input="handleInput(index, $event)"
                            @keydown.backspace="handleBackspace(index, $event)"
                            style="width: 50px; height: 50px; border: 2px solid #4edce2;" />
                    </div>


                    <!-- OTP Timer -->
                    <div class=" otp_timer mb-4">
                        <p class="primary" v-if="otpTimer > 0">OTP expires in: {{ formattedTime }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="modal-actions d-flex justify-content-center gap-3 mb-3">
                        <button type="button" @click="RegisterLandlord" class="btn px-4">Register</button>
                        <button type="button" @click="resendOtp" class="btn t px-4">Resend
                            OTP</button>


                    </div>
                </div>
            </div>


        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-4  text-create">
            <button type="button" class="btn " @click="prevStep" :disabled="currentStep === 0">
                Previous
            </button>
            <button type="button" class="btn" @click="nextStep" :disabled="currentStep === steps.length - 1">
                Next
            </button>
        </div>

    </form>

</template>
<script>
import Loader from '@/components/loader.vue';
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';

export default {
    components: {
        Toastcomponents,
        Loader

    },

    name: 'LandlordRegister',
    data() {
        return {
            //images
            previewPic: "/images/registertenant/Profile-PNG-Photo.png",
            govermentIdPicPreview: "",
            businessIdPicPreview: "",
            //messages, modals and toaster
            errors: {},
            //steps
            steps: ["Personal Details", "Identity Verification", "Business Documentation", "Email Verification"],
            currentStep: 0,
            //data
            firstname: "",
            lastname: "",
            email: "",
            password: "",
            password_confirmation: "",
            phonenumber: "",
            gender: "",
            profilePic: "",
            governmentIdFile: "",
            businessPermitFile: "",
            //loader and otp
            otpTimer: 0,
            otpdigits: Array(6).fill(''),


        };
    },
    //timer
  
    methods: {
        showToast(message, color = 'success') {
            this.messageToaster = message;
            this.toastColor = color;
            this.toaster = true;

            // Auto-hide after 3 seconds
            setTimeout(() => {
                this.ExitToaster();
            }, 3000);
        },
        ExitToaster() {
            this.toaster = false;
        },
        formattedTime() {

            const minutes = Math.floor(this.otpTimer / 60);
            const seconds = this.otpTimer % 60;
            return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        },
        //Steps 1 to 4
       async nextStep() {
            if (this.currentStep < this.steps.length - 1) {
                let isValid = true;

                if (this.currentStep === 0) {
                    isValid = this.PersonalDetails();



                } else if (this.currentStep === 1) {
                    isValid = this.IdentityVerification();
                }
                else if (this.currentStep === 2) {
                    isValid = this.BusinessDocumentation();
                }


            }
        },

        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
            }
        },
        goToStep(index) {
            if (index <= this.currentStep) {
                this.currentStep = index;
            }
        },
        // image handler
        //profilePic
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.profilePic = file;
                this.previewPic = URL.createObjectURL(file);


            } else {
                this.previewPic = "/images/registertenant/Profile-PNG-Photo.png";
            }
        },
        triggerProfileInput() {
            if (this.$refs.fileInput) {
                this.$refs.fileInput.click();
            }
        },
        //GovermentId Picture
        handleGovermentIdUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (this.govermentIdPicPreview) {
                    URL.revokeObjectURL(this.govermentIdPicPreview);
                }
                this.governmentIdFile = file;
                this.govermentIdPicPreview = URL.createObjectURL(file);
            }
        },
        removeGovermentPermitPic() {
            if (this.govermentIdPicPreview) {
                URL.revokeObjectURL(this.govermentIdPicPreview);
                this.govermentIdPicPreview = "";
                this.governmentIdFile = "";

            }
            this.govermentIdPicPreview = "";
            this.governmentIdFile = "";


            if (this.$refs.govIdInput) {
                this.$refs.govIdInput.value = ''; // Reset file input
            }
        },
        //Business Permit Picture
        handleBusinessPermitUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.businessIdPicPreview) {
                    URL.revokeObjectURL(this.businessIdPicPreview);
                }
                this.businessPermitFile = file;

                this.businessIdPicPreview = URL.createObjectURL(file);
            }
        },
        triggerBusinessPermitInput() {
            if (this.$refs.businessPermitInput) {
                this.$refs.businessPermitInput.click();
            }
        },
        triggerGovIdInput() {
            if (this.$refs.govIdInput) {
                this.$refs.govIdInput.click();
            }
        },
        removeBusinessPermitPic() {
            if (this.businessIdPicPreview) {
                URL.revokeObjectURL(this.businessIdPicPreview);
            }
            this.businessIdPicPreview = null;
            // Add null check for safety
            if (this.$refs.businessIdPicPreview) {
                this.$refs.businessIdPicPreview.value = ''; // Reset file input
            }
        },
        //validations 
        //Input Data
        //personal Details
        async PersonalDetails() {
            this.$refs.loader.loading = true;

            const formData = new FormData();
            formData.append('firstname', this.firstname.trim());
            formData.append('lastname', this.lastname.trim());
            formData.append('email', this.email.trim());
            formData.append('phonenumber', this.phonenumber.trim());
            formData.append('password', this.password.trim());
            formData.append('password_confirmation', this.password_confirmation.trim());
            formData.append("profilePic", this.profilePic);
            formData.append("gender", this.gender);
            try {
                const response = await axios.post('/personalDetails', formData, {
                    headers: {
                        // DON'T set Content-Type when using FormData
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === 'success') {
                    this.currentStep = 1;
                    this.$refs.loader.loading = false;
                    this.errors = {};

                    return true;
                }
            } catch (error) {
                this.$refs.loader.loading = false;
                console.clear();
                if (error.response) {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        this.errorMessage = error.response.data;
                    }
                }
            }
        },
        //Identify Verifaction
        async IdentityVerification() {
            this.$refs.loader.loading = true;

            const formData = new FormData();

            formData.append('governmentIdPic', this.governmentIdFile);
            try {
                const response = await axios.post('/IdentityVerifaction', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.currentStep = 2;
                    this.$refs.loader.loading = false;

                    return true;
                }
            }
            catch (error) {
                if (error.response) {
                    this.$refs.loader.loading = false;

                    if (error.response.status === 422) {
                        const errorMessages = Object.values(error.response.data.errors).flat().join('\n');
                        console.log('Validation errors:', errorMessages);
                        this.$refs.toast.showToast(errorMessages, 'danger');

                    } else {
                        console.error('Registration error:', error.response.data);
                    }
                } else {
                    console.error('Network error:', error.message);
                }
            }
            return true;
        },

        //Business Documentation
        async BusinessDocumentation() {
            this.$refs.loader.loading = true;

            const formData = new FormData();
            formData.append('businessPermitPic', this.businessPermitFile);
            formData.append('email', this.email);

            try {
                const response = await axios.post('/businessPermitValidation', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.startOtpTimer(response.data.timer);
                    this.currentStep = 3;
                    this.$refs.loader.loading = false;
                    return true;
                }
            }
            catch (error) {
                this.$refs.loader.loading = false;

                if (error.response) {

                    if (error.response.status === 422) {

                        const message = Object.values(error.response.data.message).flat().join('\n');
                        this.$refs.toast.showToast(message, 'danger');

                    } else {
                        const message = Object.values(error.response.data.message).flat().join('\n');
                        this.$refs.toast.showToast(message, 'danger');
                    }
                } else {
                    const message = Object.values(error.response.data.message).flat().join('\n');
                    this.$refs.toast.showToast(message, 'danger');
                }
            }
            finally {
                this.$refs.loader.loading = false;
            }
            return false;
        },
        //Register Landlord
        async RegisterLandlord() {
            this.$refs.loader.loading = true;

            const formData = new FormData();
            formData.append('firstname', this.firstname.trim());
            formData.append('lastname', this.lastname.trim());
            formData.append('email', this.email.trim());
            formData.append('password', this.password.trim());
            formData.append('password_confirmation', this.password_confirmation.trim());
            formData.append('phonenumber', this.phonenumber.trim());
            formData.append('gender', this.gender);
            formData.append("profilePic", this.profilePic);
            formData.append('governmentIdPic', this.governmentIdFile);
            formData.append('businessPermitPic', this.businessPermitFile);
            formData.append('codeotp', this.otpdigits.join(''));
            try {
                const response = await axios.post('/RegisterLandlord', formData, {
                    headers: {
                        // DON'T set Content-Type when using FormData
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    const message = Object.values(response.data.message).flat().join('\n');
                    this.$refs.toast.showToast(message, 'success');
                    this.$refs.loader.loading = false;
                    this.Emptyfill();
                    this.currentStep = 0;
                    this.errors = {};
                    return true;
                }
            }
            catch (error) {
                if (error.response) {
                    const response = error.response;
                    if (response.data.status === "error") {
                        const message = Object.values(response.data.message).flat().join('\n');
                        this.$refs.toast.showToast(message, 'danger');
                        this.$refs.loader.loading = false;

                    }
                } else {
                    const message = Object.values(response.data.message).flat().join('\n');
                    this.$refs.toast.showToast(message, 'danger');
                    this.$refs.loader.loading = false;

                }
            }
            return false;

        },

        //Timer and OTP
        startOtpTimer(timerValue) {
            const expirationTime = new Date(timerValue);
            const currentTime = new Date();
            let remainingSeconds = Math.floor((expirationTime - currentTime) / 1000);

            this.otpTimer = remainingSeconds;

            if (this.otpInterval) {
                clearInterval(this.otpInterval);
            }

            this.otpInterval = setInterval(() => {
                if (remainingSeconds <= 0) {
                    clearInterval(this.otpInterval);
                    this.otpTimer = 0;
                } else {
                    this.otpTimer = remainingSeconds;
                    remainingSeconds--;
                }
            }, 1000);
        },
        startTimer() {
            this.intervalId = setInterval(() => {
                if (this.otpTimer > 0) {
                    this.otpTimer--;
                } else {
                    this.stopTimer();
                }
            }, 1000);
        },
        stopTimer() {
            clearInterval(this.intervalId);
        },
        resetTimer() {
            this.otpTimer = 60;
            this.startTimer();
        },
        mounted() {
            this.startTimer();
        },
        beforeUnmount() {
            this.stopTimer();
        },
        handleInput(index, event) {
            const value = event.target.value;

            // Only allow digits (0-9)
            if (!/^\d$/.test(value)) {
                this.otpdigits[index] = '';
                return;
            }

            this.otpdigits[index] = value;

            // Move focus to next input if not last
            if (index < this.otpdigits.length - 1) {
                const nextInput = this.$refs[`otpInput${index + 1}`];
                if (nextInput) {
                    nextInput.focus();
                }
            }
        },

        handleOtpInput(index) {
            const currentValue = this.otpdigits[index];

            // If one digit is typed, move to the next input
            if (currentValue.length === 1 && index < this.otpdigits.length - 1) {
                this.$refs[`otpInput${index + 1}`]?.focus();
            }
        },

        handleBackspace(index, event) {
            if (event.key === 'Backspace' && this.otpdigits[index] === '' && index > 0) {
                const prevInput = this.$refs[`otpInput${index - 1}`];
                if (prevInput) {
                    prevInput.focus();
                }
            }
        },

        getOtpCode() {
            return this.otpDigits.join('');
        },
        handlePaste(event) {
            const pasted = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            for (let i = 0; i < pasted.length; i++) {
                this.otpdigits[i] = pasted[i];
            }
            this.$nextTick(() => {
                const nextIndex = pasted.length >= 6 ? 5 : pasted.length;
                this.$refs[`otpInput${nextIndex}`]?.focus();
            });
        },

        async resendOtp() {

            try {
                this.LoaderSendingEmail = true;
                this.errors = {};
                this.errorMessage = '';
                this.otpTimer = 0;
                const requestData = {
                    email: this.email.trim(),
                }
                const response = await axios.post('/resendOtp', requestData);
                if (response.data.status === "success") {
                    setTimeout(() => {
                        this.LoaderSendingEmail = false;
                    }, 300);
                    this.toaster = true;
                    this.Message = response.data.message;
                    this.toastColor = "success";
                    setTimeout(() => {
                        this.toaster = false;
                    }, 5000);
                    this.startOtpTimer(response.data.timer);


                }

            }
            catch (error) {
                // Handle validation errors (status 422)
                if (error.response && error.response.status === 422) {
                    this.LoaderSendingEmail = false;

                    this.errors = error.response.data.errors || {};
                    this.errorMessage = 'Please correct the errors below.';
                }
                // Handle unexpected server errors (status 500)
                else if (error.response && error.response.status === 500) {
                    this.errorMessage = error.response.data.message || 'An unexpected error occurred. Please try again.';
                }
                // Handle network errors
                else {
                    this.errorMessage = 'A network error occurred. Please check your connection.';
                }
            } finally {
                // Re-enable the Resend OTP button
                // this.isResending = false;
            }
        },
        //filling data
        Emptyfill() {
            this.profilePic = "";
            this.previewPic = "/images/registertenant/Profile-PNG-Photo.png";
            this.firstname = "";
            this.lastname = "";
            this.email = "";
            this.password = "";
            this.password_confirmation = "";
            this.phonenumber = "";
            this.gender = "";
            this.govermentIdPicPreview = "";
            this.governmentIdFile = "";
            this.businessIdPicPreview = "";
            this.businessPermitFile = "";
            this.otpTimer = 0;
            this.otpdigits = Array(6).fill('');

        },
        showpassword() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            confirmPasswordField.type = type;
        }


    },
    computed: {
        formattedTime() {
            const minutes = Math.floor(this.otpTimer / 60);
            const seconds = this.otpTimer % 60;
            return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        },
    },
    mounted() {
        this.$nextTick(() => {
            if (this.currentStep === 3) {
                this.$refs.otpInput0?.focus();
            }
        });
    }



};
</script>
