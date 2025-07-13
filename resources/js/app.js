import 'bootstrap'; // Importing Bootstrap
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { createApp } from 'vue'; // Vue 3
import TenantRegister from './components/tenants/tenantRegister.vue';
import LandlordRegister from './components/landlord/landlordregister.vue';
import LandlordLogin from './components/landlord/landlordlogin.vue';
import LandlordDormManagement from './components/landlord/auth/DormManagement.vue';
import landlordDashboard from './components/landlord/auth/dashboard.vue';
import landlordroomManagement from './components/landlord/auth/roomManagement.vue';
import tenantpage from './components/landlord/auth/tenantpage.vue';
import BookingManagement from './components/landlord/auth/bookingpage.vue';
import MessagingCenter from './components/landlord/auth/messagingCenter.vue';
import ReviewandFeedback from './components/landlord/auth/ReviewandFeedback.vue';
import Notification from './components/landlord/auth/Notification.vue';
import homepage from './components/tenants/auth/homepage.vue';
import tenant from './components/tenants/tenantLogin.vue';
import RoomDetails from './components/tenants/auth/roomdetails.vue';
import roomSelection from './components/tenants/auth/bookingProcess/roomSelection.vue';
import roomBook from './components/tenants/auth/bookingProcess/bookRoom.vue';
import dormitoriesMap from './components/tenants/auth/dormitoriesmap.vue';
import dormitories from './components/tenants/auth/dormitories.vue';
import tenantmessage from './components/tenants/auth/tenantmessage.vue';
import reservationPage from './components/landlord/auth/reservationpage.vue';

import AOS from 'aos';
import 'aos/dist/aos.css'
AOS.init();



const tenantContainer = document.querySelector('#TenantRegisterContainer');
if (tenantContainer) {
    createApp(TenantRegister).mount('#TenantRegisterContainer');
}
const landlordContainer = document.querySelector('#LandlordRegisterContainer');
if (landlordContainer) {
    createApp(LandlordRegister).mount('#LandlordRegisterContainer');
}
const landlordContainerLogin = document.querySelector('#landlordContainerLogin');
if (landlordContainerLogin) {
    createApp(LandlordLogin).mount('#landlordContainerLogin');
}
const dashboardController = document.querySelector('#dashboard');
if (dashboardController) {
    createApp(landlordDashboard).mount('#dashboard');
}
const landlorddormManagementContainer = document.querySelector('#landlorddormManagement');
if (landlorddormManagementContainer) {
    createApp(LandlordDormManagement).mount('#landlorddormManagement');
}
const landlordroomManagementContainer = document.querySelector('#landlordroomManagement');
if (landlordroomManagementContainer) {
    createApp(landlordroomManagement).mount('#landlordroomManagement');

}
const tenantpageContainer = document.querySelector('#tenantpage');
if (tenantpageContainer) {
    createApp(tenantpage).mount('#tenantpage');

}

const BookingManagementController = document.querySelector('#BookingManagement');
if (BookingManagementController) {
    createApp(BookingManagement).mount('#BookingManagement');

}

const MessagingCenterController = document.querySelector('#MessagingCenter');
if (MessagingCenterController) {
    createApp(MessagingCenter).mount('#MessagingCenter');
}
const ReviewandFeedbackController = document.querySelector('#ReviewandFeedback');
if (ReviewandFeedbackController) {
    createApp(ReviewandFeedback).mount('#ReviewandFeedback');

}
const NotificationController = document.querySelector('#Notification');
if (NotificationController) {
    createApp(Notification).mount('#Notification');

}
const homepageController = document.querySelector('#homepage');

if (homepageController) {
    createApp(homepage).mount('#homepage');
}
const tenantController = document.querySelector('#tenant');

if (tenantController) {
    createApp(tenant).mount('#tenant');
}
const RoomDetailsController = document.querySelector('#RoomDetails');

if (RoomDetailsController) {
    createApp(RoomDetails).mount('#RoomDetails');

}
const dormitoriesMapController = document.querySelector('#dormitoriesMap');

if (dormitoriesMapController) {
    createApp(dormitoriesMap).mount('#dormitoriesMap');

}
const dormitoriesController = document.querySelector('#dormitories');

if (dormitoriesController) {
    createApp(dormitories).mount('#dormitories');
}
const roomSelectionController = document.querySelector('#roomSelection');
if (roomSelectionController) {
    createApp(roomSelection).mount('#roomSelection');
}
const roomBookController = document.querySelector('#roomBook');

if (roomBookController) {
    createApp(roomBook).mount('#roomBook');
}
const tenantMessageController = document.querySelector('#tenantmessage');

if (tenantMessageController) {
    createApp(tenantmessage).mount('#tenantmessage');
}
const reservationController = document.querySelector('#reservationPage');

if (reservationController) {
    createApp(reservationPage).mount('#reservationPage');
}

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});




