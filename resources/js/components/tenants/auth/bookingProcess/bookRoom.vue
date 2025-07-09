<template>
    <Toastcomponents ref="toast" />
    <Modalconfirmation ref="modal" />
    <Loader ref="loader" />

    <div class="container py-3">
        <div class="row mb-1">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-person-fill text-primary me-2"></i>Firstname:
                    </label>
                    <div class="p-2 border rounded bg-light text-break">
                        {{ firstname || 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-person-badge-fill text-primary me-2"></i>Lastname:
                    </label>
                    <div class="p-2 border rounded bg-light text-break">
                        {{ lastname || 'N/A' }}
                    </div>
                </div>
            </div>

        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-telephone-fill text-primary me-2"></i>Contact Number:
            </label>
            <div class="p-2 border rounded bg-light text-break">
                {{ contactInfo || 'N/A' }}
            </div>
        </div>

        <!-- Contact Email -->
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-envelope-fill text-primary me-2"></i>Contact Email:
            </label>
            <div class="p-2 border rounded bg-light text-break">
                {{ email || 'N/A' }}
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-hash text-primary me-2"></i>Room Number:
            </label>
            <div class="p-2 border rounded bg-light text-break">
                {{ roomsDetail?.roomNumber || 'N/A' }}
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">
                <i class="bi bi-door-open-fill text-primary me-2"></i>Room Type:
            </label>
            <div class="p-2 border rounded bg-light text-break">
                {{ roomsDetail?.roomType || 'N/A' }}
            </div>
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="bi bi-cash-coin text-primary me-2"></i>Price:
            </label>
            <div class="p-2 border rounded bg-light text-break">
                ₱{{ Number(roomsDetail?.price).toLocaleString() || '0.00' }}
            </div>
        </div>
        <div class="container py-4 mb-4">
            <div class="mb-3">
                <label for="paymentType" class="form-label fw-semibold text-dark">
                    <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Payment
                    Method
                </label>
                <input type="text" class="form-control form-control-lg shadow-sm" id="paymentType"
                    v-model="payment_type" placeholder="e.g., GCash, Bank Transfer, Maya" readonly
                    aria-describedby="paymentTypeHelp" />
                <div id="paymentTypeHelp" class="form-text text-muted">
                    Specify your preferred method of payment.
                </div>
            </div>

            <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap mt-3">
                <div v-for="(src, name) in payment" :key="name"
                    class="text-center p-3 border rounded shadow-sm d-flex flex-column align-items-center justify-content-between"
                    :class="{ 'border-primary bg-light': payment_type === name }" role="button"
                    style="cursor: pointer; width: 120px; height: 130px;" @click="paymentTypeSelection(name)"
                    title="Click to select">
                    <img :src="src" :alt="name" class="img-fluid mb-2"
                        style="width: 50px; height: 50px; object-fit: contain;" />
                    <small class="fw-semibold text-capitalize text-center">
                        {{ name.replace('_', ' ') }}
                    </small>
                </div>
            </div>

        </div>

        <!-- Payment Image Upload -->
        <div class="border border-secondary rounded-3 p-4 mb-3 text-center" style="cursor: pointer;"
            v-if="isPaymentImage" @click="triggerPaymentImage">

            <input ref="PaymentPicturesInput" class="d-none" type="file" accept="image/*"
                @change="handlePaymentPicture" />

            <!-- Payment Icon -->
            <div class="d-flex flex-column align-items-center text-center mb-3">
                <img :src="paymentIcon" alt="Payment Icon" style="max-width: 60px; height: auto;" class="mb-2" />
                <h5 class="text-secondary mt-2">Upload Payment Image</h5>
                <small class="text-muted">Click to browse and select an image
                    file</small>
            </div>



        </div>


        <!-- Image Preview -->
        <div v-if="PaymentPicturePreview" class="text-center mb-3">
            <img :src="PaymentPicturePreview" alt="Uploaded Payment Image" class="img-fluid rounded mb-2"
                style="max-height: 250px;" />
            <div>
                <button type="button" @click="removePaymentPicture" class="btn  btn-sm">
                    Remove Uploaded Image
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn  w-100 py-2 fw-semibold shadow-sm" @click="bookRoom">
            <i class="bi bi-calendar-check-fill me-2"></i>Make a Booking
        </button>

    </div>

</template>

<script>
import axios from 'axios'
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
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            id_picture: '/images/tenant/allimagesResouces/vector-id-card-icon.jpg',
            payment: {
                gcash: '/images/tenant/allimagesResouces/GCash-Logo.png',
                maya: '/images/tenant/allimagesResouces/maya.png',
                bank_transer: '/images/tenant/allimagesResouces/bank-transfer-logo.png',

            },
            payment_type: '',
            openPaymentModel: false,
            PaymentPicturePreview: '',
            PaymentPictureFile: null,
            imageUrl: '',
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
        }
    },
    methods: {

        async getRoomDetails() {
            try {
                const response = await axios.get(`/get-room-details/${this.room_id}`);
                this.roomsDetail = response.data.room;
            } catch (error) {

            }
        },
        handlePaymentPicture(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.PaymentPicturePreview) {
                    URL.revokeObjectURL(this.PaymentPicturePreview);
                }
                this.PaymentPictureFile = file;
                this.isPaymentImage = false;

                this.PaymentPicturePreview = URL.createObjectURL(file);
            }
        },
        triggerPaymentImage() {
            if (this.$refs.PaymentPicturesInput) {
                this.$refs.PaymentPicturesInput.click();
            }
        },
        removePaymentPicture() {
            if (this.PaymentPicturePreview) {
                URL.revokeObjectURL(this.PaymentPicturePreview);
            }
            this.PaymentPicturePreview = null;
            // Add null check for safety
            if (this.$refs.PaymentPicturePreview) {
                this.$refs.PaymentPicturePreview.value = ''; // Reset file input
            }
            this.isPaymentImage = true;
        },
        paymentTypeSelection(name) {
            this.payment_type = name;
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
                formdata.append('payment_type', this.payment_type)
                formdata.append('gender', this.sex);
                formdata.append('payment_image', this.PaymentPictureFile)
                formdata.append('studentpicture_id', this.imageUrl);

                const response = await axios.post('/book-room', formdata);
                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.reservationDetailsModal = false;
                }


            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const validationErrors = error.response.data.errors;
                    let message = "";
                    for (const key in validationErrors) {
                        message += `${validationErrors[key][0]}\n`;  // ✅ Will show your custom message
                    }
                    this.$refs.toast.showToast(message.trim(), 'danger');
                } else {
                    this.$refs.toast.showToast('Something went wrong. Please try again.', 'danger');
                }
            }


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
    }
}
</script>