<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\landingPageController;
use App\Http\Middleware\AfterRegistrationMiddleware;
use App\Http\Middleware\TenantAuth;


use App\Http\Controllers\tenant\accountprocessController;
use App\Http\Controllers\tenant\auth\homepageController;
use App\Http\Controllers\tenant\auth\roomdetailsController;

use App\Http\Controllers\landloard\accountprocesslandlordController;
use App\Http\Controllers\landloard\auth\dashboard;
use App\Http\Controllers\landloard\auth\dormManagementController;
use App\Http\Controllers\landloard\auth\roomManagementController;
use App\Http\Controllers\landloard\auth\TenantController;
use App\Http\Controllers\landloard\auth\tenantScreeningController;
use App\Http\Controllers\landloard\auth\AnalyticsController;
use App\Http\Controllers\landloard\auth\MessagingCenterController;
use App\Http\Controllers\landloard\auth\NotificationController;

use App\Http\Controllers\landloard\auth\ReviewandFeedbackController;
use App\Http\Controllers\landloard\auth\BookingController;
use App\Http\Controllers\tenant\auth\dormitoriesmapcontroller;
use App\Http\Controllers\tenant\auth\dormitories;


use App\Models\Landlord;





use App\Http\Middleware\LandlordAuth;


//view landingpage, login and register for landlord and tenant
Route::get('/', [landingPageController::class, 'landingPage'])->name('landingpage');
Route::get('/tenantLogin', [accountprocessController::class, 'login'])->name('login-tenant');
Route::get('/tenantRegister', [accountprocessController::class, 'register'])->name('register-tenant');
Route::get('/landlordLogin', [accountprocesslandlordController::class, 'landlordLogin'])->name('landlord-Login');
Route::post('/tenant-logout', [accountprocesslandlordController::class, 'logout'])->name('tenant.logout');
Route::post('/logout', [accountprocessController::class, 'logout'])->name('logout');


Route::get('/landlordregister', [accountprocesslandlordController::class, 'landlordRegister'])->name('register-landlord');
//Route::get('/verify', [accountprocessController::class, 'startVerification'])->name('verify-otp');

Route::post('/verify-registration', [accountprocessController::class, 'verifyRegistration'])->name('verify.registration');


//creating tenants account

Route::match(['get', 'post'], '/SendOtp', [accountprocessController::class, 'SendOtp'])->name('SendOtp');
Route::match(['get', 'post'], '/resendOtp', [accountprocessController::class, 'resendOtp'])->name('resendOtp');
Route::match(['get', 'post'], '/registerTenant', [accountprocessController::class, 'registerTenant'])->name('registerTenant');
Route::match(['get', 'post'], 'tenant-login',[accountprocessController::class,'loginTenant'])->name('tenant-login');


//landlord login
Route::match(['get', 'post'], '/loginLandlord', [accountprocesslandlordController::class, 'loginLandlord'])->name('loginLandlord');



//landlord

Route::match(['get', 'post'], '/personalDetails', [accountprocesslandlordController::class, 'personalDetails'])->name('stepOne');
Route::post('/IdentityVerifaction', [accountprocesslandlordController::class, 'IdentityVerifaction'])->name('IdentityVerifaction');
Route::post('/businessPermitValidation', [accountprocesslandlordController::class, 'businessPermitValidation'])->name('businessPermitValidation');
Route::post('/RegisterLandlord', [accountprocesslandlordController::class, 'RegisterLandlord'])->name('RegisterLandlord');

//landlord auth

