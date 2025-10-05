<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />

    <div class="p-3">
        <!-- Profile Section -->
        <div class="text-center mb-5">
            <div class="position-relative d-inline-block">
                <img :src="tenant.previewPicUrl || (tenant.profilePicUrl ? '/storage/' + tenant.profilePicUrl : '/default-avatar.png')"
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
                <span v-if="tenant.role" class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-patch-check-fill me-1"></i> Tenant
                </span>
            </p>
        </div>

        <!-- Contact Form -->
        <div class="row g-4">
            <!-- First Name -->
            <div class="col-md-6">
                <label class="form-label fw-semibold required">First Name</label>
                <input type="text" class="form-control rounded-3 border-info shadow-sm" id="firstname" name="firstname"
                    placeholder="Enter your first name" v-model="tenant.firstname">
                <p class="text-danger small fst-italic mt-1" v-if="error.firstname">
                    <i class="bi bi-exclamation-circle"></i> {{ error.firstname[0] }}
                </p>


            </div>

            <!-- Last Name -->
            <div class="col-md-6">
                <label class="form-label fw-semibold required">Last Name</label>
                <input type="text" class="form-control rounded-3 border-info shadow-sm" id="lastname" name="lastname"
                    placeholder="Enter your last name" v-model="tenant.lastname">
                <p class="text-danger small fst-italic mt-1" v-if="error.lastname">
                    <i class="bi bi-exclamation-circle"></i> {{ error.lastname[0] }}
                </p>

            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label class="form-label fw-semibold required">Email</label>
                <input type="email" class="form-control rounded-3 border-info shadow-sm" id="email" name="email"
                    v-model="tenant.email" readonly placeholder="Enter your email">
                <p class="text-danger small fst-italic mt-1" v-if="error.email">
                    <i class="bi bi-exclamation-circle"></i> {{ error.email[0] }}
                </p>

            </div>

            <!-- Phone -->
            <div class="col-md-6">
                <label class="form-label fw-semibold required">Phone Number</label>
                <input type="tel" class="form-control rounded-3 border-info shadow-sm" id="phone" name="phonenumber"
                    v-model="tenant.phoneNumber" readonly placeholder="+63 912 345 6789" pattern="^\+?\d{7,15}$">
                <p class="text-danger small fst-italic mt-1" v-if="error.phoneNumber">
                    <i class="bi bi-exclamation-circle"></i> {{ error.phoneNumber[0] }}
                </p>

            </div>

            <!-- Gender -->
            <div class="col-md-6">
                <label for="gender" class="form-label fw-semibold required">Gender</label>
                <select class="form-select rounded-3 border-info shadow-sm" id="gender" name="gender"
                    v-model="tenant.gender"> <!-- ✅ Add this -->
                    <option disabled value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <p class="text-danger small fst-italic mt-1" v-if="error.gender">
                    <i class="bi bi-exclamation-circle"></i> {{ error.gender[0] }}
                </p>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold required">City</label>
                <input type="tel" class="form-control rounded-3 border-info shadow-sm" id="phone" name="phonenumber"
                    v-model="tenant.city" readonly placeholder="City">
                <p class="text-danger small fst-italic mt-1" v-if="error.city">
                    <i class="bi bi-exclamation-circle"></i> {{ error.city[0] }}
                </p>

            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold required">Province</label>
                <input type="tel" class="form-control rounded-3 border-info shadow-sm" id="phone" name="province"
                    v-model="tenant.province" readonly placeholder="province">
                <p class="text-danger small fst-italic mt-1" v-if="error.province">
                    <i class="bi bi-exclamation-circle"></i> {{ error.province[0] }}
                </p>

            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold required">Region</label>
                <input type="tel" class="form-control rounded-3 border-info shadow-sm" id="phone" name="region"
                    v-model="tenant.region" readonly placeholder="City">
                <p class="text-danger small fst-italic mt-1" v-if="error.region">
                    <i class="bi bi-exclamation-circle"></i> {{ error.region[0] }}
                </p>

            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold required">Current Address</label>
                <input type="tel" class="form-control rounded-3 border-info shadow-sm" id="phone" name="province"
                    v-model="tenant.currentAddress" readonly placeholder="currentAddress">
                <p class="text-danger small fst-italic mt-1" v-if="error.province">
                    <i class="bi bi-exclamation-circle"></i> {{ error.currentAddress[0] }}
                </p>

            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold required">Postal code</label>
                <input type="tel" class="form-control rounded-3 border-info shadow-sm" id="phone" name="region"
                    v-model="tenant.postalCode" readonly placeholder="City">
                <p class="text-danger small fst-italic mt-1" v-if="error.postalCode">
                    <i class="bi bi-exclamation-circle"></i> {{ error.postalCode[0] }}
                </p>

            </div>



        </div>
    </div>
    <div class="d-flex justify-content-center gap-3 mt-2 mb-4">
        <button type="submit" form="contactForm" class="btn btn-primary fw-semibold px-4 rounded-pill shadow-sm w-100"
            @click="updateTenantAccount">
            <i class="bi bi-check-circle me-1"></i> Update
        </button>

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
    name: 'TenantUpdate',
    data() {
        return {
            tenant: {
                tenantID: '',
                firstname: '',
                lastname: '',
                email: '',
                gender: '',
                city: '',
                province: '',
                region: '',
                currentAddress: '',
                postalCode: '',
                role: '',
                phoneNumber: '',
                profilePicUrl: '',
                previewPicUrl: '',
                profilePicFile: '',
            },
            error: {},
            tenant_id: '',
            clickUpdateDocument: false,
            notifications: [],
            receiverID: '',

        }
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.tenant_id;
            Echo.private(`notifications.${this.tenant_id}`)
                .subscribed(() => {
                    console.log('✔ Subscribed!');
                })
                .listen('.NewNotificationEvent', (e) => {
                    this.notifications.unshift(e); // save for list
                    this.$refs.toastRef.pushNotification({
                        title: e.title || 'New Notification',
                        message: e.message,
                        color: 'success',
                    });
                });
        },
        async fetchTenantData() {
            this.$refs.loader.loading = true;
            try {
                const response = await axios.get('/get/tenant/data/' + this.tenant_id);

                if (response.data.tenant) {
                    this.tenant = {
                        firstname: response.data.tenant.firstname ?? '',
                        lastname: response.data.tenant.lastname ?? '',
                        email: response.data.tenant.email ?? '',
                        gender: response.data.tenant.gender ?? '',
                        phoneNumber: response.data.tenant.phoneNumber ?? '',
                        tenantID: response.data.tenant.tenantID ?? '',
                        profilePicUrl: response.data.tenant.profilePicUrl ?? '',
                        isVerified: response.data.tenant.isVerified ?? '',
                        govermentID: response.data.tenant.govermentID ?? '',
                        businessPermit: response.data.tenant.businessPermit ?? '',
                        city: response.data.tenant.city ?? '',
                        province: response.data.tenant.province ?? '',
                        postalCode: response.data.tenant.postalCode ?? '',
                        currentAddress: response.data.tenant.currentAddress ?? '',
                        role: response.data.tenant.role ?? '',
                        region: response.data.tenant.region ?? '',

                        


                    };
                } else {
                    console.error('Tenant data not found');
                }
            }
            catch (error) {
                console.error('Error fetching tenant data:', error);
            }
            finally {
                this.$refs.loader.loading = false;
            }
        },

        btnclickUpdateDocument() {
            this.clickUpdateDocument = true;
        },
        async updateTenantAccount() {
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Update Account',
                    message: `Confirm update to this account information?`,
                    functionName: 'Update Account',
                });
                if (!confirmed) {
                    return false;
                }
                this.$refs.loader.loading = true;
               
                const formdata = new FormData();
                formdata.append('firstname', this.tenant.firstname);
                formdata.append('lastname', this.tenant.lastname);
                formdata.append('gender', this.tenant.gender);
                if (this.tenant.profilePicFile) {
                    formdata.append('profilePicUrl', this.tenant.profilePicFile); // only append file
                }
                const response = await axios.post(`/update/tenant/data/${this.tenant.tenantID}`, formdata, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                if (response.data.status === 'success') {
                    this.error = {};
                    this.tenant = response.data.tenant;
                    this.$refs.toast.showToast(response.data.message, 'success');
                    return true; 

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
       
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.tenant.previewPicUrl = URL.createObjectURL(file);
                this.tenant.profilePicFile = file; // ✅ diri siya gi-assign
            }
        },
      
    },
    mounted() {
        const element = document.getElementById('tenantupdateaccount');
        this.tenant_id = element.dataset.tenantId;
        this.fetchTenantData();
        this.subscribeToNotifications();
      


    }
}
</script>
