<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />

    <div class="container py-4">
        <!-- Card Container -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class=" text-black rounded-top-4">
                <h5 class="mb-0 ">🏠 Tenant Screening</h5>
            </div>
            <div class="card-body">

                <!-- Search & Filters -->
                <input type="text" v-model="searchQuery" placeholder="🔍 Search tenants..."
                    class="form-control shadow-sm mb-4" @keyup="filterTenants" />

                <div class="row mb-4">
                    <div class="col-md-6 mb-2">
                        <select id="roomSelect" class="form-select shadow-sm">
                            <option selected disabled>Select a room</option>
                            <option value="101">Room 101</option>
                            <option value="102">Room 102</option>
                            <option value="103">Room 103</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <select id="dormSelect" class="form-select shadow-sm">
                            <option selected disabled>Select a dormitory</option>
                            <option value="Dorm A">Dorm A</option>
                            <option value="Dorm B">Dorm B</option>
                            <option value="Dorm C">Dorm C</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered  align-middle mb-0 text-center">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Dorm</th>
                                <th>Room No</th>
                                <th>Payment Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="tenant in filteredTenants" :key="tenant.tenantId">
                                <td><strong>{{ tenant.tenantscreening_id }}</strong></td>
                                <td>{{ tenant.firstname }}</td>
                                <td>{{ tenant.lastname }}</td>
                                <td>{{ tenant.contact_email }}</td>
                                <td>{{ tenant.dorm?.dorm_name }}</td>
                                <td>{{ tenant.room?.room_number }}</td>
                                <td>{{ tenant.payment_type }}</td>
                                <td>
                                    <span class="badge" :class="{
                                        'bg-success': tenant.status === 'Approved',
                                        'bg-danger': tenant.status === 'Not Approved',
                                        'bg-warning text-dark': tenant.status !== 'Approved' && tenant.status !== 'Not Approved'
                                    }">{{ tenant.status }}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary"
                                            @click="openScreeningModal(tenant.tenantscreening_id)">Update</button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            @click="deleteScreening(tenant.tenantscreening_id)">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- Approval Modal -->
        <div v-if="VisibleModalApproval" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5);"
            tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow rounded-4">
                    <div class="modal-header bg-white border-0">
                        <h5 class="modal-title text-primary">🧾 Tenant Profile</h5>
                        <button type="button" class="btn-close" @click="VisibleModalApproval = false"></button>
                    </div>

                    <div class="modal-body px-4">
                        <div class="text-center mb-4">
                            <img :src="selectedtenant.studentpicture_id" class="rounded-circle border shadow-sm"
                                style="width: 120px; height: 120px; object-fit: cover;" />
                            <p class="mt-2">
                                <span class="badge rounded-pill px-3 py-2" :class="{
                                    'bg-success text-white': selectedtenant.status === 'Approved',
                                    'bg-danger text-white': selectedtenant.status === 'Not Approved',
                                    'bg-warning text-dark': selectedtenant.status !== 'Approved' && selectedtenant.status !== 'Not Approved'
                                }">{{ selectedtenant.status }}</span>
                            </p>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <p><strong>First Name:</strong> {{ selectedtenant.firstname }}</p>
                                <p><strong>Age:</strong> {{ selectedtenant.age }}</p>
                                <p><strong>Email:</strong> {{ selectedtenant.contact_email }}</p>
                                <p><strong>Address:</strong> {{ selectedtenant.address }}</p>
                                <p><strong>Room #:</strong> {{ selectedtenant.room_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Last Name:</strong> {{ selectedtenant.lastname }}</p>
                                <p><strong>Gender:</strong> {{ selectedtenant.gender }}</p>
                                <p><strong>Contact:</strong> {{ selectedtenant.contact_number }}</p>
                                <p><strong>Dorm:</strong> {{ selectedtenant.dorm_name }}</p>
                                <p><strong>Price:</strong> ₱{{ Number(selectedtenant.price).toLocaleString(undefined, {
                                    minimumFractionDigits: 2
                                }) }}</p>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <p><strong>Payment Type:</strong> {{ selectedtenant.payment_type }}</p>
                            <img :src="selectedtenant.payment_image" class="img-thumbnail rounded shadow-sm"
                                style="width: 200px; height: 200px; object-fit: cover;" />
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between bg-light border-top-0">
                        <button class="btn btn-outline-primary">Message Tenant</button>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success" @click="approveTenant"
                                :disabled="selectedtenant.status === 'Approved'">✅ Approve</button>
                            <button class="btn btn-outline-danger" @click="notApproveTenant"
                                :disabled="selectedtenant.status === 'Not Approved'">❌ Not Approve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="VisibleDeleteModal" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5);"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow rounded-3">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white"
                            @click="VisibleDeleteModal = false"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="bi bi-exclamation-triangle-fill text-warning display-4 mb-3"></i>
                        <p class="fs-5">Are you sure you want to delete this tenant? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary px-4" @click="VisibleDeleteModal = false">Cancel</button>
                        <button class="btn btn-danger px-4" @click="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <Modalconfirmation ref="modal" />