Route::middleware([LandlordAuth::class])->group(function () {
    Route::match(['get', 'post'], '/landlordDashboard', [dashboard::class, 'landlordDashboard'])->name('landlordDashboard');
    Route::match(['get', 'post'], '/landlordDormManagement/{landlordId}', [dormManagementController::class, 'DormManagement'])->name('landlordDormManagement');
    Route::match(['get', 'post'], '/input-text', [dormManagementController::class, 'inputFieldDorm'])->name('input-text');
    Route::match(['get', 'post'], '/upload-main-image', [dormManagementController::class, 'uploadmainImage'])->name('upload-main-image');
    Route::match(['get', 'post'], '/upload-secondary-image', [dormManagementController::class, 'uploadsecondaryImage'])->name('upload-secondary-image');
    Route::match(['get', 'post'], '/uplad-third-image', [dormManagementController::class, 'uploadthirdImage'])->name('upload-third-image');
    Route::match(['get', 'post'], '/edit-main-image', [dormManagementController::class, 'editmainImage'])->name('edit-main-image');
    Route::match(['get', 'post'], '/edit-secondary-image', [dormManagementController::class, 'editsecondaryImage'])->name('edit-secondary-image');
    Route::match(['get', 'post'], '/edit-images/{id}', [dormManagementController::class, 'imageUpdated'])->name('edit-images');
    Route::match(['get', 'post'], '/landlordRoomManagement/{landlordId}', [roomManagementController::class, 'RoomManagement'])->name('landlordRoomManagement');
    Route::match(['get', 'post'], '/tenant', [TenantController::class, 'tenant'])->name('tenant');
    Route::match(['get', 'post'], '/tenantScreening{landlordId}', [tenantScreeningController::class, 'tenantScreening'])->name('tenantScreening');
    Route::match(['get', 'post'], '/BookingPage', [BookingController::class, 'BookingPage'])->name('BookingPage');
    Route::match(['get', 'post'], '/AnalyticsPage', [AnalyticsController::class, 'AnalyticsPage'])->name('AnalyticsPage');
    Route::match(['get', 'post'], '/MessagingPage', [MessagingCenterController::class, 'MessagingPage'])->name('MessagingPage');
    Route::match(['get', 'post'], '/ReviewandFeedback', [ReviewandFeedbackController::class, 'ReviewandFeedback'])->name('ReviewandFeedback');
    Route::match(['get', 'post'], '/NotificationPage', [NotificationController::class, 'NotificationPage'])->name('NotificationPage');

    //functions for landlord dorm management
    Route::post('/AddDorm', [dormManagementController::class, 'AddDorm'])->name('AddDorm');
    Route::post('/UpdateDorm/{id}', [dormManagementController::class, 'UpdateDorm'])->name('UpdateDorm');
    Route::delete('/DeleteDorm/{id}', [dormManagementController::class, 'DeleteDorm'])->name('DeleteDorm');
    Route::get('/ListDorms', [dormManagementController::class, 'ListDorms'])->name('ListDorms');
    Route::get('/ViewDorm/{id}', [dormManagementController::class, 'ViewDorm'])->name('ViewDorm');
    Route::get('/SearchDorms', [dormManagementController::class, 'searchDorms'])->name('SearchDorms');
    Route::get('/filter-locations', [dormManagementController::class, 'filterLocations'])->name('filter.locations');
    
    Route::get('/filter-availability', [dormManagementController::class, 'filteredAvailability'])->name('filter.availability');

    //functions for landlord dorm rules and policy
    Route::post('/add-rules', [dormManagementController::class, 'addRulesAndPolicy'])->name('add.rules');
    Route::delete('/delete-rules/{pivotId}', [dormManagementController::class, 'deleteRulesAndPolicies'])->name('delete.rules');

     //functions for amenities
     Route::post('/add-amenities', [dormManagementController::class, 'AddAmenities'])->name('add.amenities');
     Route::delete('/delete-amenities/{id}', [dormManagementController::class, 'DeleteAmenities'])->name('delete.amenities');
    //functions for landlord room management
    Route::post('/addRoom', [roomManagementController::class, 'addRoom'])->name('addRoom');
    Route::post('/add-roomfeatures', [roomManagementController::class, 'addRoomFeatures'])->name('add.roomfeatures');
    Route::delete('/delete-roomfeatures/{pivotId}', [roomManagementController::class, 'deleteRoomFeatures'])->name('delete.roomfeatures');

    Route::post('/update-room/{id}', [roomManagementController::class, 'UpdateRoom'])->name('update.room');
    Route::post('/upload-images',[imagesDormImages::class,'roomImages'])->name('upload-images');
    Route::delete('/DeleteRoom/{id}', [roomManagementController::class, 'DeleteRoom'])->name('DeleteRoom');
    Route::get('/ListRooms', [roomManagementController::class, 'ListRooms'])->name('ListRooms');
    Route::get('/get-rooms-by-dorm/{dormId}', [roomManagementController::class, 'getRoomsByDorm'])->name('get.rooms.by.dorm');
    Route::get('/get-rooms-by-gender/{gender}', [roomManagementController::class, 'getRoomsByGender'])->name('get.rooms.by.gender');
    Route::get('/get-rooms-by-availability/{availability}', [roomManagementController::class, 'getRoomsByAvailability'])->name('get.rooms.by.availability');
    Route::get('/get-rooms-by-room-type', [roomManagementController::class, 'getRoomsByRoomType'])->name('get.rooms.by.room.type');
    Route::get('/ViewRoom/{id}', [roomManagementController::class, 'ViewRoom'])->name('ViewRoom');
    Route::get('/SearchRooms', [roomManagementController::class, 'searchRooms'])->name('SearchRooms');
    //functions for tenant screening
    
    Route::get('/tenant-screening-list', [tenantScreeningController::class, 'tenantScreeningList'])->name('tenant.screening.list');
    Route::get('/view-tenant/{id}', [tenantScreeningController::class, 'ViewTenant'])->name('view.tenant');
    Route::post('/approve-tenant', [tenantScreeningController::class, 'approveTenant'])->name('approve.tenant');
    Route::post('/not-approve-tenant', [tenantScreeningController::class, 'notapproveTenant'])->name('not_approve.tenant');
    Route::delete('/delete-screening/{id}', [tenantScreeningController::class, 'DeleteScreening'])->name('delete.screening');

    
});
//tenant auth
//tenant account process
Route::middleware([TenantAuth::class])->group(function () {
    Route::get('/homepage/{tenant_id}', [homepageController::class, 'homepage'])->name('homepage');
    Route::get('/tenant/dorms/lapu-lapu', [homepageController::class, 'dormLapuLapu']);
    Route::get('/tenant/dorms/mandaue', [homepageController::class, 'dormMandaeu']);
    // Route::get('/list-dorms', [homepageController::class, 'Listdorms'])->name('list.dorms');
  
    Route::get('/room-details/{dormitory_id}/{tenant_id}', [roomdetailsController::class, 'roomDetails'])->name('room.details');
    Route::post('/tenant-information', [roomdetailsController::class, 'tenantInformation'])->name('tenant.information');
    Route::post('/tenant-idPicture', [roomdetailsController::class, 'uploadTenantId'])->name('tenant.idPicture');

    Route::get('/dorm-details', [roomdetailsController::class, 'ViewDorms'])->name('dorm.details');
    Route::post('/book-room', [roomdetailsController::class, 'bookaroom'])->name('book.room');
    Route::get('/view-room-details/{id}', [roomdetailsController::class, 'ViewRoomDetails'])->name('view.room.details');
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

    

});





