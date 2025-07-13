<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\landingPageController;
use App\Http\Middleware\AfterRegistrationMiddleware;
use App\Http\Middleware\TenantAuth;


use App\Http\Controllers\tenant\tenantController;
use App\Http\Controllers\tenant\auth\homepageController;
use App\Http\Controllers\tenant\auth\dormdetailscontroller;

use App\Http\Controllers\landlord\auth\landlordaccountprocessController;
use App\Http\Controllers\landlord\auth\dashboardController;
use App\Http\Controllers\landlord\auth\dormpageController;
use App\Http\Controllers\landlord\auth\roompageController;
// use App\Http\Controllers\landlard\auth\TenantController;
use App\Http\Controllers\landlord\auth\bookingpageController;
use App\Http\Controllers\landlord\auth\MessagingCenterController;
use App\Http\Controllers\landlord\auth\NotificationController;
use App\Http\Controllers\landlord\auth\reservationController;
use App\Http\Controllers\landlord\auth\alltenantsController;
use App\Http\Controllers\tenant\auth\dormitoriesmapcontroller;
use App\Http\Controllers\tenant\auth\dormitories;
use App\Http\Controllers\tenant\auth\bookingprocess\selectionRoomController;
use App\Http\Controllers\tenant\auth\bookingprocess\bookroomController;
use App\Http\Controllers\tenant\auth\tenantmessageController;
use App\Models\tenant\messageModel;
use App\Models\Landlord;
use App\Http\Middleware\LandlordAuth;


//view landingpage, login and register for landlord and tenant
Route::get('/', [landingPageController::class, 'landingPage'])->name('landingpage');
Route::get('/tenantLogin', [tenantController::class, 'login'])->name('login-tenant');
Route::get('/tenantRegister', [tenantController::class, 'register'])->name('register-tenant');
Route::get('/landlordLogin', [landlordaccountprocessController::class, 'landlordLogin'])->name('landlord-Login');
Route::post('/tenant-logout', [tenantController::class, 'logout'])->name('tenant.logout');
Route::post('/logout', [landlordaccountprocessController::class, 'logout'])->name('logout');


Route::get('/landlordregister', [landlordaccountprocessController::class, 'landlordRegister'])->name('register-landlord');
//Route::get('/verify', [accountprocessController::class, 'startVerification'])->name('verify-otp');
Route::post('/verify-registration', [tenantController::class, 'verifyRegistration'])->name('verify.registration');
//creating tenants account

Route::match(['get', 'post'], '/SendOtp', [tenantController::class, 'SendOtp'])->name('SendOtp');
Route::match(['get', 'post'], '/resendOtp', [tenantController::class, 'resendOtp'])->name('resendOtp');
Route::match(['get', 'post'], '/registerTenant', [tenantController::class, 'registerTenant'])->name('registerTenant');
Route::match(['get', 'post'], 'tenant-login',[tenantController::class,'loginTenant'])->name('tenant-login');
//landlord login
Route::match(['get', 'post'], '/loginLandlord', [landlordaccountprocessController::class, 'loginLandlord'])->name('loginLandlord');



//landlord

Route::match(['get', 'post'], '/personalDetails', [landlordaccountprocessController::class, 'personalDetails'])->name('stepOne');
Route::post('/IdentityVerifaction', [landlordaccountprocessController::class, 'IdentityVerifaction'])->name('IdentityVerifaction');
Route::post('/businessPermitValidation', [landlordaccountprocessController::class, 'businessPermitValidation'])->name('businessPermitValidation');
Route::post('/RegisterLandlord', [landlordaccountprocessController::class, 'RegisterLandlord'])->name('RegisterLandlord');

//landlord auth

