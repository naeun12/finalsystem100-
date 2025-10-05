<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />
    <div class="p-3">
        <!-- Profile Section -->
        <div class="text-center mb-5">
            <div class="position-relative d-inline-block">
                <img :src="landlord.previewPicUrl || (landlord.profilePicUrl ? '/' + landlord.profilePicUrl : '/default-avatar.png')"
                    alt="Profile Preview" class="rounded-circle shadow-sm border border-3 border-light"
                    style="width: 140px; height: 140px; object-fit: cover;">


                <!-- Upload Button -->
                <label for="profilePic"
                    class="btn btn-sm btn-light border position-absolute bottom-0 end-0 rounded-circle shadow-sm"
                    style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <i class="bi bi-camera-fill text-primary"></i>
                </label>
            </div>
            <input type="file" id="profilePic" name="profilePicUrl" accept="image/*" class="d-none"
                @change="previewImage">
            <p class="small mt-3 mb-0">
                <span v-if="landlord.isVerified" class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-patch-check-fill me-1"></i> Verified
                </span>

                <span v-else class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-x-circle-fill me-1"></i> Not Verified
                </span>
            </p>
        </div>

        <!-- Contact Form -->
        <div class="row g-4">
            <!-- First Name -->
            <div class="col-md-6">
                <label class="form-label fw-semibold required">First Name</label>
                <input type="text" class="form-control rounded-3 border-info shadow-sm" id="firstname" name="firstname"
                    placeholder="Enter your first name" v-model="landlord.firstname">
                <p class="text-danger small fst-italic mt-1" v-if="error.firstname">
                    <i class="bi bi-exclamation-circle"></i> {{ error.firstname[0] }}
                </p>


            </div>

            <!-- Last Name -->
            <div class="col-md-6">
                <label class="form-label fw-semibold required">Last Name</label>
                <input type="text" class="form-control rounded-3 border-info shadow-sm" id="lastname" name="lastname"
                    placeholder="Enter your last name" v-model="landlord.lastname">
                <p class="text-danger small fst-italic mt-1" v-if="error.lastname">
                    <i class="bi bi-exclamation-circle"></i> {{ error.lastname[0] }}
                </p>

            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label class="form-label fw-semibold required">Email</label>
                <input type="email" class="form-control rounded-3 border-info shadow-sm" id="email" name="email"
                    v-model="landlord.email" readonly placeholder="Enter your email">
                <p class="text-danger small fst-italic mt-1" v-if="error.email">
                    <i class="bi bi-exclamation-circle"></i> {{ error.email[0] }}
                </p>

            </div>

            <!-- Phone -->
            <div class="col-md-6">
                <label class="form-label fw-semibold required">Phone Number</label>
                <input type="tel" class="form-control rounded-3 border-info shadow-sm" id="phone" name="phonenumber"
                    v-model="landlord.phoneNumber" readonly placeholder="+63 912 345 6789" pattern="^\+?\d{7,15}$">
                <p class="text-danger small fst-italic mt-1" v-if="error.phoneNumber">
                    <i class="bi bi-exclamation-circle"></i> {{ error.phoneNumber[0] }}
                </p>

            </div>

            <!-- Gender -->
            <div class="col-md-6">
                <label for="gender" class="form-label fw-semibold required">Gender</label>
                <select class="form-select rounded-3 border-info shadow-sm" id="gender" name="gender"
                    v-model="landlord.gender"> <!-- ✅ Add this -->
                    <option disabled value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <p class="text-danger small fst-italic mt-1" v-if="error.gender">
                    <i class="bi bi-exclamation-circle"></i> {{ error.gender[0] }}
                </p>
            </div>

            <div class="col-md-6 d-flex align-items-end">
                <button @click="btnclickUpdateDocument()" class="btn btn-primary w-100">
                    Update Documents
                </button>
            </div>

        </div>
        <div v-if="clickUpdateDocument" class="modal fade show d-block" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 border-0">

                    <!-- Header -->
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-folder-check me-2"></i> Update Documents
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            @click="clickUpdateDocument = false"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <div class="row g-4 text-center">

                            <!-- Business Permit -->
                            <div class="col-md-6">
                                <div class="card-body text-center">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="bi bi-file-earmark-text me-2 text-primary"></i> Business Permit
                                    </h6>

                                    <!-- Preview Box -->
                                    <div class="position-relative mb-3">
                                        <img :src="landlord.businessPermitPreview || (landlord.businessPermit ? '/' + landlord.businessPermit : '/images/no-file.png')"
                                            alt="Business Permit"
                                            class="img-fluid rounded shadow-sm border border-2 border-light"
                                            style="max-height: 200px; width: 100%; object-fit: cover;">

                                        <!-- Overlay Upload Icon -->
                                        <label for="businessPermit"
                                            class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow p-2 border border-primary"
                                            style="cursor: pointer;" title="Upload Business Permit">
                                            <i class="bi bi-upload text-primary fs-5"></i>
                                        </label>
                                    </div>

                                    <!-- Hidden Input -->
                                    <input type="file" id="businessPermit" class="d-none" accept="image/*"
                                        @change="previewBusinessPermit">

                                    <!-- Helper Text -->
                                    <p class="text-muted small mb-0">
                                        Allowed formats: <span class="fw-semibold">JPG, PNG</span> | Max size: <span
                                            class="fw-semibold">2MB</span>
                                    </p>
                                </div>

                            </div>

                            <!-- Government ID -->
                            <div class="col-md-6">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-body text-center">
                                        <h6 class="fw-semibold mb-3">
                                            <i class="bi bi-person-vcard me-2 text-success"></i> Government ID
                                        </h6>

                                        <!-- Preview Box -->
                                        <div class="position-relative mb-3">
                                            <img :src="landlord.governmentIDPreview || (landlord.govermentID ? '/' + landlord.govermentID : '/images/no-file.png')"
                                                alt="Government ID"
                                                class="img-fluid rounded shadow-sm border border-2 border-light"
                                                style="max-height: 200px; width: 100%; object-fit: cover;">

                                            <!-- Overlay Upload Icon -->
                                            <label for="governmentID"
                                                class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow p-2 border border-success"
                                                style="cursor: pointer;" title="Upload Government ID">
                                                <i class="bi bi-upload text-success fs-5"></i>
                                            </label>
                                        </div>

                                        <!-- Hidden Input -->
                                        <input type="file" id="governmentID" class="d-none" accept="image/*"
                                            @change="previewGovernmentID">

                                        <!-- Helper Text -->
                                        <p class="text-muted small mb-0">
                                            Allowed formats: <span class="fw-semibold">JPG, PNG</span> | Max size: <span
                                                class="fw-semibold">2MB</span>
                                        </p>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer ">

                        <button class="btn btn-primary" @click="updateDocuments">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backdrop -->
        <div v-if="clickUpdateDocument" class="modal-backdrop fade show"></div>



        <!-- Buttons -->
        <div class="d-flex justify-content-center gap-3 mt-5">
            <button type="submit" form="contactForm"
                class="btn btn-primary fw-semibold px-4 rounded-pill shadow-sm w-100" @click="updateLandlordAccount">
                <i class="bi bi-check-circle me-1"></i> Update
            </button>

        </div>
    </div>
    <Modalconfirmation ref="modal" />
    <Toastcomponents ref="toast" />