</template>
<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation


    },
    data() {
        return {
            VisibleModalApproval: false,
            VisibleDeleteModal: false,
            tenants: [],
            selectedtenant: [],
            selecttenant_id: '',
            tenant: {},
            searchQuery: "",
            filteredTenants: [],
            landlord_id: '',
        };
    },

    methods: {
        filterTenants() {
            // Filter tenants based on the search query
            this.filteredTenants = this.tenants.filter((tenant) => {
                return (
                    tenant.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    tenant.email.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            });
        },
        async fetchtenantScreening() {
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get('/tenant-screening-list', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                this.$refs.loader.loading = false;

                this.filteredTenants = response.data.screening || [];
                console.log(response.data.screening);


            } catch (error) {
                this.$refs.loader.loading = false;

                console.error('Error fetching rooms:', error);
            }
        },
        async openScreeningModal(tenant_id) {
            this.$refs.loader.loading = true;

            this.selecttenant_id = tenant_id;
            try {
                const response = await axios.get(`/view-tenant/${this.selecttenant_id}`);
                this.selectedtenant = response.data.tenant;
                this.VisibleModalApproval = true;
                this.$refs.loader.loading = false;


            }
            catch (error) {
                this.$refs.loader.loading = false;

                console.error('Error fetching tenant data:', error);

            }
        },
        async approveTenant() {
            const confirmed = await this.$refs.modal.show({
                title: 'Confirm Approval Tenant',
                message: 'Approve this tenant’s application? This will reserve a room for them.',
                functionName: 'Confirm'
            });

            if (!confirmed) {
                this.$refs.loader.loading = false;
                return;
            }

            this.$refs.loader.loading = true;

            try {
                const formData = new FormData();
                formData.append('tenant_screening_id', this.selecttenant_id);

                const response = await axios.post('/approve-tenant', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });

                if (response.data.status === "success") {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.VisibleModalApproval = false;
                    this.fetchtenantScreening();
                } else if (response.data.status === "error") {
                    this.$refs.toast.showToast(response.data.message || 'Approval failed.', 'error');
                }
            } catch (error) {
                this.$refs.loader.loading = false;
                this.VisibleModalApproval = false;
                const errorMsg = error.response.data?.message || 'Failed to approve tenant.';
                this.$refs.toast.showToast(errorMsg, 'danger');
            } finally {
                this.$refs.loader.loading = false;
            }
        },


        async notApproveTenant() {
            const confirmed = await this.$refs.modal.show({
                title: 'Confirm Tenant Approval',
                message: 'Are you sure you want to decline this tenant’s application?',
                functionName: 'Confirm'
            });

            if (!confirmed) {
                this.$refs.loader.loading = false;
                return;
            }
            try {
                const formData = new FormData();
                formData.append('tenant_screening_id', this.selecttenant_id);

                const response = await axios.post('/not-approve-tenant', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                });
                if (response.data.status === "success") {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.$refs.loader.loading = false;
                    this.VisibleModalApproval = false;
                    this.fetchtenantScreening();

                }


                // Optional: trigger a success alert or reload data
            } catch (error) {
                console.error('Error approving tenant:', error);
                // Optional: show a user-friendly error message
            }
        },
        async deleteScreening(tenant_id) {
            this.selecttenant_id = tenant_id;

            const confirmed = await this.$refs.modal.show({
                title: 'Delete Room?',
                message: 'This will permanently remove the room. Proceed?',
                functionName: 'Delete Room'
            });

            if (!confirmed) {
                this.$refs.loader.loading = false;
                return;
            }
            this.$refs.loader.loading = true; // Show loading indicator
            try {
                const response = await axios.delete(`/delete-screening/${this.selecttenant_id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.fetchtenantScreening();

                    this.$refs.loader.loading = false;

                    this.$refs.toast.showToast(response.data.message, 'success');

                }
                else {
                    this.$refs.toast.showToast(response.data.message, 'error');
                }
            }
            catch (error) {
                console.error('Error deleting room:', error);
                this.$refs.toast.showToast('Failed to delete room.', 'error');
            } finally {
                this.$refs.loader.loading = false; // Hide loading indicator

            }
        },
    },
    mounted() {
        this.fetchtenantScreening();
    }
};
</script>
<style scoped>
/* Optional custom styles */
</style>
