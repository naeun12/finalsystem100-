<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />

    <div class="mt-5 py-3 px-5 d-flex justify-content-end align-items-end">
        <button class="custom-btn" @click="viewPayment">View Payments History</button>
    </div>
    <div v-if="rooms.length === 0"
        class="text-center p-5 bg-light rounded-4 shadow-lg position-relative overflow-hidden">


        <h4 class="text-muted fw-bold mb-3">No Room found</h4>
        <p class="text-muted fs-6 mb-4">You haven't made any Room yet. Explore dormitories and find your perfect
            room!</p>
    </div>
    <div class="container-fluid py-4" :class="{ 'card-slide': animate }"
        v-for="tenant in rooms.slice(currentIndex, currentIndex + 1)" :key="tenant.roomID">
        <div class="container-fluid">

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Left Card -->
                <div class="col d-flex">
                    <div class="card shadow-sm border-0 w-100" style="border: 1px solid #4edce2;">
                        <div class="card-body text-center" style="border: 1px solid #4edce2;">
                            <img :src="tenant.pictureID" class="rounded-circle mb-3 border border-2 border-primary"
                                width="100" height="100" alt="User Image" />
                            <h5 class="fw-bold mb-1">{{ tenant.firstname }} {{
                                tenant.lastname }}</h5>
                            <span v-if="tenant.status != 'pending'" class="badge rounded-pill px-3 py-2" :class="{
                                'bg-success text-white': tenant.status === 'active',
                                'bg-secondary text-white': tenant.status === 'moved_out',
                                'bg-warning text-dark': tenant.status === 'pending_moveout',
                                'bg-info text-white': tenant.status === 'transferring',
                            }">
                                {{ tenant.status?.replace('_', ' ').toUpperCase() }}
                            </span>
                            <span v-if="tenant.status === 'pending'" class="text-success d-block mt-2">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Note: Your reservation is pending. Please wait for your move-in date and present your
                                receipt to the landlord.
                            </span>


                            <ul class="list-group list-group-flush text-start small">
                                <li class="list-group-item">
                                    <i class="bi bi-gender-male me-2 text-primary"></i>
                                    <strong>Gender:</strong> {{ tenant.gender }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-person-fill me-2 text-secondary"></i>
                                    <strong>Age:</strong> {{ tenant.age }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-envelope-fill me-2 text-danger"></i>
                                    <strong>Email:</strong> {{ tenant.contactEmail }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-telephone-fill me-2 text-success"></i>
                                    <strong>Contact #:</strong> {{ tenant.contactNumber }}
                                </li>
                            </ul>
                            <button class="custom-btn" @click="viewReceipt(tenant.approvedID)">
                                <i class="bi bi-file-earmark-pdf"></i> View Receipt
                            </button>

                        </div>
                    </div>
                </div>

                <!-- Middle Card -->
                <div class="col d-flex">
                    <div class="card shadow-sm border-0 w-100">
                        <img :src="tenant.room?.roomImages" class="card-img-top"
                            style="height: 200px; object-fit: cover;" alt="Room Image" />

                        <div class="card-body" style="border: 1px solid #4edce2;">
                            <h5 class="card-title mb-1">Room #{{ tenant.room?.roomNumber }}</h5>

                            <div class="mb-2">
                                <span class="badge bg-primary me-1">{{ tenant.room?.roomType }}</span>
                            </div>

                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item">
                                    <i class="bi bi-currency-peso text-success me-2"></i>
                                    <strong>Monthly Payment:</strong> ₱{{ tenant.room?.price }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-house-door-fill text-info me-2"></i>
                                    <strong>Furnishing:</strong> {{ tenant.room?.furnishing_status }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-box-fill text-warning me-2"></i>
                                    <strong>Listing Type:</strong> {{ tenant.room?.listingType }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-rulers text-secondary me-2"></i>
                                    <strong>Area:</strong> {{ tenant.room?.areaSqm }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-gender-male text-purple me-2"></i>
                                    <strong>For:</strong> {{ tenant.room?.genderPreference }}
                                </li>
                            </ul>
                            <div class="text-center"
                                v-if="getDaysStayed(tenant.moveInDate) >= 2 && tenant.status === 'active' ">
                                <!-- If not yet reviewed -->
                                <div v-if="alreadyReviewed === tenant.has_rated">
                                    <h5 class="mb-3 text-primary fw-bold border-bottom pb-2">
                                        <i class="bi bi-star-fill text-warning me-2"></i>
                                        Rate Dorm
                                    </h5>

                                    <div class="rating">
                                        <i v-for="star in 5" :key="star" class="bi"
                                            :class="star <= currentRating ? 'bi-star-fill text-warning' : 'bi-star text-secondary'"
                                            style="font-size: 2rem; cursor: pointer;" @click="setRating(star)">
                                        </i>
                                    </div>

                                    <p class="mt-2">Your Rating: <strong>{{ currentRating }}</strong> / 5</p>
                                    <textarea class="form-control" v-model="currentReview"
                                        placeholder="Reviews"></textarea>

                                    <button class="btn btn-primary mt-3" :disabled="currentRating === 0"
                                        @click="reviewandrating(tenant)">
                                        Submit Rating
                                    </button>
                                </div>

                                <!-- If already reviewed -->
                                <div v-else class="alert alert-success mt-3">
                                    ✅ You have already reviewed this dorm.
                                </div>
                            </div>

                            <!-- If less than 3 days -->
                            <div v-else v-if="tenant.status != 'pending' && tenant.status != 'moved_out'"
                                class="alert alert-warning text-center shadow-sm p-3 mt-3">
                                <i class="bi bi-hourglass-split me-2"></i>
                                Please wait at least <strong>3 days</strong> before you can rate your Dorm.
                            </div>

                            <!-- Reviews List -->

                            <div v-if="tenant.status != 'pending'" class="mt-2 d-flex justify-content-center">
                                <button class="custom-btn" @click="messageMaintenance()"> Report Maintenance Issue
                                </button>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Card -->
                <div class="col d-flex">
                    <div class="card shadow-sm border-0 w-100">
                        <div class="card-body text-center" style="border: 1px solid #4edce2;">
                            <h5 class="fw-bold mb-2">🏠 Tenant Lease Summary

                            </h5>

                            <ul class="list-group list-group-flush text-start small">
                                <li class="list-group-item">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>
                                    <strong>Lease Start:</strong> {{ formatDate(tenant.moveInDate) }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-calendar2-check-fill text-success me-2"></i>
                                    <strong>Lease End:</strong> {{ formatDate(tenant.moveOutDate) }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-currency-peso text-info me-2"></i>
                                    <strong class="">💸 Monthly Payment:</strong>
                                    ₱{{ tenant.room?.price }}
                                </li>
                                <li v-if="tenant.status != 'pending'" class="list-group-item">
                                    <i class="bi bi-clock-history text-warning me-2"></i>
                                    <strong>Days Remaining: </strong>
                                    <span class="text-success">
                                        {{ getRemainingLeaseDays(tenant.moveInDate,
                                        tenant.moveOutDate) }}
                                    </span>
                                </li>

                            </ul>
                            <div v-if="tenant.extension_payment_status === 'done' "
                                class="alert alert-info text-center p-3 rounded shadow-sm">
                                <p class="mb-0 fw-semibold">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Your rent extension has been successfully paid and approved. You may view the
                                    details in your payment history.
                                </p>
                            </div>

                            <div class="mt-2 mb-4 p-3 border rounded shadow-sm bg-light small"
                                v-if="tenant.notifyRent == 1">
                                <h6 class="fw-bold text-primary text-center mb-3">
                                    💰 Please choose an option for the rent extension request
                                </h6>

                                <!-- Extend Button -->
                                <button class="btn btn-success btn-sm px-4" @click="updateRentStatus(tenant, 'extend')">
                                    ✅ Extend
                                </button>

                                <!-- Not Extending Button -->
                                <button class="btn btn-danger btn-sm px-4"
                                    @click="updateRentStatus(tenant, 'not_extending')">
                                    ❌ Not Extending
                                </button>

                            </div>
                            <div class="mt-2 mb-4 p-3 border rounded shadow-sm bg-light small"
                                v-if="tenant.extension_decision === 'not_extending'">
                                <h6 class="fw-bold text-primary text-center mb-3">
                                    ❌ You have chosen not to extend your lease.
                                </h6>
                                <p class="text-muted">
                                    Please be reminded that your lease will end on
                                    <strong>{{ formatDate(tenant.moveOutDate) }}</strong>. Kindly coordinate with your
                                    landlord for the move-out process.
                                </p>

                            </div>
                            <div class="mt-2 mb-4 p-3 border rounded shadow-sm bg-light small"
                                v-if="tenant.extension_decision === 'extend'">
                                <h6 class="fw-bold text-primary text-center mb-3">
                                    💰 Extension Payment Details
                                </h6>

                                <p>
                                    <i class="bi bi-calendar-event text-secondary"></i>
                                    <strong> Billing Period:</strong>
                                    {{ formatDate(tenant.moveInDate) }} – {{ formatDate(tenant.moveOutDate) }}
                                </p>

                                <p>
                                    <i class="bi bi-cash-coin text-success"></i>
                                    <strong> Room Monthly Rate:</strong>
                                    <span class="text-danger fw-bold">₱{{ tenant.room.price }}</span>
                                </p>

                                <p v-if="tenant.payments[0]?.status === 'Approved'">
                                    <i class="bi bi-wallet2 text-info"></i>
                                    <strong> Amount Paid:</strong>
                                    <span class="text-success fw-bold">
                                        ₱{{ Number(tenant.payments[0]?.amount || 0).toLocaleString('en-PH', {
                                        minimumFractionDigits: 2
                                        }) }}
                                    </span>
                                </p>
                                <p v-if="tenant.paymentOption === 'online'">
                                    Payment Status:
                                    <span class="badge" :class="{
                                        'bg-success': tenant.payments[0]?.status === 'approved',
                                        'bg-warning text-dark': tenant.payments[0]?.status === 'pending',
                                        'bg-danger': tenant.payments[0]?.status === 'rejected'
                                    }">
                                        {{ tenant.payments[0]?.status || 'No Payment' }}
                                    </span>
                                </p>
                                <button class="custom-btn" type="button" @click="extendrentModal(tenant)"> Extend
                                    Rent
                                </button>
                            </div>
                            <div class="text-center mt-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="extendRateModal" class="modal fade show radius-3 d-block" tabindex="-1"
                style="background-color: rgba(0,0,0,0.5);" @click.self="extendRateModal = false">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title text-white">Extend Payment </h5>
                            <button type="button" class="btn-close text-white"
                                @click="extendRateModal = false"></button>
                        </div>

                        <div class="modal-body">
                            <!-- Payment Option (Online or On-site) -->
                            <div class="card shadow-sm border-0 rounded-4 mb-3">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-2">
                                        <i class="bi bi-wallet2 me-2"></i> Choose Payment Option
                                    </h6>
                                    <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap mt-2">
                                        <!-- Online Option -->
                                        <div class="text-center p-3 border rounded shadow-sm d-flex flex-column align-items-center justify-content-between"
                                            style="cursor: pointer; width: 120px; height: 120px;"
                                            @click="paymentOption('online')">
                                            <i class="bi bi-globe2 fs-1 text-info mb-2"></i>
                                            <small class="fw-semibold text-capitalize">Online</small>
                                        </div>

                                        <!-- On-site Option -->
                                        <div class="text-center p-3 border rounded shadow-sm d-flex flex-column align-items-center justify-content-between"
                                            style="cursor: pointer; width: 120px; height: 120px;"
                                            @click="paymentOption('onsite')">
                                            <i class="bi bi-house-door fs-1 text-success mb-2"></i>
                                            <small class="fw-semibold text-capitalize">On-site</small>
                                        </div>
                                    </div>
                                    <div class="justify-content-center d-flex mt-2">
                                        <span v-if="errors.payment_option" class="text-danger small mt-1 d-block">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{
                                            errors.payment_option[0] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="container py-4 mb-4" v-if="payment_option === 'online'">
                                <div class="card shadow-sm border-0 rounded-4 mb-3">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-primary mb-2">
                                            <i class="bi bi-credit-card-2-front-fill me-2"></i> Landlord GCash Number
                                        </h6>
                                        <div class="p-3 bg-light rounded-3 border text-center">
                                            <span class="fw-semibold fs-5 text-dark">
                                                {{ tenant.room?.dorm.gcashNumber }}
                                            </span>
                                        </div>
                                        <p class="text-muted mt-2 mb-0 small">
                                            Use this number when sending your payment via GCash.
                                        </p>
                                    </div>
                                </div>
                                <!-- Payment Options -->
                                <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap mt-3">
                                    <div v-for="(src, name) in payment" :key="name"
                                        class="text-center p-3 border rounded shadow-sm d-flex flex-column align-items-center justify-content-between"
                                        :class="{ 'border-primary bg-light': payment_type === name }" role="button"
                                        style="cursor: pointer; width: 120px; height: 130px;"
                                        @click="paymentTypeSelection(name)">
                                        <img :src="src" :alt="name" class="img-fluid mb-2"
                                            style="width: 50px; height: 50px; object-fit: contain;" />
                                        <small class="fw-semibold text-capitalize text-center">
                                            {{ name.replace('_', ' ') }}
                                        </small>
                                    </div>

                                </div>
                                <div class="justify-content-center d-flex mt-2">
                                    <span v-if="errors.paymentType" class="text-danger small mt-1 d-block">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.paymentType[0] }}
                                    </span>
                                </div>
                                <div class="border border-secondary rounded-3 p-4 mb-3 text-center"
                                    style="cursor: pointer;" v-if="isPaymentImage" @click="triggerPaymentImage">
                                    <input ref="PaymentPicturesInput" class="d-none" type="file" accept="image/*"
                                        @change="handlePaymentPicture" />
                                    <div class="d-flex flex-column align-items-center text-center mb-3">
                                        <img :src="paymentIcon" alt="Payment Icon"
                                            style="max-width: 60px; height: auto;" class="mb-2" />
                                        <h5 class="text-secondary mt-2">Upload Payment Image</h5>
                                        <small class="text-muted">Click to browse and select an image file</small>
                                    </div>
                                </div>
                                <div class="justify-content-center d-flex mb-2">
                                    <span v-if="errors.PaymentPictureFile" class="text-danger small mt-1 d-block">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>{{
                                        errors.PaymentPictureFile[0]
                                        }}
                                    </span>
                                </div>
                                <div v-if="PaymentPicturePreview" class="text-center mb-3">
                                    <img :src="PaymentPicturePreview" alt="Uploaded Payment Image"
                                        class="img-fluid rounded mb-2" style="max-height: 250px;" />
                                    <div>
                                        <button type="button" @click="removePaymentPicture"
                                            class="btn btn-danger shadow-sm">
                                            Remove Uploaded Image
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="payment_option === 'onsite'" class="alert alert-warning mt-3 shadow-sm">
                            <i class="bi bi-cash-stack me-2"></i>
                            You chose <strong>On-Site Payment</strong>.
                            Kindly meet your landlord to complete the payment process.
                        </div>


                        <div class="modal-footer">
                            <button class="btn btn-success" @click="submitRent(tenant)">Submit Extension Rent</button>
                        </div>

                    </div>
                </div>
            </div>
            <Toastcomponents ref="toast" />
            <div v-if="messageModal" class="modal fade show d-block" tabindex="-1"
                style="background-color: rgba(0,0,0,0.5);" @click.self="messageModal = false">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content shadow-lg rounded-4 border-0">

                        <!-- Header -->
                        <div class="modal-header  text-black border-0 rounded-top">
                            <h5 class="modal-title">Report to Landlord</h5>
                            <button type="button" class="btn-close btn-close-black"
                                @click="messageModal = false"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <p class="text-muted small mb-3">Select the issue you want to report to your landlord:</p>

                            <div class="list-group mb-3">
                                <button v-for="(label, key) in issues" :key="key"
                                    class="list-group-item list-group-item-action rounded-3 mb-2 shadow-sm"
                                    @click="selectIssue(label)">
                                    {{ label }}
                                </button>
                            </div>

                            <div v-if="selectedIssue" class="alert alert-info text-center fw-bold">
                                Selected Issue: <span class="text-dark">{{ selectedIssue }}</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer border-0 justify-content-between">
                            <button class="btn btn-outline-secondary rounded-pill" @click="messageModal = false">
                                Cancel
                            </button>
                            <button class="btn btn-primary rounded-pill px-4" :disabled="!selectedIssue"
                                @click="sendIssue(tenant)">
                                Send to Landlord
                            </button>
                        </div>

                    </div>
                </div>
            </div>


        </div>
        <div class="mt-4 d-flex justify-content-center gap-3">
            <button class="btn btn-danger " @click="prevCard(tenant.approvedID)" :disabled="currentIndex === 0">⬅️
                Prev</button>
            <button class="btn btn-primary" @click="nextCard(tenant.approvedID)"
                :disabled="currentIndex >= rooms.length - 1">Next
                ➡️</button>
        </div>


    </div>

    <Toastcomponents ref="toast" />
    <Modalconfirmation ref="modal" />


</template>
<script>
import axios from 'axios';
import { nextTick } from 'vue';
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
            tenantid: '',
            rooms: [],
            extendRateModal: false,
            messageModal: false,
            selectedIssue: "",
            comments: '',
            issues: {
                water_leak: "💧 Water Leak",
                broken_light: "💡 Broken Light",
                wifi_problem: "📶 Wi‑Fi Problem",
                appliance_damage: "🔌 Appliance Damage",
                other: "🛠 Other"
            },
            currentIndex: 0,
            errors: {},
            payment_type: 'online',
            payment: '',
            animate: false,
            isPaymentImage: true,
            currentRating: 0,
            currentReview: '',
            reviews: [],
            alreadyReviewed: 0,
            payment: {
                gcash: '/images/tenant/allimagesResouces/GCash-Logo.png',

            },
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            PaymentPicturePreview: '',
            PaymentPictureFile: null,
            notifications: [],
            receiverID: '',
            approvedID: '',
            payment_option: '',

        }
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.tenantid;
            Echo.private(`notifications.${this.tenantid}`)
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

        async roomsList() {
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get(`/tenant/room-list/${this.tenantid}`);
                this.rooms = response.data.rooms;
            } catch (error) {
                console.error('Failed to fetch room list:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        getRemainingLeaseDays(moveIn, moveOut) {
            if (!moveIn || !moveOut) return 'Incomplete dates';

            // Parse properly
            const today = new Date();
            const moveOutDate = new Date(moveOut);

            // Zero out time to avoid timezone issues
            today.setHours(0, 0, 0, 0);
            moveOutDate.setHours(0, 0, 0, 0);

            // Calculate the difference in milliseconds then convert to days
            const diffInMs = moveOutDate - today;
            const remainingDays = Math.ceil(diffInMs / (1000 * 60 * 60 * 24));

            if (remainingDays < 0) return 'Lease ended';
            return `${remainingDays} day(s) remaining`;
        },

        nextCard(approvedID) {
            this.approvedID = approvedID;
            if (this.currentIndex < this.rooms.length - 1) {
                this.currentIndex++;
                this.triggerAnimation();
            }
        },
        prevCard(approvedID) {
            this.approvedID = approvedID;

            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.triggerAnimation();
            }
        },
        triggerAnimation() {
            this.animate = false;
            this.$nextTick(() => {
                this.animate = true;
            });
        },
        viewPayment() {
            window.location.href = `/view/payment/${this.tenantid}`;

        },
        viewReceipt(id) {
            window.open(`/tenant/${id}/receipt`, '_blank');
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
        paymentTypeSelection(name) {
            this.payment_type = name;
        },
        triggerPaymentImage() {
            if (this.$refs.PaymentPicturesInput) {
                this.$refs.PaymentPicturesInput[0].click();
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
        selectIssue(label) {
            this.selectedIssue = label;
        },
        messageMaintenance() {
            this.messageModal = true;
        },
        sendIssue(tenant) {
            // Example: redirect to chat with pre-filled message
            const message =
                `Hello, I would like to report this issue: ${this.selectedIssue}`;
            const trimmedMessage = message.trim();

            if (!trimmedMessage) return;
            this.$refs.loader.loading = true;

            axios.post('/send/issue', {
                message: trimmedMessage,
                senderID: this.tenantid,
                receiverID: tenant.room?.dorm?.fklandlordID,
                senderRole: 'tenant',
            })
                .then(() => {
                    window.location.href = `/tenant-message-nav/${this.tenantid}`;
                    this.message = '';
                    this.selectedIssue = '';
                    this.$refs.loader.loading = false;

                })
                .catch(err => {
                    console.error("❌ Failed to send message:", err);
                    alert("Failed to send message. Please try again.");
                    this.$refs.loader.loading = false;
                });


            // Close modal
            this.showIssueModal = false;
        },
        setRating(star) {
            this.currentRating = star;
        },
        getDaysStayed(moveInDate) {
            if (!moveInDate) return 0;
            const today = new Date();
            const start = new Date(moveInDate);

            // Zero out time values para dili maapektuhan sa timezone
            today.setHours(0, 0, 0, 0);
            start.setHours(0, 0, 0, 0);
            const diffInMs = today - start;
            return Math.floor(diffInMs / (1000 * 60 * 60 * 24)); // convert to days
        },
        extendrentModal(tenant) {
            this.extendRateModal = true
        },
        async submitRent(tenant) {
            const confirmed = await this.$refs.modal.show({
                title: `Extension Payment`,
                message: `Are you sure you want to proceed with this extension payment?`,
                functionName: 'Confirm Extension Payment'
            });

            if (!confirmed) {
                return;
            }
            const formdata = new FormData();
            formdata.append('paymentType', this.payment_type);
            formdata.append('approveID', tenant.approvedID);
            formdata.append('amount', tenant.room?.price);
            formdata.append('paymentImage', this.PaymentPictureFile);
            formdata.append('paymentOption',this.payment_option);
            try {
                const response = await axios.post('/extend-rent', formdata,
                    {
                        headers: { 'Content-Type': 'multipart/form-data' }

                    }
                );
                if (response.data.status === 'success') {
                    this.extendRateModal = false;
                    this.PaymentPictureFile = null;
                    this.PaymentPicturePreview = null;
                    this.isPaymentImage = true;
                    this.roomsList();
                    this.$refs.toast.showToast(response.data.message, 'success');
                }
            }
            catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    this.$refs.toast.showToast('Double Check your Payment', 'danger');

                }
            }
            finally {

            }
        },
        async reviewandrating(tenant) {

            const formdata = new FormData();
            formdata.append('roomID', tenant.room?.roomID);
            formdata.append('approvedID', tenant.approvedID);
            formdata.append('rating', this.currentRating);
            formdata.append('review', this.currentReview);

            try {
                this.$refs.loader.loading = true;

                let res = await axios.post('/reviewandrating', formdata);

                if (res.data.success) {
                    this.$refs.loader.loading = false;
                    this.$refs.toast.showToast(res.data.message, 'success');
                    this.roomsList();
                    this.currentRating = 0;
                    this.currentReview = '';
                }
            } catch (err) {
                if (err.response?.status === 409) {
                    this.$refs.toast.showToast('You have already reviewed this dorm.', 'warning');
                } else {
                    console.error(err);
                    this.$refs.toast.showToast('Error submitting review', 'error');
                }
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async updateRentStatus(tenant, decision) {
            try {
                let actionText = '';
                if (decision === 'extend') {
                    actionText = 'approve this extension request';
                } else if (decision === 'not_extending') {
                    actionText = 'reject this extension request';
                } else {
                    actionText = 'set as pending';
                }

                const confirmed = await this.$refs.modal.show({
                    title: `Confirm Action`,
                    message: `Are you sure you want to ${actionText}?`,
                    functionName: 'Confirm'
                });

                if (!confirmed) {
                    return;
                }
                this.$refs.loader.loading = true;

                const formdata = new FormData();
                formdata.append('approveID', tenant.approvedID);
                formdata.append('decision', decision);

                const response = await axios.post('/update/rentstatus', formdata);

                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(
                        `The request has been successfully ${decision}.`,
                        'success'
                    );
                    this.roomsList();
                    
                } else {
                    this.$refs.toast.showToast('Something went wrong.', 'error');
                }
            }
            catch (error) {
                console.error(error);
                this.$refs.toast.showToast('Server error, please try again later.', 'error');
            }
            finally {
                this.$refs.loader.loading = false;
            }
        },
        paymentOption(payment_option)
        {
            this.payment_option = payment_option;
        },
        formatDate(date) {
            if (!date) return "N/A";
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },


    },
    mounted() {
        nextTick(() => {
            const el = document.getElementById('myRooms');
            if (el) {
                this.tenantid = el.getAttribute('tenant_id')?.trim();
                this.roomsList();
                this.subscribeToNotifications();

            } else {
                console.warn('nextPayment div not found');
            }
        });
    }

}
</script>
<style>
.rating i:hover {
    transform: scale(1.2);
    transition: transform 0.2s;
}

.card-slide {
    animation: slideIn 0.3s ease-in-out;
}

@keyframes slideIn {
    0% {
        transform: translateX(30px);
        opacity: 0;
    }

    100% {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