</template>
<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import { debounce } from 'lodash';
import NotificationList from '@/components/notifications.vue';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList,
    },
    name: 'LandlordUpdate',
    data() { 
        return {
            landlord: {
                landlordID: '',
                firstname: '',
                lastname: '',
                email: '',
                gender: '',
                phoneNumber: '',
                profilePicUrl: '',
                previewPicUrl: '',
                profilePicFile: '',  
                isVerified: '',
                businessPermit: '',
                businessPermitPreview: '',
                businessPermitFile: '',
                govermentID: '',
                governmentIDPreview: '',
                governmentIDFile : '',
            },
            error: {},
            landlord_id: '',
            clickUpdateDocument: false,

        }
    },
    methods: {
        fetchLandlordData() {
            this.$refs.loader.loading = true;

            fetch('/get/landlord/data/' + this.landlord_id)
                .then(response => response.json())
                .then(data => {
                    if (data.landlord) {
                            this.landlord = {
                                firstname: data.landlord.firstname ?? '',
                                lastname: data.landlord.lastname ?? '',
                                email: data.landlord.email ?? '',
                                gender: data.landlord.gender ?? '',
                                phoneNumber: data.landlord.phoneNumber ?? '',
                                landlordID: data.landlord.landlordID ?? '',
                                profilePicUrl: data.landlord.profilePicUrl ?? '',
                                isVerified: data.landlord.isVerified ?? '',
                                govermentID: data.landlord.govermentID ?? '',
                                businessPermit: data.landlord.businessPermit ?? '',

                            };
                         this.$refs.loader.loading = false;

                    } else {
                        console.error('Landlord data not found');
                        this.$refs.loader.loading = false;

                    }
                })
                .catch(error => console.error('Error fetching landlord data:', error));
        },
         btnclickUpdateDocument() { 
            this.clickUpdateDocument = true;
        },
        async updateLandlordAccount() { 
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Update Account',
                    message: `Confirm update to this account information?`,
                    functionName: 'Update Account',
                });
                if (!confirmed) {
                    return;
                }
                this.$refs.loader.loading = true;
                const formdata = new FormData();
                formdata.append('firstname', this.landlord.firstname);
                formdata.append('lastname', this.landlord.lastname);
                formdata.append('gender', this.landlord.gender);
                if (this.landlord.profilePicFile) {
                    formdata.append('profilePicUrl', this.landlord.profilePicFile);
                }
                const response = await axios.post(`/update/landlord/data/${this.landlord.landlordID}`, formdata, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });        
                if (response.data.status === 'success') { 
                    this.error = {};
                    this.landlord = response.data.landlord;
                    this.$refs.toast.showToast(response.data.message, 'success');
                }
                    
            }
            catch (error) {
                    if (error.response && error.response.status === 422) {
                        // Laravel validation errors
                        this.error = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                }

            
            finally { 
                this.$refs.loader.loading = false;

            }
        },
        async updateDocuments() {
            try {
                this.$refs.loader.loading = true;

                const formdata = new FormData();

                // Business Permit
                if (this.landlord.businessPermitFile) {
                    formdata.append('businessPermit', this.landlord.businessPermitFile);
                }

                // Government ID
                if (this.landlord.governmentIDFile) {
                    formdata.append('governmentID', this.landlord.governmentIDFile);
                }

                // Send to backend
                const response = await axios.post(
                    `/update/landlord/documents/${this.landlord.landlordID}`,
                    formdata,
                    { headers: { "Content-Type": "multipart/form-data" } }
                );

                if (response.data.status === "success") {
                    this.landlord = response.data.landlord; 
                    this.clickUpdateDocument = false;
                    this.$refs.toast.showToast(response.data.message, "success");
                }
            }
            catch (error) {
                if (error.response && error.response.status === 422) {
                    this.error = error.response.data.errors; // validation errors
                } else {
                    console.error(error);
                    this.$refs.toast.showToast("Something went wrong. Try again.", "error");
                }
            }
            finally {
                console.log("Update documents request finished.");
                this.$refs.loader.loading = false;

            }
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.landlord.previewPicUrl = URL.createObjectURL(file);
                this.landlord.profilePicFile = file; // ✅ diri siya gi-assign
            }
        },
        previewBusinessPermit(event) {
            const file = event.target.files[0];
            if (file) {
                this.landlord.businessPermitPreview = URL.createObjectURL(file);
                this.landlord.businessPermitFile = file; // keep file for upload
            }
        },
        previewGovernmentID(event) {
            const file = event.target.files[0];
            if (file) {
                this.landlord.governmentIDPreview = URL.createObjectURL(file);
                this.landlord.governmentIDFile = file; // keep file for upload
            }
        },
    },
    mounted() {
        const element = document.getElementById('landlordaccountUpdated');
        this.landlord_id = element.dataset.landlordId;
        this.fetchLandlordData();
        // window.vueInstance = this;


        }
}
</script>
