<template>
    <NotificationList ref="toastRef" />
    <Loader ref="loader" />

    <div class="p-4 mt-4">
        <div class="input-group mb-2 w-100 shadow-sm rounded-pill overflow-hidden" style="border: 1px solid #4edce2;">
            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none" placeholder="Search Tenants name"
                aria-label="Search Tenant " v-model="searchTerm" />
        </div>
        <div class="py-3 d-flex gap-3 align-items-center">
            <!-- Dorm No Dropdown -->
            <div class="mb-2 d-flex align-items-center gap-2"
                style="border:1px solid #4edce2; border-radius: 0.375rem;">
                <div class="w-100">

                    <select id="dormSelect" class="form-select form-select-lg shadow-sm" @change="filterDorms"
                        v-model="selectedDormId">
                        <option value="" disabled> Select Dorm</option>
                        <option value="all"> All dorms</option>
                        <option v-for="dorm in dorms" :key="dorm.dormID" :value="dorm.dormID">
                            {{ dorm.dormName }} (ID: {{ dorm.dormID }})
                        </option>
                    </select>
                </div>
            </div>
            <!-- Room No Dropdown -->
            <div class="mb-2 d-flex align-items-center gap-2"
                style="border:1px solid #4edce2; border-radius: 0.375rem;">
                <div class="w-100">
                    <select id="dormSelect" class="form-select form-select-lg shadow-sm" v-model="selectedroomNumber"
                        @change="filterroomNumber">
                        <option value="" disabled> Select Room Number</option>
                        <option value="all">All rooms</option>
                        <option v-for="room in uniqueRooms" :key="room.fkroomID" :value="room.room?.roomNumber">
                            Room {{ room.room?.roomNumber }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="container bg-white rounded shadow-sm " style="border:1px solid #4edce2; border-radius: 0.375rem;">
            <div class="row fw-bold bg-primary text-white text-center py-3 rounded">
                <div class="col">#</div>
                <div class="col">Tenant Name</div>
                <div class="col">Email</div>
                <div class="col">Dorm Name</div>
                <div class="col">Room No</div>
                <div class="col">Status</div>
                <div class="col">Actions</div>
            </div>

            <!-- Tenant Rows -->
            <div v-for="tenant in tenants" :key="tenant.approvedID"
                class="row text-center align-items-center border-bottom py-2">
                <div class="col">{{ tenant.approvedID }}</div>
                <div class="col">{{ tenant.firstname }} {{ tenant.lastname }}</div>
                <div class="col">{{ tenant.contactEmail }}</div>
                <div class="col">{{ tenant.room?.dorm?.dormName ?? 'N/A' }}</div>
                <div class="col">{{ tenant.room?.roomNumber ?? 'N/A' }}</div>
                <div class="col">
                    <span class="badge rounded-pill px-3 py-2" :class="{
                        'bg-success text-white': tenant.status === 'active',
                        'bg-secondary text-white': tenant.status === 'moved_out',
                        'bg-danger text-white': tenant.status === 'terminated',
                        'bg-warning text-dark': tenant.status === 'pending_moveout',
                        'bg-info text-white': tenant.status === 'transferring',
                        'bg-dark text-white': tenant.status === 'suspended'
                    }">
                        {{ tenant.status?.replace('_', ' ').toUpperCase() }}
                    </span>
                </div>
                <div class="col">
                    <button class="btn btn-sm btn-outline-primary"
                        @click="displaytenantInformation(tenant.approvedID)">View</button>
                    <button class="btn btn-sm btn-outline-danger ms-2"
                        @click="deleteReservation(tenant.approvedID)">Delete</button>
                </div>
            </div>
        </div>


        <div v-if="VisibleTenantModal" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5);"
            tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 border-0">
                    <!-- Header -->
                    <div class="modal-header bg-white border-bottom-0">
                        <h5 class="modal-title text-primary fw-bold">
                            🧾 Tenants Information
                        </h5>
                        <button type="button" class="btn-close" @click="VisibleTenantModal = false"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body px-5">
                        <!-- Profile Picture and Status -->
                        <div class="text-center mb-4">
                            <img :src="selectedtenant.studentpictureId"
                                class="rounded-circle border border-3 border-light shadow-sm"
                                style="width: 130px; height: 130px; object-fit: cover;" />
                            <p class="mt-3">
                                <span class="badge rounded-pill px-3 py-2 fs-6" :class="{
                                    'bg-success text-white': selectedtenant.status === 'active',
                                    'bg-secondary text-white': selectedtenant.status === 'moved_out',
                                    'bg-danger text-white': selectedtenant.status === 'terminated',
                                    'bg-warning text-dark': selectedtenant.status === 'pending_moveout',
                                    'bg-info text-white': selectedtenant.status === 'transferring',
                                    'bg-dark text-white': selectedtenant.status === 'suspended'
                                }">
                                    {{ selectedtenant.status?.replace('_', ' ').toUpperCase() }}
                                </span>

                            </p>
                        </div>

                        <!-- Information Grid -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-person-fill me-2"></i>First Name</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.firstname">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-123 me-2"></i>Age</label>
                                    <input type="number" class="form-control" v-model="selectedtenant.age">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-envelope-fill me-2"></i>Email</label>
                                    <input type="email" class="form-control" v-model="selectedtenant.contactEmail">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-building me-2"></i> Dorm Name
                                    </label>

                                    <select v-model="selectedDormId" class="form-select" @change="fetchRooms">
                                        <option disabled value="">Select Dorm</option>
                                        <option v-for="dorm in dorms" :key="dorm.dormID" :value="dorm.dormID">
                                            {{ dorm.dormName }}
                                        </option>
                                    </select>

                                </div>


                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-geo-alt-fill me-2"></i>Dorm Address
                                    </label>
                                    <input type="text" class="form-control" :value="selectedDorm.address" readonly>
                                </div>




                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-calendar-check-fill me-2"></i>Move-In
                                        Date</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.moveInDate"
                                        readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-door-closed-fill me-2"></i>Room
                                        Number</label>
                                    <select class="form-select" v-model="selectedRoomNumber">
                                        <option disabled value="">Select Room</option>
                                        <option v-for="room in filteredRooms" :key="room.roomID"
                                            :value="room.roomNumber">
                                            {{ room.roomNumber }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-person-fill me-2"></i>Last Name</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.lastname">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-gender-ambiguous me-2"></i>Gender</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.gender">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-telephone-fill me-2"></i>Contact
                                        Number</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.contactNumber">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-house-door-fill me-2"></i>Room
                                        Type</label>
                                    <input type="text" class="form-control" :value="selectedtenant.room?.roomType"
                                        readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-currency-dollar me-2"></i>Monthly
                                        Rent</label>
                                    <input type="text" class="form-control" :value="'₱ ' + selectedtenant.room?.price"
                                        readonly>

                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="bi bi-calendar-x me-2"></i>Move-Out Date</label>
                                    <input type="text" class="form-control" v-model="selectedtenant.moveOutDate"
                                        readonly>
                                </div>
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">
                                            <i class="bi bi-person-check-fill me-2"></i>Status
                                        </label>
                                        <select v-model="selectedtenant.status" id="status" class="form-select" :class="{
                                            'text-success': selectedtenant.status === 'active',
                                            'text-secondary': selectedtenant.status === 'moved_out',
                                            'text-danger': selectedtenant.status === 'terminated',
                                            'text-warning': selectedtenant.status === 'pending_moveout',
                                            'text-info': selectedtenant.status === 'transferring',
                                            'text-dark': selectedtenant.status === 'suspended'
                                        }">
                                            <option value="active">Active</option>
                                            <option value="moved_out">Moved Out</option>
                                            <option value="terminated">Terminated</option>
                                            <option value="pending_moveout">Pending Move Out</option>
                                            <option value="transferring">Transferring</option>
                                            <option value="suspended">Suspended</option>
                                        </select>
                                    </div>

                                </div>

                            </div>
                            <StatusAlert :status="selectedtenant.status" role="Landlord" />


                        </div>
                        <!-- Current Occupant Information -->
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light border-top-0 px-4 py-3">
                        <button class="btn btn-outline-primary px-4">
                            💬 Message Tenant
                        </button>
                        <button class="btn btn-outline-success px-4">
                            📩 Notify Tenant about Rent Extension
                        </button>
                        <button class="btn btn-outline-secondary px-4" @click="updateTenantInformation">
                            Update Tenant Information
                        </button>
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
import { debounce } from 'lodash';
import NotificationList from '@/components/notifications.vue';
import StatusAlert from '@/components/approveTenantsAlert.vue';
import { update } from 'lodash';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList,
        StatusAlert

    },
    data() {
        return {
            searchTerm: '',
            selectedDormId: '',
            selectedRoomNumber: '', // bound to room number dropdown
            dorms: [],
            selectedroomNumber: '',
            tenants: [],
            selectedtenant: '',
            VisibleTenantModal: false,
            notifications: [],
            hasSubscribed: false,
            receiverID: '',
            landlord_id: '',

        }
    },
    watch:
    {
        'selectedtenant.room.dorm.dormID'(newVal) {
            if (newVal) {
                this.selectedDormId = newVal;
            }
        }
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.landlord_id;
            Echo.private(`notifications.${this.receiverID}`)
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

        async getDormId() {
            const res = await axios.get(`/get-dorms/${this.landlord_id}`);
            this.dorms = res.data.dorms;
            console.log(this.dorms);
        },
        async getTenantList() {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get('/tenants-list', { withCredentials: true });
                this.tenants = response.data.tenant;
                this.tenants = response.data.tenant.data;
            } catch (error) {
                console.error("Error fetching tenant list:", error.response?.data || error.message);
            } finally {
                this.$refs.loader.loading = false;

            }
        },

        async displaytenantInformation(tenantID) {
            // this.$refs.loader.loading = true;
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/tenants-view/${tenantID}`);
                if (response.data.status === 'success') {
                    this.selectedtenant = response.data.tenant;
                    this.VisibleTenantModal = true;
                }

            } catch (error) {
                console.error('Error fetching reservation details:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async updateTenantInformation() {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.put(`/tenants-update/${this.selectedtenant.approvedID}`, this.selectedtenant);
                if (response.data.status === 'success') {
                    this.VisibleTenantModal = false;
                    this.getTenantList();
                }
            } catch (error) {
                console.error('Error updating tenant information:', error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },

    },
    mounted() {
        this.landlord_id = document.getElementById('tenantpage').dataset.landlordId;
        this.subscribeToNotifications();
        this.getTenantList();
        if (this.selectedtenant?.room?.dorm?.dormID) {
            this.selectedDormId = this.selectedtenant.room.dorm.dormID;
        }
        this.getDormId();

    },
    computed:
    {
        uniqueRooms() {
            const seen = new Set();
            return this.tenants.filter(tenants => {
                if (seen.has(tenants.fkroomID)) return false;
                seen.add(tenants.fkroomID);
                return true;
            });
        },
        selectedDorm() {
            return this.dorms.find(d => d.dormID === this.selectedDormId) || {};
        },

        filteredRooms() {
            const dorm = this.selectedDorm;
            return dorm ? dorm.rooms : [];
        }
    },
}
</script>