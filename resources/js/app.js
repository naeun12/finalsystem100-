import 'bootstrap'; // Importing Bootstrap
import { createApp } from 'vue'; // Vue 3
import TenantRegister from './components/tenants/tenantRegister.vue';
import LandlordRegister from './components/landlord/landlordregister.vue';
import LandlordLogin from './components/landlord/landlordlogin.vue';
import LandlordDormManagement from './components/landlord/auth/DormManagement.vue';
import landlordroomManagement from './components/landlord/auth/roomManagement.vue';
import tenantAuth from './components/landlord/auth/tenant.vue';
import tenantScreening from './components/landlord/auth/tenantScreening.vue';
import BookingManagement from './components/landlord/auth/bookingManagement.vue';
import Analytics from './components/landlord/auth/analytics.vue';
import MessagingCenter from './components/landlord/auth/messagingCenter.vue';
import ReviewandFeedback from './components/landlord/auth/ReviewandFeedback.vue';
import Notification from './components/landlord/auth/Notification.vue';
import homepage from './components/tenants/auth/homepage.vue';
import tenant from './components/tenants/tenantLogin.vue';
import RoomDetails from './components/tenants/auth/roomdetails.vue';
import dormitoriesMap from './components/tenants/auth/dormitoriesmap.vue';
import dormitories from './components/tenants/auth/dormitories.vue';


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
const landlorddormManagementContainer = document.querySelector('#landlorddormManagement');
if (landlorddormManagementContainer) {
    createApp(LandlordDormManagement).mount('#landlorddormManagement');

}
const landlordroomManagementContainer = document.querySelector('#landlordroomManagement');
if (landlordroomManagementContainer) {
    createApp(landlordroomManagement).mount('#landlordroomManagement');

}
const tenantAuthContainer = document.querySelector('#tenant');
if (tenantAuthContainer) {
    createApp(tenantAuth).mount('#tenant');

}
const tenantScreeningContainer = document.querySelector('#tenantScreening');
if (tenantScreeningContainer) {
    createApp(tenantScreening).mount('#tenantScreening');

}
const BookingManagementController = document.querySelector('#BookingManagement');
if (BookingManagementController) {
    createApp(BookingManagement).mount('#BookingManagement');

}
const AnalyticsController = document.querySelector('#Analytics');
if (AnalyticsController) {
    createApp(Analytics).mount('#Analytics');

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