Route::middleware([LandlordAuth::class])->group(function () {
    Route::match(['get', 'post'], '/landlordDashboard/{landlordId}', [dashboardController::class, 'landlordDashboard'])->name('landlordDashboard');
    Route::match(['get', 'post'], '/landlordDormManagement/{landlordId}', [dormpageController::class, 'DormManagement'])->name('landlordDormManagement');
    Route::match(['get', 'post'], '/input-text', [dormpageController::class, 'inputFieldDorm'])->name('input-text');
    Route::match(['get', 'post'], '/upload-main-image', [dormpageController::class, 'uploadmainImage'])->name('upload-main-image');
    Route::match(['get', 'post'], '/upload-secondary-image', [dormpageController::class, 'uploadsecondaryImage'])->name('upload-secondary-image');
    Route::match(['get', 'post'], '/uplad-third-image', [dormpageController::class, 'uploadthirdImage'])->name('upload-third-image');
    Route::match(['get', 'post'], '/edit-main-image', [dormpageController::class, 'editmainImage'])->name('edit-main-image');
    Route::match(['get', 'post'], '/edit-secondary-image', [dormpageController::class, 'editsecondaryImage'])->name('edit-secondary-image');
    Route::match(['get', 'post'], '/edit-images/{id}', [dormpageController::class, 'imageUpdated'])->name('edit-images');
    Route::match(['get', 'post'], '/landlordRoomManagement/{landlordId}', [roompageController::class, 'RoomManagement'])->name('landlordRoomManagement');
    Route::match(['get', 'post'], '/booking-index/{landlord_id}', [bookingpageController::class, 'bookingpageIndex'])->name('booking.index');
    Route::match(['get', 'post'], '/reservation-index/{landlord_id}', [reservationController::class, 'reservationIndex'])->name('reservation.index');
    Route::match(['get', 'post'], '/all-tenants-index/{landlord_id}', [alltenantsController::class, 'alltenantIndex'])->name('all.tenants.index');
    Route::match(['get', 'post'], '/MessagingPage', [MessagingCenterController::class, 'MessagingPage'])->name('MessagingPage');
    Route::match(['get', 'post'], '/ReviewandFeedback', [ReviewandFeedbackController::class, 'ReviewandFeedback'])->name('ReviewandFeedback');
    Route::match(['get', 'post'], '/NotificationPage', [NotificationController::class, 'NotificationPage'])->name('NotificationPage');

    //functions for getting data dashboard 
    Route::get('/landlordDashboard/get/landlord/{landlord_id}', [dashboardController::class, 'getLandlord']);
    Route::get('/get/total-tenants/{landlord_id}', [dashboardController::class, 'getTotalTenants']);
    Route::get('/get/available-beds/{landlord_id}', [dashboardController::class, 'availableBeds']);
    Route::get('/get/reservation-list/{landlord_id}', [dashboardController::class, 'getReservationList']);
    Route::get('/get/booking-list/{landlord_id}', [dashboardController::class, 'getBookingList']);
    Route::get('/get/dorm-profits/{landlord_id}', [dashboardController::class, 'getDormProfits']);
    Route::get('/get/booking-profits/{landlord_id}', [dashboardController::class, 'getBookingProfits']);
    Route::get('/generate-full-report/{landlordID}', [dashboardController::class, 'generateFullReport']);



    //functions for landlord dorm management
    Route::post('/AddDorm', [dormpageController::class, 'AddDorm'])->name('AddDorm');
    Route::post('/UpdateDorm/{id}', [dormpageController::class, 'UpdateDorm']);
    Route::delete('/DeleteDorm/{id}', [dormpageController::class, 'DeleteDorm'])->name('DeleteDorm');
    Route::get('/ListDorms', [dormpageController::class, 'ListDorms'])->name('ListDorms');
    Route::get('/view-dorm/{id}', [dormpageController::class, 'ViewDorm']);
    Route::get('/SearchDorms', [dormpageController::class, 'searchDorms'])->name('SearchDorms');
    Route::get('/filter-locations', [dormpageController::class, 'filterLocations'])->name('filter.locations');
    
    Route::get('/filter-availability', [dormpageController::class, 'filteredAvailability'])->name('filter.availability');

    //functions for landlord dorm rules and policy
    Route::post('/add-rules', [dormpageController::class, 'addRulesAndPolicy'])->name('add.rules');
    Route::delete('/delete-rules/{pivotId}', [dormpageController::class, 'deleteRulesAndPolicies'])->name('delete.rules');

     //functions for amenities
     Route::post('/add-amenities', [dormpageController::class, 'AddAmenities'])->name('add.amenities');
     Route::delete('/delete-amenities/{id}', [dormpageController::class, 'DeleteAmenities'])->name('delete.amenities');
    //functions for landlord room management
    Route::post('/addRoom', [roompageController::class, 'addRoom'])->name('addRoom');
    Route::post('/add-roomfeatures', [roompageController::class, 'addRoomFeatures'])->name('add.roomfeatures');
    Route::delete('/delete-roomfeatures/{pivotId}', [roompageController::class, 'deleteRoomFeatures'])->name('delete.roomfeatures');

    Route::post('/update-room/{id}', [roompageController::class, 'UpdateRoom'])->name('update.room');
    // Route::post('/upload-images',[imagesDormImages::class,'roomImages'])->name('upload-images');
    Route::delete('/DeleteRoom/{id}', [roompageController::class, 'DeleteRoom'])->name('DeleteRoom');
    Route::get('/ListRooms', [roompageController::class, 'ListRooms'])->name('ListRooms');
    Route::get('/get-rooms-by-dorm/{dormId}', [roompageController::class, 'getRoomsByDorm'])->name('get.rooms.by.dorm');
    Route::get('/get-rooms-by-gender/{gender}', [roompageController::class, 'getRoomsByGender'])->name('get.rooms.by.gender');
    Route::get('/get-rooms-by-availability/{availability}', [roompageController::class, 'getRoomsByAvailability'])->name('get.rooms.by.availability');
    Route::get('/get-rooms-by-room-type', [roompageController::class, 'getRoomsByRoomType'])->name('get.rooms.by.room.type');
    Route::get('/ViewRoom/{id}', [roompageController::class, 'ViewRoom'])->name('ViewRoom');
    Route::get('/SearchRooms', [roompageController::class, 'searchRooms'])->name('SearchRooms');
    //functions for booking approval
    Route::get('/booking-list', [bookingpageController::class, 'bookingList']);
    Route::get('/booking-tenant-view/{id}', [bookingpageController::class, 'ViewTenant']);
    Route::post('/approve-tenant',[bookingpageController::class,'approveTenant']);
    Route::post('/not-approve-tenant',[bookingpageController::class,'notapproveTenant']);
    Route::delete('/delete-booking/{id}',[bookingpageController::class,'deleteBooking']);
    Route::get('/search-booking', [bookingpageController::class, 'searchBooking']);
    Route::get('/api/dorms', [bookingpageController::class, 'getDormName']);
    Route::get('/api/dorms/{dormId}/tenants', [bookingpageController::class, 'getTenantsByDorm']);
    Route::get('/api/roomnumber/booking', [bookingpageController::class, 'getrooms']);
    Route::get('/api/applications/booking/', [bookingpageController::class, 'getapplications']);
    //functions for reservation 
    Route::get('/reservation-list', [reservationController::class, 'reservationList']);
    Route::get('/view-reservation/reservation/{id}', [reservationController::class, 'viewReservation']);
    Route::get('/search-reservation', [reservationController::class, 'searchReservation']);
    Route::delete('/delete-reservation/reservation/{id}', [reservationController::class, 'reservationDelete']);
    Route::get('/api/dorms/{dormId}/reservation', [reservationController::class, 'getTenantsByDorm']);
    Route::get('/api/roomnumber/reservation', [reservationController::class, 'getRoomsNumber']);
    Route::get('/get/allroomNumbers', [reservationController::class, 'getAllReservations']);
    Route::post('/accept-reservation', [reservationController::class, 'acceptReservation']);
    Route::post('/eject-reservation', [reservationController::class, 'EjectReservation']);
    Route::get('/api/applications/reservation', [reservationController::class, 'getapplications']);
    //functions for all tenants
    Route::get('/tenants-list', [alltenantsController::class, 'tenantsList']);
    Route::get('/tenants-view/{id}', [alltenantsController::class, 'ViewTenant']);
});
//tenant auth
//tenant account process
Route::middleware([TenantAuth::class])->group(function () {
    Route::get('/homepage/{tenant_id}', [homepageController::class, 'homepage'])->name('homepage');
    Route::get('/tenant/dorms/lapu-lapu', [homepageController::class, 'dormLapuLapu']);
    Route::get('/tenant/dorms/mandaue', [homepageController::class, 'dormMandaeu']);  
    Route::get('/room-details/{dormitory_id}/{tenant_id}', [dormdetailscontroller::class, 'roomDetails'])->name('room.details');
    Route::get('/room-selection/{dormitoryID}/{tenantID}', [selectionRoomController::class, 'SelectionRoom'])->name('room.selection');
    Route::get('/available-room/{dormitoryID}', [selectionRoomController::class, 'availableRooms'])->name('available.room');
    Route::get('/occupied-room/{dormitoryID}', [selectionRoomController::class, 'occupiedRooms'])->name('occupied.room');
    Route::get('/under-maintenace-room/{dormitoryID}', [selectionRoomController::class, 'maintenanceRooms'])->name('under.maintenace.room');
    Route::get('/filter-price-range/{dormitoryID}', [selectionRoomController::class, 'selectedPriceRange']);
    Route::get('/filter-gender/{dormitoryID}', [selectionRoomController::class, 'filterGender']);
    Route::post('/reserved-room', [selectionRoomController::class, 'reservation'])->name('reserved.room');
    Route::get('/view-room-details/{id}', [selectionRoomController::class, 'ViewRoomDetails'])->name('view.room.details');
    Route::get('/booking-process/{roomId}/{tenantID}', [bookroomController::class, 'bookRoomPage'])->name('room.selection');
    Route::get('/get-room-details/{roomID}', [bookroomController::class, 'getRoom'])->name('get.room.details');
    Route::post('/book-room',[bookroomController::class,'bookingaRoom']);
    Route::post('/tenant-information', [dormdetailscontroller::class, 'tenantInformation'])->name('tenant.information');
    Route::post('/tenant-idPicture', [dormdetailscontroller::class, 'uploadTenantId'])->name('tenant.idPicture');

    Route::get('/dorm-details', [dormdetailscontroller::class, 'ViewDorms'])->name('dorm.details');
    //map dormitories page
    Route::get('/dorm-map{tenant_id}', [dormitoriesmapcontroller::class, 'dormitoriesMap'])->name('dorm.map');
    Route::get('/nearby-dorms', [dormitoriesmapcontroller::class, 'getNearbyByCoordinates']);
    Route::post('/price-range', [dormitoriesmapcontroller::class, 'priceRange']);
    Route::post('/selected-gender-type', [dormitoriesmapcontroller::class, 'SelectedGenderType']);
    Route::post('/filter-price-gender', [dormitoriesmapcontroller::class, 'filterPriceGender']);


    // dormitories page
    Route::get('/dormitories{tenant_id}', [dormitories::class, 'dormitoriesListing'])->name('dormitories');
    Route::get('/list-dorms', [dormitories::class, 'Listdorms']);
    Route::post('/search-locations', [dormitories::class, 'searchLocations']);
    Route::post('/pricerecommendations', [dormitories::class, 'priceRecommendations']);
    Route::post('/gender-recommendations', [dormitories::class, 'genderRecommendations']);
    Route::post('/api/search', [dormitories::class, 'searchWithPrice']);
    Route::post('/recommendations/gender-location', [dormitories::class, 'genderLocationRecommendations']);
    Route::get('/dormitories/filter', [dormitories::class, 'filtergenderpriceDormitories']);
    Route::post('/filterpricegender-dormitories', [dormitories::class, 'filterpriceGenderDormitories']);

    //tenant message
   // In your routes
    Route::get('/tenant-message-nav/{tenant_id}', [tenantmessageController::class, 'tenantmessageIndex'])->name('tenant.message');
    Route::get('/tenant-message/{tenant_id}', [tenantmessageController::class, 'landlordtenantmessageIndex'])->name('tenant.landlord.message');
    Route::post('/tenant/send-message', [tenantmessageController::class, 'sendMessage'])->name('tenant.send.message');
    
    Route::get('/tenant-conversation/{tenant_id}', [tenantmessageController::class, 'displayConversation']);

    Route::get('/get-messages/{tenant_id}/{receiver_id}', [tenantmessageController::class, 'getMessages']);
    Route::get('/convo-history', [tenantmessageController::class, 'getLandlordInformation']);

});





