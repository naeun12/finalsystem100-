<template>
    <Loader ref="loader" />

    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light shadow-sm rounded">
        <h1 class="fw-bold text-dark mb-0" style="font-size: 1.8rem;">
            Landlord Management Dashboard
        </h1>
        <button class="btn btn-primary d-inline-flex align-items-center px-4 py-2" @click="downloadReport"
            style="transition: transform 0.2s, box-shadow 0.2s;"
            onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';"
            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
            <i class="bi bi-file-earmark-arrow-down me-2"></i>
            Download Landlord Report
        </button>
    </div>
    <div class="input-group  w-100 shadow-sm rounded-pill overflow-hidden mb-3" style="border:2px solid #4edce2;">
        <span class="input-group-text bg-white border-0">
            <i class="bi bi-search text-primary"></i>
        </span>
        <input type="text" class="form-control border-0 shadow-none" placeholder="Search Landlord name"
            aria-label="Search Tenant " v-model="searchTerm" style="border:2px solid #4edce2;" />
    </div>


    <div class="table-responsive shadow-sm rounded border border-1" style="overflow: hidden;"
        v-if="landlords && landlords.length">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-info">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(landlord, index) in landlords" :key="landlord.landlordID">
                    <th scope="row">{{ landlord.landlordID }}</th>
                    <td>{{ landlord.firstname }}</td>
                    <td>{{ landlord.lastname }}</td>
                    <td>{{ landlord.email }}</td>
                    <td>
                        <span :class="landlord.is_deactivated ? 'badge bg-danger' : 'badge bg-success'">
                            {{ landlord.is_deactivated ? 'Deactivated' : 'Active' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-success btn-sm d-inline-flex align-items-center me-2"
                            @click="viewTenantProfile(landlord.landlordID)" style="transition: transform 0.2s;"
                            onmouseover="this.style.transform='scale(1.1)';"
                            onmouseout="this.style.transform='scale(1)';">
                            <i class="bi bi-eye me-1"></i> <!-- Eye icon for view -->
                            View
                        </button>

                        <button class="btn btn-warning btn-sm d-inline-flex align-items-center"
                            :disabled="landlord.is_deactivated" @click="deactivatedLandlord(landlord.landlordID)"
                            style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)';"
                            onmouseout="this.style.transform='scale(1)';">
                            <i class="bi bi-slash-circle me-1"></i> <!-- Bootstrap icon for deactivate -->
                            Deactivate
                        </button>

                    </td>
                </tr>
            </tbody>
        </table>
        <nav aria-label="Page navigation" class="mt-3" v-if="landlords && landlords.length">
            <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button class="page-link" @click="fetchTenantsList(currentPage - 1)">Previous</button>
                </li>

                <!-- Numbered pages -->
                <li class="page-item" v-for="page in lastPage" :key="page" :class="{ active: currentPage === page }">
                    <button class="page-link" @click="fetchTenantsList(page)">{{ page }}</button>
                </li>

                <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                    <button class="page-link" @click="fetchTenantsList(currentPage + 1)">Next</button>
                </li>
            </ul>
        </nav>

    </div>


    <div v-else class="alert alert-info text-center shadow-sm rounded d-flex align-items-center justify-content-center">
        <i class="bi bi-exclamation-circle me-2"></i>
        No landlords found.
    </div>

    <div v-if="landlordviewModal" class="modal d-block fade show" tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- Modal Header with gradient -->
                <div class="modal-header p-3"
                    style="background: linear-gradient(90deg, #4e73df, #224abe); color: #fff;">
                    <h5 class="modal-title">Landlord Profile</h5>
                    <button type="button" class="btn-close btn-close-white" @click="landlordviewModal = false"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light">
                    <div class="mb-3  text-center">
                        <img :src="selectedLandlord.previewPicUrl || (selectedLandlord.profilePicUrl ? '/' + selectedLandlord.profilePicUrl : '/default-avatar.png')"
                            class="rounded-circle border border-3 border-white shadow-sm"
                            style="width: 150px; height: 150px; object-fit: cover;">

                        <!-- Account Status Badge -->

                    </div>
                    <div class="mb-3  text-center">
                        <span class="badge mt-2" :class="selectedLandlord.is_deactivated ? 'bg-danger' : 'bg-success'">
                            {{ selectedLandlord.is_deactivated ? 'Deactivated' : 'Active' }}
                        </span>
                        <!-- Account Status Badge -->

                    </div>

                    <div v-if="selectedLandlord" class="d-flex  flex-md-row align-items-center ">

                        <!-- Profile Image -->
                        <!-- Tenant Info -->
                        <div class="flex-fill">
                            <div class="row mb-2">
                                <div class="col">
                                    <p class="mb-1"><strong class="text-primary">First Name:</strong> {{
                                        selectedLandlord.firstname }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-primary">Last Name:</strong> {{
                                        selectedLandlord.lastname }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-primary">Gender:</strong> {{
                                        selectedLandlord.gender }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong class="text-primary">Account Verified:</strong>
                                        <span class="badge"
                                            :class="selectedLandlord.isVerified ? 'bg-success' : 'bg-danger'">
                                            {{ selectedLandlord.isVerified ? 'Verified' : 'Not Verified' }}
                                        </span>
                                    </p>
                                </div>

                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-primary">Email:</strong> {{
                                        selectedLandlord.email
                                        }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-primary">Phone:</strong> {{
                                        selectedLandlord.phoneNumber }}</p>
                                </div>
                            </div>


                        </div>

                    </div>



                </div>

                <div class="modal-footer bg-light">
                    <button class="btn btn-success d-inline-flex align-items-center"
                        @click="reActivateLandlord(selectedLandlord.landlordID)"
                        :disabled="!selectedLandlord.is_deactivated" style="transition: transform 0.2s;"
                        onmouseover="this.style.transform='scale(1.1)';" onmouseout="this.style.transform='scale(1)';">
                        <i class="bi bi-arrow-repeat me-1"></i> <!-- Reactivate icon -->
                        Re Active
                    </button>

                    <button type="button" class="btn btn-primary" @click="OpensendEmailModal(selectedLandlord)">
                        <i class="bi bi-envelope-fill me-1"></i> Send Email</button>
                </div>

            </div>
        </div>

    </div>
    <div v-if="emailModal" class="modal d-block fade show" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- Modal Header with gradient -->
                <div class="modal-header p-3"
                    style="background: linear-gradient(90deg, #4e73df, #224abe); color: #fff;">
                    <h5 class="modal-title">Send Email</h5>
                    <button type="button" class="btn-close btn-close-white" @click="ClosesendEmailModal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4 bg-light">
                    <form @submit.prevent="sendEmail">
                        <div class="mb-3">
                            <label for="emailTo" class="form-label">To</label>
                            <input type="email" class="form-control" id="emailTo" v-model="emailForm.to"
                                placeholder="Recipient email" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="emailSubject" v-model="emailForm.subject"
                                placeholder="Email subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="emailMessage" rows="5" v-model="emailForm.message"
                                placeholder="Type your message..." required></textarea>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-primary" @click="sendEmail">
                        <i class="bi bi-envelope-fill me-1"></i> Send Email
                    </button>
                </div>

            </div>
        </div>
    </div>

    <Modalconfirmation ref="modal" />
    <Toastcomponents ref="toast" />


