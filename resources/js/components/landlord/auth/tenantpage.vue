<template>
    <NotificationList ref="toastRef" />
    <Loader ref="loader" />

    <div class="p-4 mt-4">
        <div class="input-group mb-2 w-100 shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none" placeholder="Search Tenants name"
                aria-label="Search Tenant " v-model="searchTerm" />
        </div>
        <div class="py-3 d-flex gap-3 align-items-center">
            <!-- Dorm No Dropdown -->
            <div class="mb-2 d-flex align-items-center gap-2">
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
            <div class="mb-2 d-flex align-items-center gap-2">
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
        <div class="table-responsive shadow-sm rounded p-3 bg-white">
            <!-- Table -->
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-primary bg-primary text-center">
                    <tr>
                        <th>Tenant ID</th>
                        <th>Tenant Name</th>
                        <th>Email</th>
                        <th>Dorm Name</th>
                        <th>Room No</th>
                        <th>End Occupancy Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr v-for="tenant in tenants" :key="tenant.approvedID">
                        <td>{{ tenant.approvedID }}</td>
                        <td>{{ tenant.firstname }} {{ tenant.lastname }}</td>
                        <td>{{ tenant.contactEmail }}</td>
                        <td>{{ tenant.room?.dorm?.dormName ?? 'N/A' }}</td>
                        <td>{{ tenant.room?.roomNumber ?? 'N/A' }}</td>
                        <td>
                            {{ tenant.moveOutDate }}
                        </td>

                        <td>
                            <button class="btn btn-sm btn-primary"
                                @click="displaytenantInformation(tenant.approvedID)">View</button>
                            <button class="btn btn-sm btn-danger ms-2"
                                @click="deleteReservation(tenant.approvedID)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="VisibleTenantModal" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5);"
            tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
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
                                    'bg-success text-white': selectedtenant.status === 'Approved',
                                    'bg-danger text-white': selectedtenant.status === 'Not Approved',
                                    'bg-warning text-dark': selectedtenant.status !== 'Approved' && selectedtenant.status !== 'Not Approved'
                                }">{{ selectedtenant.status }}</span>
                            </p>
                        </div>

                        <!-- Information Grid -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <p><strong>👤 First Name:</strong> {{ selectedtenant.firstname }}</p>
                                <p><strong>🎂 Age:</strong> {{ selectedtenant.age }}</p>
                                <p><strong>📧 Email:</strong> {{ selectedtenant.contactEmail }}</p>
                                <p><strong>📍 Address:</strong> {{ selectedtenant.room?.dorm?.address }}</p>
                                <p><strong>🚪 Room #:</strong> {{ selectedtenant.room?.roomNumber }}</p>
                                <p><strong>📅 Start Occupancy Date:</strong> {{ selectedtenant.moveInDate || 'N/A' }}
                                </p>


                            </div>
                            <div class="col-md-6">
                                <p><strong>👤 Last Name:</strong> {{ selectedtenant.lastname }}</p>
                                <p><strong>🚻 Gender:</strong> {{ selectedtenant.gender }}</p>
                                <p><strong>📱 Contact:</strong> {{ selectedtenant.contactNumber }}</p>
                                <p><strong>🏠 Dorm:</strong> {{ selectedtenant.room?.dorm?.dormName || 'N/A' }}</p>
                                <p><strong>💰 Price:</strong> ₱{{
                                    Number(selectedtenant.room?.price).toLocaleString(undefined, {
                                        minimumFractionDigits: 2
                                    }) }}</p>
                                <p><strong>📅 End Occupancy Date:</strong> {{ selectedtenant.moveOutDate || 'N/A' }}</p>
                            </div>
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
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList

    },
    data() {
        return {
            searchTerm: '',
            selectedDormId: '',
            selectedroomNumber: '',
            tenants: [],
            dorms: [],
            selectedtenant: '',
            VisibleTenantModal: false,
            notifications: [],
            hasSubscribed: false,
            receiverID: '',
            landlord_id: '',

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
        displaydorms() {
            axios.get('/api/dorms')
                .then(response => {
                    this.dorms = response.data;
                });
        },
        dormId(dorm) {
            this.selectedDormId = dorm;
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

    },
    mounted() {
        this.landlord_id = document.getElementById('tenantpage').dataset.landlordId;
        this.subscribeToNotifications();
        this.getTenantList();
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
        }
    },
}
</script>