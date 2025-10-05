<template>
    <Toastcomponents ref="toast" />
    <Modalconfirmation ref="modal" />
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />

    <div class="container py-3">
        <h2 class="fw-bold text-center mb-4 p-2 bg-light text-dark  -2 rounded" style="border:1px solid #4edce2">
            ✨ Book Your Room Now
        </h2>
        <div class="row mb-1">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-person-fill text-primary me-2"></i>Firstname:
                    </label>
                    <div class="p-2  rounded bg-light text-break" style="border:1px solid #4edce2">
                        {{ firstname || 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-person-badge-fill text-primary me-2"></i>Lastname:
                    </label>
                    <div class="p-2  rounded bg-light text-break" style="border:1px solid #4edce2">
                        {{ lastname || 'N/A' }}
                    </div>
                </div>
            </div>

        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-telephone-fill text-primary me-2"></i>Contact Number:
            </label>
            <div class="p-2  rounded bg-light text-break" style="border:1px solid #4edce2">
                {{ contactInfo || 'N/A' }}
            </div>
        </div>

        <!-- Contact Email -->
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-envelope-fill text-primary me-2"></i>Contact Email:
            </label>
            <div class="p-2  rounded bg-light text-break" style="border:1px solid #4edce2">
                {{ email || 'N/A' }}
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-hash text-primary me-2"></i>Room Number:
            </label>
            <div class="p-2  rounded bg-light text-break" style="border:1px solid #4edce2">
                {{ roomsDetail?.roomNumber || 'N/A' }}
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">
                <i class="bi bi-door-open-fill text-primary me-2"></i>Room Type:
            </label>
            <div class="p-2  rounded bg-light text-break" style="border:1px solid #4edce2">
                {{ roomsDetail?.roomType || 'N/A' }}
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-cash-coin text-primary me-2"></i>Monthly Rate:
            </label>
            <div class="p-2  rounded bg-light text-break" style="border:1px solid #4edce2">
                ₱{{ Number(roomsDetail?.price).toLocaleString() || '0.00' }}
            </div>
        </div>
        <div class="d-flex gap-3">
            <!-- Move In -->
            <div class="mb-3 flex-fill">
                <label for="move_in_date" class="form-label fw-semibold">
                    <i class="bi bi-calendar-event me-2 text-primary"></i>Move-in Date
                </label>
                <input type="date" style="border:1px solid #4edce2" class="form-control shadow-sm" v-model="moveInDate"
                    @change="setMoveOutDate" id="move_in_date" :min="today" />
                <span v-if="errors.moveInDate" style="border:1px solid #4edce2"
                    class="error text-danger small mt-1 d-block">
                    <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.moveInDate[0] }}
                </span>
            </div>
            <div class="mb-3 flex-fill">
                <label for="move_out_date" class="form-label fw-semibold">
                    <i class="bi bi-calendar-check me-2 text-primary"></i>Move-out Date
                </label>
                <input type="date" class="form-control shadow-sm" v-model="moveOutDate" id="move_out_date" disabled />
                <span v-if="errors.moveOutDate" class="error text-danger small mt-1 d-block">
                    <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.moveOutDate[0] }}
                </span>
            </div>
        </div>
        <!-- Submit Button -->
        <button type="submit" class="btn btn-success  w-100 py-2 fw-semibold shadow-sm" @click="bookRoom">
            <i class="bi bi-calendar-check-fill me-2"></i>Make a Booking
        </button>

    </div>

</template>

<script>
import axios from 'axios'
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import NotificationList from '@/components/notifications.vue';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList,

    },
    data() {
        return {
            firstname: '',
            lastname: '',
            email: '',
            contactInfo: '',
            age: '',
            sex: '',
            tenant_id: '',
            room_id: '',
            dormitory_id: '',
            isPaymentImage: true,
            idPicturePreview: '',
            selectedRoomId: '',
            roomsDetail: {},
            errors: {},
            moveInDate: this.getTodayLocal(),
            moveOutDate: '',
            today: this.getTodayLocal(),
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            id_picture: '/images/tenant/allimagesResouces/vector-id-card-icon.jpg',
            openPaymentModel: false,
            imageUrl: '',
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
        async getRoomDetails() {
            try {
                const response = await axios.get(`/get-room-details/${this.room_id}`);
                this.roomsDetail = response.data.room;
            } catch (error) {

            }
        },
        tenantData() {
            this.room_id = '';
            this.tenant_id = '';
            this.firstname = '';
            this.lastname = '';
            this.contactInfo = '';
            this.email = '';
            this.age = '';
            this.sex = '';
            this.imageUrl = '';
            this.moveInDate = '';
            this.moveOutDate = '';
        },


        async bookRoom() {
            try {
                const confirmed = await this.$refs.modal.show({
                    title: `Confirm Booking`,
                    message: `Are you sure you want to book Room ${this.roomsDetail?.roomNumber || 'N/A'} (${this.roomsDetail?.roomType || 'Room'})?`,
                    functionName: 'Confirm Room Booking'
                });


                if (!confirmed) {
                    return;
                }
                
                const formdata = new FormData();
                formdata.append('room_id', this.room_id);
                formdata.append('tenant_id', this.tenant_id);
                formdata.append('firstname', this.firstname);
                formdata.append('lastname', this.lastname);
                formdata.append('contact_number', this.contactInfo);
                formdata.append('email', this.email);
                formdata.append('age', this.age);
                formdata.append('gender', this.sex);
                formdata.append('studentpicture_id', this.imageUrl);
                formdata.append('moveInDate', this.moveInDate);
                formdata.append('moveOutDate', this.moveOutDate);
                const response = await axios.post('/book-room', formdata);
                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.tenantData();
                    this.tenant_id = window.tenant_id;
                    window.location.href = `/view/booking/${this.tenant_id}`;

                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const validationErrors = error.response.data.errors;
                    this.errors = validationErrors;
                } else {
                    this.$refs.toast.showToast('Something went wrong. Please try again.', 'danger');
                }
            }


        },
        setMoveOutDate() {
            if (this.moveInDate) {
                // Parse YYYY-MM-DD manually
                const [year, month, day] = this.moveInDate.split('-').map(Number);
                const date = new Date(year, month - 1, day); // Local date

                // Add 1 month
                date.setMonth(date.getMonth() + 1);

                // Format back to YYYY-MM-DD
                const outYear = date.getFullYear();
                const outMonth = String(date.getMonth() + 1).padStart(2, '0');
                const outDay = String(date.getDate()).padStart(2, '0');

                this.moveOutDate = `${outYear}-${outMonth}-${outDay}`;
            } else {
                this.moveOutDate = '';
            }
        },

        getTodayLocal() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`; // YYYY-MM-DD
        },
    },
    mounted() {
        const data = localStorage.getItem('tenantInfo');
        if (data) {
            const parsed = JSON.parse(data);
            this.firstname = parsed.firstname;
            this.lastname = parsed.lastname;
            this.email = parsed.email;
            this.contactInfo = parsed.contactInfo;
            this.age = parsed.age;
            this.sex = parsed.sex;
            this.idPicturePreview = parsed.idPicturePreview;
            this.imageUrl = parsed.imageUrl;
        }
        const element = document.getElementById('roomBook');
        this.dormitory_id = element.dataset.dormId;
        this.room_id = element.dataset.roomId;
        this.tenant_id = element.dataset.tenantId;
        this.getRoomDetails();
        this.subscribeToNotifications();

    }
}
</script>