</template>
<script>
import axios from 'axios';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import Toastcomponents from '@/components/Toastcomponents.vue';


export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
    },
    data() {
        return {
            landlords: [],
            currentPage: 1,
            lastpage: 1,
            landlordviewModal: false,
            selectedLandlord: [],
            emailModal: false,
            emailForm: {
                to: '',
                subject: '',
                message: ''
            },
            searchTerm: '',
        }
    },
    methods:
    {
        async fetchTenantsList(page = 1) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get('/admin/get-landlord', {
                    params: { q: this.searchTerm, page }
                });
                this.landlords = response.data.data;
                this.currentPage = response.data.current_page;
                this.lastPage = response.data.last_page;
            }
            catch (error) {
                console.log(error);
            }
            finally {
                this.$refs.loader.loading = false;

            }

        },
        async viewTenantProfile(id) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/admin/view-landlord/${id}`);
                const landlord = response.data;

                this.selectedLandlord = landlord;
                this.landlordviewModal = true;

                // You can now open a modal or display landlord info
            } catch (error) {
                console.error('Error fetching landlord profile:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async deactivatedLandlord(id) {

            const confirmed = await this.$refs.modal.show({
                title: 'Deactivate Landlord',
                message: `Are you sure you want to deactivate this landlord account?`,
                functionName: 'Deactivate', // this can be used for the button text inside the modal
            });


            if (!confirmed) {
                return;
            }
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get(`/admin/deact-landlord/${id}`);
                this.fetchTenantsList();
                this.$refs.toast.showToast(response.data.message, 'success');
            }
            catch (error) {

            }
            finally {
                this.$refs.loader.loading = false;

            }


        },
        async reActivateLandlord(id) {

            const confirmed = await this.$refs.modal.show({
                title: 'Reactivate Landlord',
                message: `Do you want to reactivate this landlord's account? They will regain full access once reactivated.`,
                functionName: 'Reactivate', // text on the confirm button
            });


            if (!confirmed) {
                return;
            }
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get(`/admin/active-landlord/${id}`);
                this.fetchTenantsList();
                this.landlordviewModal = false;
                this.$refs.toast.showToast(response.data.message, 'success');
            }
            catch (error) {

            }
            finally {
                this.$refs.loader.loading = false;

            }


        },
        OpensendEmailModal(landlord) {
            this.emailForm.to = landlord.email;      // pre-fill recipient
            this.emailForm.subject = landlord.firstname;           // optional: reset subject
            this.emailForm.message = '';           // optional: reset message
            this.emailModal = true;
            this.landlordviewModal = false;
        },
        ClosesendEmailModal() {
            this.emailModal = false;
            this.landlordviewModal = true;
        },
        async sendEmail() {
            try {
                this.$refs.loader.loading = true;

                const formdata = new FormData();
                formdata.append('to', this.emailForm.to);
                formdata.append('subject', this.emailForm.subject);
                formdata.append('message', this.emailForm.message);
                const response = await axios.post('/admin/landlord/send/email', formdata);
                this.emailModal = false;

                this.$refs.toast.showToast(response.data.message, 'success');
            }
            catch (error) {
                console.log(error);
            }
            finally { 
                this.$refs.loader.loading = false;

            }
            

        },
        downloadReport() {
            try {
                this.$refs.loader.loading = true;

                window.open("/generate-landlord-report", "_blank");

            } catch {

            }
            finally {
                this.$refs.loader.loading = false;

            }
        },



    },
    mounted() {
        this.fetchTenantsList();
    },
    watch: {
        searchTerm(newVal) {
            axios.get('/admin/search-landlords', { params: { q: newVal } })
                .then(res => {
                    this.landlords = res.data.data.data; // for paginated results, data is nested
                })
                .catch(err => console.error(err));
        }
    }



}
</script>
