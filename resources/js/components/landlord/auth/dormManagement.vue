<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />

    <div class="container mt-5">

        <div class="d-flex justify-content-end align-items-center mb-4">
            <div class="d-flex gap-2 flex-wrap">
                <!-- View Rooms Button -->
                <button class="btn btn-outline-secondary shadow-sm px-4 rounded-pill d-flex align-items-center gap-2"
                    @click="ViewRoomsPage">
                    <i class="bi bi-door-open-fill"></i>
                    View Rooms
                </button>

                <!-- Add Dorm Button -->
                <button class="btn btn-outline-primary shadow-sm px-4 rounded-pill d-flex align-items-center gap-2"
                    @click="VisibleAddModal = true" :disabled="!isVerified" :title="isVerified
                        ? 'Add Dormitory'
                        : 'Your account is not verified. Please verify your account to add dormitories.'">
                    <i class="bi bi-plus-circle"></i>
                    Add Dorm
                </button>


            </div>

        </div>
        <div v-if="!isVerified"
            class="alert alert-warning border-0 shadow-sm rounded-3 p-4 mb-4 d-flex align-items-center gap-3">
            <i class="bi bi-shield-lock-fill fs-3 text-warning"></i>
            <div>
                <h6 class="fw-bold text-dark mb-1">Account Not Verified</h6>
                <p class="mb-2 small text-muted">
                    Please verify your account to unlock all features including adding dormitories.
                    Verification ensures security and trust within the platform.
                </p>
                <button class="btn btn-warning btn-sm rounded-pill shadow-sm px-3" @click="goToVerificationPage">
                    🔒 Verify Now
                </button>
            </div>
        </div>


        <div class="input-group mb-4 w-100 shadow-sm rounded-pill overflow-hidden" style="border:2px solid #4edce2;">
            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none" placeholder="Search Dormitories name"
                aria-label="Search Locations" v-model="searchTerm" />
        </div>
        <div class="mb-1 d-flex gap-2 flex-wrap justify-content-start">
            <div class="col-md-6 col-lg-4 mb-2">
                <select class="form-select shadow-sm rounded-4" v-model="selectedLocation" @change="dropdownLocation"
                    style="border:2px solid #4edce2;">

                    <option disabled value="">Select Locations</option>
                    <option value="all">All Locations</option>
                    <option value="mandaue">Mandaue City</option>
                    <option value="lapu-lapu">Lapu-Lapu</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4 mb-2">
                <select class="form-select shadow-sm rounded-4" v-model="selectedAvailability"
                    @change="dropdownAvailability" style="border:2px solid #4edce2;">

                    <option disabled value="">Select Availability</option>
                    <option value="all">All Availability</option>
                    <option value="Available">Available</option>
                    <option value="Not Available">Not Available</option>
                </select>
            </div>
        </div>
        <!-- Search Bar -->

        <div v-if="dorms.length === 0" class="text-center text-muted">
            No dormitories found matching your search criteria.
        </div>
        <!-- Table -->
        <div class="container bg-white rounded shadow-sm p-3"
            style="border: 1px solid #4edce2; border-radius: 0.5rem; max-height: 700px; overflow-y: auto;">

            <table class="table bg-info table-bordered table-hover mb-0"
                style="border-collapse: separate; border-spacing: 0;">
                <!-- Table Header -->
                <thead class="bg-info text-white">
                    <tr class="text-center align-middle">
                        <th scope="col">#</th>
                        <th scope="col">Dormitory Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Contact Email</th>
                        <th scope="col">Contact Phone</th>
                        <th scope="col">Rooms</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>


                <!-- Table Body -->
                <tbody>
                    <tr v-for="(dorm, index) in dorms" :key="dorm.dormID" class="text-center align-middle bg-info">

                        <td class="fw-semibold">{{ dorm.dormID }}</td>
                        <td>{{ dorm.dormName }}</td>
                        <td class="text-truncate" style="max-width: 150px;">{{ dorm.address }}</td>
                        <td class="text-truncate" style="max-width: 150px;">{{ dorm.contactEmail }}</td>
                        <td>{{ dorm.contactPhone }}</td>
                        <td class="fw-bold">{{ dorm.totalRooms }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-success" @click="viewDorm(dorm.dormID)" title="View Dorm">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                <button class="btn btn-sm btn-primary" @click="editDorm(dorm.dormID)" title="Edit Dorm">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteDorm(dorm.dormID)"
                                    title="Delete Dorm">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <!-- Pagination with Bootstrap 5 -->
        <div v-if="lastPage > 1" class="d-flex justify-content-center align-items-center my-3">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    <li :class="['page-item', { disabled: currentPage === 1 }]">
                        <button class="page-link" :disabled="currentPage === 1"
                            @click="handlePagination(currentPage - 1)" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Prev</span>
                        </button>
                    </li>

                    <li class="page-item disabled">
                        <span class="page-link">
                            Page {{ currentPage }} of {{ lastPage }}
                        </span>
                    </li>

                    <li :class="['page-item', { disabled: currentPage === lastPage }]">
                        <button class="page-link" :disabled="currentPage === lastPage"
                            @click="handlePagination(currentPage + 1)" aria-label="Next">
                            <span aria-hidden="true">Next &raquo;</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- Modal Add Dorm -->
        <div v-if="VisibleAddModal" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                    <!-- Modal Header -->
                    <div class="modal-header text-black py-3">
                        <h5 class="modal-title fw-bold">🏠 Add Dorm</h5>
                        <button type="button" class="btn-close btn-close-black" @click="CloseAddModal"></button>
                    </div>
                    <!-- Modal Body -->
                    <div class="modal-body bg-white">
                        <div class="p-3 d-flex align-items-center flex-wrap bg-light border rounded shadow-sm"
                            style="gap: 1rem;">
                            <img :src="getAssetPath('images/Logo/logo.png')" alt="Company Logo" width="50" class="me-3"
                                style="border-radius: 8px;" />

                            <span class="fw-bold fs-5 logo-text me-4" style="color: #2c3e50; user-select: none;">
                                DormHub
                            </span>

                            <div class="d-flex flex-column flex-grow-1" style="gap: 0.25rem;">
                                <small class="text-muted d-flex align-items-center" style="gap: 0.3rem;">
                                    <span>📍</span>
                                    Click <strong>Select address</strong> to locate your dormitory.
                                </small>

                                <small class="text-muted d-flex align-items-center" style="gap: 0.3rem;">
                                    <span>✅</span>
                                    Ensure all required fields are filled before submitting.
                                </small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Left Column -->
                            <div class="col">
                                <div class="form-floating mb-3 mt-3">
                                    <input type="text" class="form-control border-primary shadow-sm" id="dormName"
                                        v-model="dorm_name" placeholder="Dorm Name"
                                        style="height: 48px; font-size: 1rem;" />
                                    <label for="dormName" class="fw-semibold text-primary">Dorm Name</label>
                                </div>

                                <span class="mb-3 text-danger small d-flex align-items-center gap-1"
                                    v-if="errors.dorm_name">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ errors.dorm_name[0] }}
                                </span>
                                <div class="form-floating mt-3">
                                    <input type="text" class="form-control bg-light text-muted" id="address"
                                        v-model="address" placeholder="Address" readonly
                                        style="height: 48px; cursor: not-allowed;" />
                                    <label for="address" class="fw-semibold">Address</label>
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="button" class="btn btn-outline-primary fw-semibold"
                                        @click="VisibleMap = true" style="gap: 0.5rem;">
                                        📍 Select address
                                    </button>
                                </div>

                                <span class="mb-3 text-danger " v-if="errors.address">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ errors.address[0] }}
                                </span>
                                <div class="form-floating mb-3 mt-3">
                                    <textarea class="form-control border-primary shadow-sm text-muted" id="description"
                                        v-model="description" placeholder="Enter Description"
                                        style="height: 150px; font-size: 1rem;"></textarea>
                                    <label for="description" class="fw-semibold text-primary">Description</label>
                                </div>


                                <span class="mb-3 text-danger" v-if="errors.description">
                                    <i class="fa-solid fa-circle-exclamation"></i>{{ errors.description[0]
                                    }}</span>

                                <div>
                                    <label for="total_rooms">Number of Rooms</label>

                                    <div class="mb-3 mt-3 d-flex align-items-center gap-2">

                                        <button class="btn btn-outline-danger" @click="decreamnentRooms()"><span
                                                class="fw-bold">-</span></button>
                                        <input type="text" class="form-control w-100 text-center" id="total_rooms"
                                            placeholder="0" v-model="total_rooms" readonly>
                                        <button class="btn btn-outline-success" @click="increamentRooms()"><span
                                                class="fw-bold">+</span></button>
                                    </div>
                                </div>
                            </div>
                            <!-- Middle Column -->
                            <div class="col">
                                <div class="form-floating mb-3 mt-3">
                                    <input type="email" class="form-control border-primary shadow-sm" id="contact_email"
                                        placeholder="Contact Email" v-model="contact_email"
                                        style="height: 48px; font-size: 1rem;" />
                                    <label for="contact_email" class="fw-semibold text-primary">Contact Email</label>
                                </div>

                                <span class="mb-3 text-danger mt-3" v-if="errors.contact_email"> <i
                                        class="fa-solid fa-circle-exclamation"></i> {{
                                    errors.contact_email[0] }}</span>
                                <div class="form-floating mb-3 mt-3">
                                    <input type="tel" class="form-control border-primary shadow-sm" id="contact_phone"
                                        placeholder="Contact Phone" v-model="contact_phone"
                                        style="height: 48px; font-size: 1rem;" />
                                    <label for="contact_phone" class="fw-semibold text-primary">Contact Phone</label>
                                </div>

                                <span class="text-danger mb-3" v-if="errors.contact_phone"> <i
                                        class="fa-solid fa-circle-exclamation"></i> {{
                                    errors.contact_phone[0] }}</span>
                                <div class="form-floating mb-3 mt-3">
                                    <input type="tel" class="form-control border-primary shadow-sm" id="contact_phone"
                                        placeholder="Gcash Number" v-model="gcashNumber"
                                        style="height: 48px; font-size: 1rem;" />
                                    <label for="contact_phone" class="fw-semibold text-primary">Gcash Number</label>
                                </div>
                                <span class="text-danger mb-3" v-if="errors.gcashNumber"> <i
                                        class="fa-solid fa-circle-exclamation"></i> {{
                                    errors.gcashNumber[0] }}</span>

                                <div class="form-floating mb-3 mt-3">
                                    <input type="text" class="form-control border-primary shadow-sm" id="building_type"
                                        placeholder="Enter Building Type" v-model="building_type"
                                        style="height: 48px; font-size: 1rem;" />
                                    <label for="building_type" class="fw-semibold text-primary">Building Type</label>
                                </div>

                                <span class="text-danger mb-3" v-if="errors.building_type"> <i
                                        class="fa-solid fa-circle-exclamation"></i> {{
                                    errors.building_type[0] }}</span>
                                <div class="form-floating mb-3 mt-3">
                                    <select class="form-select border-primary shadow-sm" id="availability"
                                        v-model="availability" style="height: 48px; font-size: 1rem;">
                                        <option disabled value="">Select Availability</option>
                                        <option value="Available">Available</option>
                                        <option value="Not Available">Not Available</option>
                                    </select>
                                    <label for="availability" class="fw-semibold text-primary">Select
                                        Availability</label>
                                </div>

                                <span class="text-danger mb-3" v-if="errors.availability">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{
                                    errors.availability[0] }}</span>
                                <div class="form-floating mb-3 mt-3">
                                    <select class="form-select border-primary shadow-sm" id="occupancy_type"
                                        v-model="occupancy_type" style="height: 48px; font-size: 1rem;">
                                        <option disabled value="">Select Occupancy Type</option>
                                        <option value="Male only">Male only</option>
                                        <option value="Female only">Female only</option>
                                        <option value="Mixed (Male & Female – separate floors)">Mixed (Male & Female –
                                            separate floors)</option>
                                        <option value="Mixed (Unspecified)">Mixed (Unspecified)</option>
                                    </select>
                                    <label for="occupancy_type" class="fw-semibold text-primary">Occupancy Type</label>
                                </div>

                                <span class="text-danger mb-3" v-if="errors.occupancy_type"> <i
                                        class="fa-solid fa-circle-exclamation"></i> {{
                                    errors.occupancy_type[0] }}</span>


                                <div class="d-grid">
                                    <button type="submit" @click="DisplayModalImages"
                                        class="btn btn-outline-success btn-lg">
                                        Upload Dormitory Images
                                    </button>
                                </div>
                            </div>
                            <!-- Right Column -->
                        </div>
                    </div>
                    <!-- Modal Footer -->
                </div>
            </div>
            <Toastcomponents ref="toast" />
        </div>
        <!-- MAP MODAL -->
        <div v-if="VisibleMap" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);" @click.self="VisibleMap = false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content position-relative">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">Dorm Location</h5>
                        <button type="button" class="btn-close" @click="VisibleMap = false" aria-label="Close"></button>
                    </div>
                    <!-- Modal Body with Map -->
                    <div class="modal-body pt-3 position-relative">
                        <!-- 📍 Instruction -->
                        <div class="mb-2 text-muted" style="font-size: 14px;">
                            📍 Use this pin to locate your dormitory.
                        </div>
                        <!-- Map Container -->
                        <div id="AddMap" class="rounded" style="height: 400px; width: 100%;"></div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="VisibleMap = false">
                            Back to Inputs
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <!-- Amenities Modal -->
        <div class="modal fade show d-block w-100" v-if="amenitiesModal" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);" @click.self="amenitiesModal = false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header  text-dark rounded-top-4">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-plus text-primary"></i>
                            Add Amenities <span class="text-muted small">(Optional)</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-dark" @click="closeaminitiemodal"
                            aria-label="Close"></button>
                    </div>
                    <Toastcomponents ref="toast" />

                    <div class="modal-body">
                        <div v-for="(amenity, index) in amenities" :key="index" class="form-floating mb-3">
                            <input type="text" class="form-control mb-2" v-model="amenities[index]"
                                :id="'amenity' + index" placeholder="Enter amenity" />
                            <label :for="'amenity' + index">Amenity {{ index + 1 }}</label>
                            <span class="text-danger mb-3 " v-if="errors.amenities">{{ errors.amenities[0] }}</span>

                        </div>

                        <button class="btn btn-primary mb-4" @click="addAmenity" :disabled="amenities.length >= 4"
                            :title="amenities.length >= 4 ? 'Max 4 amenities allowed' : 'Add Amenity'">
                            <i class="fa-solid fa-plus"></i> Add Amenity
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade show d-block w-100" v-if="rulesandpoliciesModal" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);" @click.self="rulesandpoliciesModal = false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header  text-dark rounded-top-4">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-plus text-primary"></i>
                            Add Rules and Policy <span class="text-muted small">(Optional)</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-dark" @click="rulesandpoliciesModal = false"
                            aria-label="Close"></button>
                    </div>
                    <Toastcomponents ref="toast" />

                    <div class="modal-body">
                        <div v-for="(rule, index) in rules" :key="index" class="form-floating mb-3">
                            <input type="text" class="form-control mb-2" v-model="rules[index]" :id="'rule' + index"
                                placeholder="Enter rule or policy" />
                            <label :for="'rule' + index">Rule/Policy {{ index + 1 }}</label>
                            <span class="text-danger mb-3 " v-if="errors.rules">{{ errors.rules[0]
                                }}</span>
                        </div>
                        <button class="btn btn-primary mb-4" @click="addRulesAndpolicy"
                            :disabled="addRulesAndpolicy.length >= 4"
                            :title="addRulesAndpolicy.length >= 4 ? 'Max 4 rules/policies allowed' : 'Add Rule/Policy'">
                            <i class="fa-solid fa-plus"></i> Add Rule/Policy
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Update Modal -->
        <div v-if="VisibleUpdateModal" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                    <!-- Modal Header -->
                    <div class="modal-header text-dark py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                            <i class="fa-solid fa-pen-to-square text-primary"></i>
                            Update Dorm
                        </h5>
                        <button type="button" class="btn-close btn-close-dark" @click="VisibleUpdateModal = false"
                            aria-label="Close"></button>
                    </div>

                    <Toastcomponents ref="toast" />

                    <!-- Modal Body -->
                    <form @submit.prevent="updateDorm">
                        <div class="modal-body bg-white">
                            <div
                                class="p-3 d-flex align-items-center flex-wrap gap-3 shadow-sm rounded-4 bg-light border mb-3">
                                <!-- Logo + Name -->
                                <div class="d-flex align-items-center me-4">
                                    <img :src="getAssetPath('images/Logo/logo.png')" alt="Company Logo" width="50"
                                        class="me-2 rounded-circle border">
                                    <span class="fw-bold fs-5 logo-text text-primary">DormHub</span>
                                </div>

                                <!-- Instructions -->
                                <div class="d-flex flex-column small text-muted">
                                    <div class="mb-1">
                                        📍 Click <strong class="text-dark">Select address</strong> to locate your
                                        dormitory.
                                    </div>
                                    <div>
                                        ✅ Ensure all required fields are filled before submitting.
                                    </div>
                                </div>
                            </div>


                            <div class="row g-3">
                                <!-- Left Column -->
                                <div class="col-md-4">
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control  rounded-4 border-primary shadow-sm"
                                            id="dormName" v-model="editDormData.dormName" placeholder="Dorm Name">
                                        <label for="dormName" class="text-primary">Dorm Name</label>
                                        <span class="text-danger small d-flex align-items-center mt-1"
                                            v-if="errors.editDormData?.dormName">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            {{ errors.editDormData.dormName[0] }}
                                        </span>
                                    </div>

                                    <div class="form-floating mb-2 position-relative">
                                        <input type="text" class="form-control  rounded-4 border-primary shadow-sm"
                                            id="address" v-model="editDormData.address" placeholder="Address" readonly>
                                        <label for="address" class="text-primary">Address</label>

                                        <!-- Error message with icon -->
                                        <span class="text-danger small d-flex align-items-center mt-1"
                                            v-if="errors.editDormData?.address">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            {{ errors.editDormData.address[0] }}
                                        </span>
                                    </div>
                                    <button type="button" class="btn mb-2 btn-outline-primary w-100"
                                        @click="UpdateVisibleMap = true">
                                        📍 Select Address
                                    </button>

                                    <div class="form-floating mb-2 position-relative">
                                        <!-- Description Icon -->


                                        <textarea class="form-control  rounded-4 border-primary shadow-sm"
                                            id="description" v-model="editDormData.description"
                                            placeholder="Description" style="height: 120px;"></textarea>
                                        <label for="description" class="text-primary">Description</label>

                                        <!-- Error Message with Icon -->
                                        <span class="text-danger small d-flex align-items-center mt-1"
                                            v-if="errors.editDormData?.description">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            {{ errors.editDormData.description[0] }}
                                        </span>
                                    </div>

                                    <!-- Optional Amenities Field -->
                                    <div class="form-floating mb-2 position-relative">
                                        <!-- Amenities Icon -->
                                        <input type="text" class="form-control rounded-4 border-secondary shadow-sm"
                                            id="Optional" v-model="editDormData.newAmenities"
                                            placeholder="Optional Add Amenities">
                                        <label for="Optional" class="text-secondary">(Optional) Add Amenities</label>
                                    </div>
                                    <span class="text-danger small" v-if="errors.editDormData?.newAmenities">
                                        {{ errors.editDormData.newAmenities[0] }}
                                    </span>

                                    <div class="mb-2">
                                        <button type="button" class="btn btn-outline-secondary w-100"
                                            @click="addnewAmenity()">
                                            <i class="fas fa-plus me-2"></i> Add Amenity
                                        </button>


                                    </div>

                                    <div class="border rounded-4 p-2 shadow-sm bg-light">
                                        <!-- Table Header -->
                                        <div class="row fw-bold border-bottom py-2 text-center text-primary">
                                            <div class="col">
                                                <i class="fa-solid fa-list me-1"></i> Amenity
                                            </div>
                                            <div class="col-3">
                                                <i class="fa-solid fa-gears me-1"></i> Actions
                                            </div>
                                        </div>

                                        <!-- Amenities List -->
                                        <div v-for="amenity in editDormData.amenities"
                                            :key="amenity.pivot ? amenity.pivot.id : amenity.id"
                                            class="row align-items-center py-2 border-bottom text-center bg-white rounded-3 my-1 shadow-sm">

                                            <!-- Amenity Name -->
                                            <div class="col">
                                                <input type="text" readonly
                                                    class="form-control text-center border-0 bg-transparent fw-semibold text-secondary"
                                                    v-model="amenity.aminityName" placeholder="Amenity name" />
                                            </div>

                                            <!-- Actions -->
                                            <div class="col-3 d-flex justify-content-center gap-2">
                                                <button
                                                    class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                                                    @click.prevent="deleteAmenity(amenity.pivot.id)">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Middle Column -->
                                <div class="col-md-4">
                                    <div class="form-floating mb-2 position-relative">
                                        <!-- Email Icon -->


                                        <input type="email" class="form-control rounded-4 border-primary shadow-sm"
                                            id="contact_email" v-model="editDormData.contactEmail"
                                            placeholder="Contact Email">
                                        <label for="contact_email" class="text-primary">Contact Email</label>

                                        <!-- Error message with icon -->
                                        <span class="text-danger small d-flex align-items-center mt-1"
                                            v-if="errors.editDormData?.contactEmail">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            {{ errors.editDormData.contactEmail[0] }}
                                        </span>
                                    </div>

                                    <!-- Contact Phone -->
                                    <div class="form-floating mb-2 position-relative">
                                        <!-- Phone Icon -->
                                        <input type="tel" class="form-control  rounded-4 border-primary shadow-sm"
                                            id="contact_phone" v-model="editDormData.contactPhone"
                                            placeholder="Contact Phone">
                                        <label for="contact_phone" class="text-primary">Contact Phone</label>

                                        <!-- Error message with icon -->
                                        <span class="text-danger small d-flex align-items-center mt-1"
                                            v-if="errors.editDormData?.contactPhone">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            {{ errors.editDormData.contactPhone[0] }}
                                        </span>

                                    </div>
                                    <div class="form-floating mb-2 position-relative">
                                        <!-- Phone Icon -->
                                        <input type="tel" class="form-control  rounded-4 border-primary shadow-sm"
                                            id="gcash number" v-model="editDormData.gcashNumber"
                                            placeholder="Gcash number">
                                        <label for="gcash number" class="text-primary">Gcash Number</label>

                                        <!-- Error message with icon -->
                                        <span class="text-danger small d-flex align-items-center mt-1"
                                            v-if="errors.editDormData?.gcashNumber">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            {{ errors.editDormData.gcashNumber[0] }}
                                        </span>

                                    </div>

                                    <div class="form-floating mb-2 position-relative">
                                        <!-- Building Icon -->

                                        <input type="text" class="form-control  rounded-4 border-primary shadow-sm"
                                            id="building_type" placeholder="Enter Building Type"
                                            v-model="editDormData.buildingType">
                                        <label for="building_type" class="text-primary">Building Type</label>

                                        <!-- Error message with icon -->
                                        <span class="text-danger small d-flex align-items-center mt-1"
                                            v-if="errors.editDormData?.buildingType">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            {{ errors.editDormData.buildingType[0] }}
                                        </span>
                                    </div>
                                    <div class="form-floating mb-2 position-relative">
                                        <!-- Availability Icon -->


                                        <select class="form-select  rounded-4 border-primary shadow-sm"
                                            id="availability" v-model="editDormData.availability">
                                            <option disabled value="">Select Availability</option>
                                            <option value="Available">Available</option>
                                            <option value="Not Available">Not Available</option>
                                        </select>
                                        <label for="availability" class="text-primary">Select Availability</label>

                                        <!-- Error Message with Icon -->
                                        <span class="text-danger small d-flex align-items-center mt-1"
                                            v-if="errors.editDormData?.availability">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i>
                                            {{ errors.editDormData.availability[0] }}
                                        </span>
                                    </div>

                                    <!-- Occupancy Type Field -->
                                    <div class="form-floating mb-2 position-relative">
                                        <select class="form-select  rounded-4 border-primary shadow-sm"
                                            id="occupancy_type" v-model="editDormData.occupancyType">
                                            <option disabled value="">Select Occupancy Type</option>
                                            <option value="Male only">Male only</option>
                                            <option value="Female only">Female only</option>
                                            <option value="Mixed (Male & Female – separate floors)">Mixed (Male & Female
                                                – separate floors)</option>
                                            <option value="Mixed (Unspecified)">Mixed (Unspecified)</option>
                                        </select>
                                        <label for="occupancy_type" class="text-primary">Occupancy Type</label>
                                    </div>
                                    <span class="text-danger mb-3" v-if="errors.editDormData?.occupancyType">{{
                                        errors.editDormData.occupancyType[0] }}</span>
                                    <div class="mt-3 mb-3">
                                        <label class="fw-bold mb-2 text-primary text-center">
                                            <i class="fa-solid fa-door-closed me-1"></i> Number of Rooms
                                        </label>

                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <!-- Decrease Button -->
                                            <button type="button"
                                                class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;" @click="updatedecreamnentRooms()">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>

                                            <!-- Room Count Input -->
                                            <input type="text"
                                                class="form-control text-center fw-bold rounded-4 border-primary shadow-sm"
                                                id="total_rooms" placeholder="0" v-model="editDormData.totalRooms"
                                                readonly style="max-width: 300px;">

                                            <!-- Increase Button -->
                                            <button type="button"
                                                class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;" @click="updateincreamentRooms()">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <button type="button" @click="updateImages()"
                                            class="btn btn-outline-dark w-100">
                                            <i class="bi bi-image me-2"></i> Update Images
                                        </button>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">

                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="fas fa-edit me-2"></i> Update Dorm
                                        </button>
                                    </div>
                                </div>
                                <!-- Right Column -->
                                <div class="col-md-4">
                                    <div class="form-floating mb-2 position-relative">
                                        <input type="text" class="form-control rounded-4 border-secondary shadow-sm"
                                            id="Optional" v-model="newrules"
                                            placeholder="Optional Add Rules and Policies">
                                        <label for="Optional" class="text-secondary">(Optional) Add Rules and
                                            Policies</label>
                                    </div>
                                    <button class="btn btn-outline-primary w-100" type="button" @click="addNewRule()"><i
                                            class="fas fa-plus me-2"></i>Add Rule</button>
                                    <span class="text-danger small d-flex align-items-center mt-1"
                                        v-if="errors.newrules">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                                        {{ errors.newrules[0] }}
                                    </span>
                                    <div class="border rounded-4 p-3 shadow-sm bg-light">
                                        <!-- Header -->
                                        <div class="row fw-bold border-bottom py-2 text-center bg-white rounded-top">
                                            <div class="col">
                                                <i class="fa-solid fa-scroll me-2"></i>Rules and Policies
                                            </div>
                                            <div class="col-3">
                                                Actions
                                            </div>
                                        </div>

                                        <!-- List -->
                                        <div v-for="rule in editDormData.rules_and_policy"
                                            :key="rule.pivot ? rule.pivot.id : rule.id"
                                            class="row align-items-center py-2 border-bottom text-center bg-white rounded mb-2 shadow-sm">
                                            <div class="col">
                                                <input type="text" readonly
                                                    class="form-control text-center bg-light border-0 fw-semibold text-secondary"
                                                    v-model="rule.rulesName" placeholder="Rule name" />
                                            </div>
                                            <div class="col-3 d-flex justify-content-center gap-2">
                                                <button
                                                    class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                                                    type="button"
                                                    @click.prevent="deleteRulesAndPolicies(rule.pivot.id)">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Map View -->
        <div v-if="UpdateVisibleMap" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);" @click.self="UpdateVisibleMap = false">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Dorm Location</h5>
                        <button type="button" class="btn-close" @click="UpdateVisibleMap = false"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Display Data Modal -->
        <div v-if="VisibleDisplayDataModal" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);" @click.self="VisibleDisplayDataModal = false">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <!-- Modal Header -->
                    <div class="modal-header b text-dark rounded-top-4">
                        <h5 class="modal-title" id="tenantModalLabel">Dormitory Information</h5>
                        <button type="button" class="btn-close btn-close-dark" @click="VisibleDisplayDataModal = false"
                            aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding: 1.5rem;">
                        <div class="mb-3 text-center">
                            <img :src="currentMainImage" class="img-fluid rounded shadow-sm"
                                style="max-height: 300px; object-fit: cover; width: auto;" alt="Main Image">
                        </div>

                        <!-- Images Section -->
                        <div class="d-flex gap-3 justify-content-center">
                            <img v-if="selectedDorm?.images?.mainImage" :src="selectedDorm.images.mainImage"
                                class="img-thumbnail shadow-sm"
                                style="width: 120px; height: 80px; object-fit: cover; cursor: pointer;"
                                @click="changeMainImage(selectedDorm.images.mainImage)" alt="Thumbnail 1">

                            <img v-if="selectedDorm?.images?.secondaryImage" :src="selectedDorm.images.secondaryImage"
                                class="img-thumbnail shadow-sm"
                                style="width: 120px; height: 80px; object-fit: cover; cursor: pointer;"
                                @click="changeMainImage(selectedDorm.images.secondaryImage)" alt="Thumbnail 2">

                            <img v-if="selectedDorm?.images?.thirdImage" :src="selectedDorm.images.thirdImage"
                                class="img-thumbnail shadow-sm"
                                style="width: 120px; height: 80px; object-fit: cover; cursor: pointer;"
                                @click="changeMainImage(selectedDorm.images.thirdImage)" alt="Thumbnail 3">
                        </div>
                        <!-- Dorm Info Section -->
                        <div class="row g-4 mt-3">

                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Dorm Name</h6>
                                        <p class="mb-0 text-secondary">{{ selectedDorm?.dormName }}</p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Address</h6>
                                        <p class="mb-0 text-secondary" style="max-height: 100px; overflow-y: auto;">
                                            {{ selectedDorm?.address }}
                                        </p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Contact Email</h6>
                                        <p class="mb-0 text-secondary">{{ selectedDorm?.contactEmail }}</p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Description</h6>
                                        <p class="mb-0 text-secondary">{{ selectedDorm?.description }}</p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-2">Amenities</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span v-for="amenity in selectedDorm?.amenities" :key="amenity.id"
                                                class="badge rounded-pill bg-gradient px-3 py-2 shadow-sm"
                                                style="background:black; font-size: 0.85rem;">
                                                {{ amenity.aminityName }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-2">Rules & Policies</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span v-for="rule in selectedDorm?.rules_and_policy" :key="rule.id"
                                                class="badge rounded-pill bg-gradient px-3 py-2 shadow-sm"
                                                style="background: black; font-size: 0.85rem;">
                                                {{ rule.rulesName }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Contact Phone</h6>
                                        <p class="mb-0 text-secondary">{{ selectedDorm?.contactPhone }}</p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Total Rooms</h6>
                                        <p class="mb-0 text-secondary fw-semibold">{{ selectedDorm?.totalRooms }}</p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Registration Date</h6>
                                        <p class="mb-0 text-secondary">{{ formatDate(selectedDorm?.created_at) }}</p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Occupancy Type</h6>
                                        <p class="mb-0 text-secondary" style="max-height: 120px; overflow-y: auto;">
                                            {{ selectedDorm?.occupancyType }}
                                        </p>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-body rounded-4" style="border:2px solid #4edce2;">
                                        <h6 class="fw-bold text-primary mb-1">Building Type</h6>
                                        <p class="mb-0 text-secondary" style="max-height: 120px; overflow-y: auto;">
                                            {{ selectedDorm?.buildingType }}
                                        </p>
                                    </div>
                                </div>
                                <div class="card border-0 shadow-sm mt-3 mb-3">
                                    <div class="card-body rounded-4 p-3"
                                        style="border:2px solid #4edce2; background: linear-gradient(135deg, #e0f7f9, #ffffff);">

                                        <div class="d-flex align-items-center">
                                            <!-- GCash Icon -->
                                            <div class="me-3 p-2 bg-primary bg-opacity-10 rounded-circle">
                                                <i class="bi bi-wallet2 text-primary fs-4"></i>
                                            </div>

                                            <!-- Text Section -->
                                            <div>
                                                <h6 class="fw-bold text-primary mb-1">GCash Number</h6>
                                                <p class="mb-0 text-dark fw-semibold fs-5">
                                                    {{ selectedDorm?.gcashNumber }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>

                        </div>
                        <div class="card shadow-sm p-3 mb-4 border-0" @click="showReviews = !showReviews"
                            style="cursor:pointer;">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bi bi-chat-left-text-fill text-primary fs-2"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-primary mb-1">Review & Feedback</h6>
                                    <p class="mb-0 text-dark fw-semibold fs-5">
                                        {{ totalReviews }} Reviews
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Section -->
                        <div v-if="showReviews" class="card shadow-sm p-3 mb-4 border-0">
                            <div v-for="review in reviews" :key="review.id"
                                class="d-flex mb-3 align-items-start gap-3 p-3 rounded-3 shadow-sm"
                                style="background-color: #f9f9f9; border-left: 4px solid #3498db;">

                                <!-- Profile Image -->
                                <img v-if="review.profileImage" :src="review.profileImage" class="rounded-circle"
                                    style="width:50px; height:50px; object-fit:cover;">

                                <div class="flex-fill">
                                    <!-- Name & Stars -->
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold">{{ review.firstname }} {{ review.lastname }}</span>
                                        <small class="text-muted">{{ review.created_at }}</small>
                                    </div>
                                    <div class="text-warning mb-1">{{ review.stars }}</div>

                                    <!-- Comment -->
                                    <p class="text-muted mb-0">{{ review.comment }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="VisibleImagePostModal" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 overflow-hidden">
                    <!-- Header -->
                    <div class="modal-header text-black">
                        <h5 class="modal-title">Upload Images</h5>
                        <button type="button" class="btn-close" @click="VisibleImagePostModal = false"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body bg-white">
                        <div class="container mx-auto">

                            <div class="nav-pills w-100 ">
                                <ul class="nav mb-3 justify-content-center flex-wrap">
                                    <li class="" v-for="(step, index) in steps" :key="index">
                                        <button class="btn btn-primary m-2" :class="{ active: currentStep === index }"
                                            :disabled="index > currentStep">
                                            {{ step }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <!-- Step 0: Room Image Upload -->
                            <div v-if="currentStep === 0">
                                <div class="border border-secondary rounded-3 p-4 mb-3 text-center bg-light hover-shadow"
                                    style="cursor: pointer; transition: all 0.3s;" @click="triggerRoomImage1">

                                    <!-- Hidden File Input -->
                                    <input ref="RoomsImages1Input" class="d-none" type="file" accept="image/*"
                                        @change="handleroomImage1" />

                                    <!-- Icon + Text -->
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fa-solid fa-image fa-2x text-secondary mb-2"></i>
                                        <h5 class="text-secondary mb-1">Click to Upload Dorm Main Image</h5>
                                        <small class="text-muted">JPG, PNG or GIF – Max 5MB</small>
                                    </div>
                                </div>
                                <p v-if="errors.roomImage1File" class="text-danger text-center">{{
                                    errors.roomImage1File[0] }}</p>
                                <!-- Image Preview -->
                                <div v-if="roomImage1Preview" class="text-center mb-3">
                                    <img :src="roomImage1Preview" alt="Uploaded Room Image"
                                        class="img-fluid rounded mb-2" style="max-height: 250px;" />
                                    <div>
                                        <button type="button" @click="removeRoomImages1" class="btn btn-danger mt-3">
                                            Remove Uploaded Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 1: Room Image 2 Upload -->
                            <div v-if="currentStep === 1">
                                <div class="border border-secondary rounded-3 p-4 mb-3 text-center bg-light hover-shadow"
                                    style="cursor: pointer; transition: all 0.3s;" @click="triggerRoomImage2">

                                    <!-- Hidden File Input -->
                                    <input ref="RoomsImages2Input" class="d-none" type="file" accept="image/*"
                                        @change="handleroomImage2" />

                                    <!-- Icon + Text -->
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fa-solid fa-image fa-2x text-secondary mb-2"></i>
                                        <h5 class="text-secondary mb-1">Click to Upload Dorm Room Image 2</h5>
                                        <small class="text-muted">JPG, PNG or GIF – Max 5MB</small>
                                    </div>
                                </div>
                                <p v-if="errors.roomImage2File" class="text-danger text-center">{{
                                    errors.roomImage2File[0] }}</p>


                                <!-- Image Preview -->
                                <div v-if="roomImage2Preview" class="text-center mb-3">
                                    <img :src="roomImage2Preview" alt="Uploaded Room Image"
                                        class="img-fluid rounded mb-2" style="max-height: 250px;" />
                                    <div>
                                        <button type="button" @click="removeRoomImages2" class="btn btn-danger mt-3">
                                            Remove Uploaded Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Room Image 3 Upload -->
                            <div v-if="currentStep === 2">
                                <div class="border border-secondary rounded-3 p-4 mb-3 text-center bg-light hover-shadow"
                                    style="cursor: pointer; transition: all 0.3s;" @click="triggerRoomImage3">

                                    <!-- Hidden file input -->
                                    <input ref="RoomsImages3Input" class="d-none" type="file" accept="image/*"
                                        @change="handleroomImage3" />

                                    <!-- Icon and Text -->
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fa-solid fa-image fa-2x text-secondary mb-2"></i>
                                        <h5 class="text-secondary mb-1">Click to Upload Dorm Room Image 3</h5>
                                        <small class="text-muted">JPG, PNG or GIF – Max 5MB</small>
                                    </div>
                                </div>
                                <p v-if="errors.roomImage3File" class="text-danger text-center">{{
                                    errors.roomImage3File[0] }}</p>

                                <!-- Image Preview -->
                                <div v-if="roomImage3Preview" class="text-center mb-3">
                                    <img :src="roomImage3Preview" alt="Uploaded Room Image"
                                        class="img-fluid rounded mb-2" style="max-height: 250px;" />
                                    <div>
                                        <button type="button" @click="removeRoomImages3" class="btn btn-danger">
                                            Remove Uploaded Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4  text-create">
                                <button type="button" class="btn btn-outline-secondary" @click="prevStep"
                                    :disabled="currentStep === 0">
                                    Previous
                                </button>
                                <button type="button" class="btn btn-outline-success" @click="nextStep"
                                    :disabled="currentStep === steps.length - 1">
                                    Next
                                </button>
                            </div>
                            <div v-if="currentStep === steps.length - 1" class="mt-3 text-center">
                                <button class="btn btn-outline-success mb-2" @click="AddnewDorm">
                                    Submit Dorm Details
                                </button>
                            </div>
                            <!-- Image Grid -->
                        </div>
                    </div>
                    <!-- Footer -->
                </div>
            </div>
        </div>
        <!--Update Images-->
        <div v-if="VisibleUpdateImagePostModal" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 overflow-hidden">
                    <!-- Header -->
                    <div class="modal-header  text-black">
                        <h5 class="modal-title">Update Images</h5>
                        <button type="button" class="btn-close" @click="VisibleUpdateImagePostModal = false"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body bg-white">
                        <div class="container mx-auto">
                            <div class="nav-pills w-100 ">
                                <ul class="nav mb-3 justify-content-center flex-wrap">
                                    <li class="" v-for="(step, index) in steps" :key="index">
                                        <button class="btn btn-primary m-2"
                                            :class="{ active: editcurrentStep === index }"
                                            :disabled="index > editcurrentStep">
                                            {{ step }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div v-if="editcurrentStep === 0">
                                <div class="border border-secondary rounded-3 p-4 mb-3 text-center"
                                    style="cursor: pointer;" @click="edittriggerRoomImage1">

                                    <input ref="editRoomsImages1Input" class="d-none" type="file" accept="image/*"
                                        @change="edithandleroomImage1" />

                                    <!-- Icon + Text -->
                                    <div class="d-flex flex-column align-items-center text-center mb-3">
                                        <h5 class="text-secondary mt-2">Update Main Room Image</h5>
                                        <small class="text-muted">Click here to choose a new photo from your
                                            device</small>
                                    </div>
                                </div>


                                <!-- Image Preview -->
                                <div v-if="editDormData.roomImage1Preview || editDormData.roomImage1"
                                    class="text-center mb-3">
                                    <img :src="editDormData.roomImage1Preview || editDormData.roomImage1"
                                        alt="Uploaded Room Image" class="img-fluid rounded mb-2"
                                        style="max-height: 250px;" />
                                    <div class="mt-2">
                                        <button type="button" @click="editremoveRoomImages1"
                                            class="btn btn-danger btn-sm">
                                            Remove Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="editcurrentStep === 1">
                                <div class="border border-secondary rounded-3 p-4 mb-3 text-center"
                                    style="cursor: pointer;" @click="edittriggerRoomImage2">

                                    <input ref="editRoomsImages2Input" class="d-none" type="file" accept="image/*"
                                        @change="edithandleroomImage2" />

                                    <!-- Icon + Text -->
                                    <div class="d-flex flex-column align-items-center text-center mb-3">
                                        <h5 class="text-secondary mt-2">Update Secondary Room Image</h5>
                                        <small class="text-muted">Click here to select a new photo from your
                                            device</small>
                                    </div>
                                </div>


                                <!-- Image Preview -->
                                <div v-if="editDormData.roomImage2Preview || editDormData.roomImage2"
                                    class="text-center mb-3">
                                    <img :src="editDormData.roomImage2Preview || editDormData.roomImage2"
                                        alt="Uploaded Room Image" class="img-fluid rounded mb-2"
                                        style="max-height: 250px;" />
                                    <div>
                                        <div class="mt-2">
                                            <button type="button" @click="editremoveRoomImages2"
                                                class="btn btn-danger btn-sm">
                                                Remove Image
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="editcurrentStep === 2">
                                <div class="border border-secondary rounded-3 p-4 mb-3 text-center"
                                    style="cursor: pointer;" @click="edittriggerRoomImage3">

                                    <input ref="editRoomsImages3Input" class="d-none" type="file" accept="image/*"
                                        @change="edithandleroomImage3" />

                                    <!-- Icon + Text -->
                                    <div class="d-flex flex-column align-items-center text-center mb-3">
                                        <h5 class="text-secondary mt-2">Update Third Room Image</h5>
                                        <small class="text-muted">Click here to choose a new photo from your
                                            device</small>
                                    </div>
                                </div>


                                <!-- Image Preview -->
                                <div v-if="editDormData.roomImage3Preview || editDormData.roomImage3"
                                    class="text-center mb-3">
                                    <img :src="editDormData.roomImage3Preview || editDormData.roomImage3"
                                        alt="Uploaded Room Image" class="img-fluid rounded mb-2"
                                        style="max-height: 250px;" />
                                    <div>
                                        <button type="button" @click="editremoveRoomImages3"
                                            class="btn btn-danger btn-sm">
                                            Remove Image
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4  text-create">
                                <button type="button" class="btn btn-outline-secondary" @click="updateprevStep"
                                    :disabled="editcurrentStep === 0">
                                    Previous
                                </button>
                                <button type="button" class="btn btn-outline-success" @click="updatenextStep"
                                    :disabled="editcurrentStep === steps.length - 1">
                                    Next
                                </button>
                            </div>
                            <div v-if="editcurrentStep === steps.length - 1" class="mt-3 text-center">
                                <button class="btn btn-outline-success mb-2" @click="editImages">
                                    Update Dormitory Images
                                </button>
                            </div>

                            <!-- Image Grid -->

                        </div>
                    </div>

                    <!-- Footer -->
                </div>
            </div>

        </div>

    </div>
    <Modalconfirmation ref="modal" />
    <Toastcomponents ref="toast" />


    <!-- Modal Footer -->

</template>

<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import NotificationList from '@/components/notifications.vue';
import { debounce } from 'lodash';


export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList
    },
    name: "MapView",
    data() {
        return {
            landlord_id: '',
            searchTerm: '',
           
            debouncedSearchTerm: '',
            selectedLocation: '',
            selectedAvailability: '',
            VisibleAddModal: false,
            VisibleMap: false,
            UpdateVisibleMap: false,
            VisibleDeleteModal: false,
            VisibleUpdateModal: false,
            VisibleDisplayDataModal: false,
            VisibleImagePostModal: false,
            VisibleUpdateImagePostModal: false,
            amenitiesModal: false,
            steps: ["Upload Images 1 ", "Upload Images 2", "Upload Images 3"],
            rulesandpoliciesModal: false,
            currentStep: 0,
            roomImage1Preview: "",
            roomImage1File: "",
            roomImage2Preview: "",
            roomImage2File: "",
            roomImage3Preview: "",
            roomImage3File: "",
            dorm_name: "",
            address: "",
            description: "",
            total_rooms: 1,
            contact_email: "",
            contact_phone: "",
            contact_phone: "",
            availability: "",
            occupancy_type: "",
            building_type: "",
            longitude: "",
            latitude: "",
            amenities: [''],
            inputamenities: '',
            gcashNumber: '',
            rules: [''],
            inputrules: '',
            errors: {},
            errorsEdit: {},
            dorms: [],
            dormId: '',
            selectedDorm: null,
            currentDormId: null,
            currntamenitiesId: null,
            editDormData: {
                dorm_id: null,
                dormName: "",
                address: "",
                description: "",
                totalRooms: "",
                contactEmail: "",
                contactPhone: "",
                availability: "",
                occupancyType: "",
                buildingType: "",
                latitude: "",
                longitude: "",
                newAmenities: '',
                roomImage1: '',
                roomImage1Preview: "",
                roomImage1File: "",
                roomImage2: '',
                roomImage2Preview: "",
                roomImage2File: "",
                roomImage3: '',
                roomImage3Preview: "",
                roomImage3File: "",
                gcashNumber: '',
                image_id: "",
                images: {
                    main_image: null,
                    secondary_image: null,
                    third_image: null,
                    image_id: null,
                }
            },
            editcurrentStep: 0,
            lastPage: 1,
            currentPage: 1,
            newAmenity: '',
            newrules: "",
            notifications: [],
            currentMainImage: null,
            isVerified: false,
            firstname: '',
            lastname: '',
            profilePic: '',
            averageRating: 0,
            average_rating: 0,
            average_stars: '',
            reviews: [],       // load from API
            totalReviews: 0,
            showReviews: false,


        };
    },
    methods: {
        async getlandlordVerifiedStatus()
        {
            const res = await axios.get('/getlandlordVerifiedStatus');
            if (res.data.status === 'success') {
                this.isVerified = res.data.isVerified;
            } else {
                console.error('Failed to fetch landlord verified status');
                this.isVerified = false;
            }
        },  
        goToVerificationPage() {
            window.location.href = '/paymentLandlord/' + this.landlord_id;
        },
        modalconfirmation() {
            this.$refs.modal.visible = true;

        },
        subscribeToNotifications() {
            if (this.hasSubscribed) return; // prevent multiple subscriptions
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
        handlePagination(page) {
            this.currentPage = page;

            if (this.filterMode === 'search') {
                this.fetchDorms(page, this.searchTerm.toLowerCase());
            } else if (this.filterMode === 'location') {
                this.dropdownLocation(page);
            } else if (this.filterMode === 'availability') {
                this.dropdownAvailability(page);
            }
            else {
                // default fallback
                this.fetchDorms(page);
            }
        },

        //list of dormitories and search
        async fetchDorms(page = 1, term = '') {
            this.$refs.loader.loading = true;
            try {
                const response = await axios.get('/SearchDorms', {
                    params: { page, search: term },
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                });

                if (response.data.status === "success") {
                    this.dorms = response.data.dorms.data;
                    this.currentPage = response.data.dorms.current_page;
                    this.lastPage = response.data.dorms.last_page;
                    this.$refs.loader.loading = false;
                    this.selectedAvailability = '';
                    this.selectedLocation = '';

                } else {
                    console.error("Failed to fetch dorms:", response.data.message);
                }
            } catch (error) {
                console.error("Error fetching dorms:", error);
            } finally {
                this.$refs.loader.loading = false;
                ;
            }
        },

        async dropdownLocation(page = 1) {
            this.$refs.loader.loading = true;
            try {
                const response = await axios.get('/filter-locations', {
                    params: { page: this.currentPage, location: this.selectedLocation },
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                });
                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;

                    this.dorms = response.data.dorms.data;
                    this.currentPage = response.data.dorms.current_page;
                    this.lastPage = response.data.dorms.last_page;
                } else {
                    console.error("Failed to fetch dorms by location:", response.data.message);
                    this.$refs.loader.loading = false;

                }
            }
            catch (error) {
                console.error("Error fetching dorms by location:", error);
                this.$refs.loader.loading = false;

            } finally {
                this.$refs.loader.loading = false;
            }

        },
        async dropdownAvailability(page = 1) {
            this.$refs.loader.loading = true;
            const response = await axios.get('/filter-availability', {
                params: { page: this.currentPage, availability: this.selectedAvailability },
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            });
            if (response.data.status === "success") {
                this.dorms = response.data.dorms.data;
                this.currentPage = response.data.dorms.current_page;
                this.lastPage = response.data.dorms.last_page;
                this.$refs.loader.loading = false;

            } else {
                console.error("Failed to fetch dorms by availability:", response.data.message);
                this.$refs.loader.loading = false;
            }
        },
        //view Dorm data
        async viewDorm(dormId) {
            this.$refs.loader.loading = true;
            try {
                const response = await axios.get(`/view-dorm/${dormId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;
                    this.selectedDorm = response.data.dorm;
                    this.VisibleDisplayDataModal = true;
                    this.totalReviews = response.data.total_reviews;

                    // Add these lines
                    this.average_rating = response.data.average_rating;
                    this.average_stars = response.data.average_stars;
                    this.reviews = response.data.reviews;
                } else {
                    console.error("Failed to fetch dorm details:", response.data.message);
                }

            } catch (error) {
                console.error("Error fetching dorm details:", error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        //end view doorm
        fill() {
            this.dorm_name = " ";
            this.address = " ";
            this.description = " ";
            this.total_rooms = 1;
            this.contact_email = " ";
            this.contact_phone = " ";
            this.rules = " ";
            this.gcashNumber = " ",
                this.roomImage1File = ' ';
            if (this.$refs.roomImage1Preview) {
                this.$refs.roomImage1Preview.value = ''; // Reset file input
            }

            this.roomImage2File = ' ';
            if (this.$refs.roomImage2Preview) {
                this.$refs.roomImage2Preview.value = ''; // Reset file input
            }
            this.roomImage3Preview = ' ';
            if (this.$refs.roomImage3Preview) {
                this.$refs.roomImage3Preview.value = ''; // Reset file input
            }
            
            this.currentStep = 0;


        },
        changeMainImage(imageUrl) {
            this.currentMainImage = imageUrl;
        },
        //end fill data
        //redirect rooms page
        ViewRoomsPage() {
            window.location.href = `/landlordRoomManagement/${this.landlord_id}`;
        },
        //end room page
        //functions for adding dormitories
        async AddnewDorm() {
            // this.$refs.loader.loading = true;
            const formData = new FormData();
            formData.append('dorm_name', this.dorm_name);
            formData.append('address', this.address);
            formData.append('description', this.description);
            formData.append('latitude', this.latitude);
            formData.append('longitude', this.longitude);
            formData.append('total_rooms', this.total_rooms);
            formData.append('contact_email', this.contact_email);
            formData.append('contact_phone', this.contact_phone);
            formData.append('availability', this.availability);
            formData.append('occupancy_type', this.occupancy_type);
            formData.append('building_type', this.building_type);
            formData.append('gcashNumber', this.gcashNumber);
            formData.append('roomImage1File', this.roomImage1File);
            formData.append('roomImage2File', this.roomImage2File);
            formData.append('roomImage3File', this.roomImage3File);

            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Adding Dorm',
                    message: `Are you sure you want to Add Dorm? ${this.dorm_name}`,
                    functionName: 'Add new Dormitory'
                });

                if (!confirmed) {
                    this.$refs.loader.loading = false;
                    return;
                }

                const response = await axios.post('/AddDorm', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                this.$refs.loader.loading = false;

                if (response.data.status === "success") {
                    this.VisibleAddModal = false;
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.VisibleImagePostModal = false;
                    this.fill();
                    this.amenitiesModal = true;
                    this.errors = {};
                    this.currentStep = 0;
                    this.dormId = response.data.dormId;
                    await this.fetchDorms(); // Refresh dorm list
                } else {
                    // Handle other statuses if necessary
                    this.$refs.toast.showToast('Failed to add dorm. Please try again.', 'danger');
                    this.$refs.loader.loading = false;  // <== Add this here!

                }

            } catch (error) {
                this.$refs.loader.loading = false;
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    this.$refs.loader.loading = false;

                } else if (error.response && error.response.data && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error("Error adding dorm:", error);
                    alert('An error occurred while adding the dorm. Please try again.');
                }
            }
        },

        CloseAddModal() {
            this.VisibleAddModal = false;
            this.fill();
            this.errors = {};
        },
        increamentRooms() {
            this.total_rooms++;
        },
        decreamnentRooms() {
            if (this.total_rooms <= 1) {

            }
            else {
                this.total_rooms--;
            }
        },
        async DisplayModalImages() {
            this.$refs.loader.loading = true;
            const formData = new FormData();
            formData.append('dorm_name', this.dorm_name);
            formData.append('address', this.address);
            formData.append('description', this.description);
            formData.append('total_rooms', this.total_rooms);
            formData.append('contact_email', this.contact_email);
            formData.append('contact_phone', this.contact_phone);
            formData.append('availability', this.availability);
            formData.append('occupancy_type', this.occupancy_type);
            formData.append('building_type', this.building_type);
            formData.append('gcashNumber', this.gcashNumber);


            try {
                const response = await axios.post('/input-text', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;

                    this.VisibleImagePostModal = true;
                    this.errors = {};
                }
            } catch (error) {
                this.$refs.loader.loading = false;

                if (error.response && error.response.status === 422) {
                    console.error("Validation errors:", error.response.data.message);
                    this.errors = error.response.data.message;
                    // this.$refs.toast.showToast(response.data.message, 'danger');
                } else {
                    console.error("An error occurred:", error);
                }
            }
        },
        //Room Images Picture
        handleroomImage1(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.roomImage1Preview) {
                    URL.revokeObjectURL(this.roomImage1Preview);
                }
                this.roomImage1File = file;

                this.roomImage1Preview = URL.createObjectURL(file);
            }
        },
        triggerRoomImage1() {
            if (this.$refs.RoomsImages1Input) {
                this.$refs.RoomsImages1Input.click();
            }
        },

        removeRoomImages1() {
            if (this.roomImage1Preview) {
                URL.revokeObjectURL(this.roomImage1Preview);
            }
            this.roomImage1Preview = null;
            this.roomImage1File = null;
            // Add null check for safety
            if (this.$refs.roomImage1Preview) {
                this.$refs.roomImage1Preview.value = '';
                this.$refs.roomImage1File = '';
            }
        },
        //image 2
        handleroomImage2(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.roomImage2Preview) {
                    URL.revokeObjectURL(this.roomImage2Preview);
                }
                this.roomImage2File = file;

                this.roomImage2Preview = URL.createObjectURL(file);
            }
        },
        triggerRoomImage2() {
            if (this.$refs.RoomsImages2Input) {
                this.$refs.RoomsImages2Input.click();
            }
        },

        removeRoomImages2() {
            if (this.roomImage2Preview) {
                URL.revokeObjectURL(this.roomImage2Preview);
            }
            this.roomImage2Preview = null;
            this.roomImage2File = "";

            // Add null check for safety
            if (this.$refs.roomImage2Preview) {
                this.$refs.roomImage2Preview.value = ''; // Reset file input
            }
        },
        //image 3
        handleroomImage3(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.roomImage3Preview) {
                    URL.revokeObjectURL(this.roomImage3Preview);
                }
                this.roomImage3File = file;


                this.roomImage3Preview = URL.createObjectURL(file);
            }
        },
        triggerRoomImage3() {
            if (this.$refs.RoomsImages3Input) {
                this.$refs.RoomsImages3Input.click();
            }
        },

        removeRoomImages3() {
            if (this.roomImage3Preview) {
                URL.revokeObjectURL(this.roomImage3Preview);
            }
            this.roomImage3Preview = null;
            this.roomImag32File = "";

            // Add null check for safety
            if (this.$refs.roomImage3Preview) {
                this.$refs.roomImage3Preview.value = ''; // Reset file input
            }
        },
        async addnewAmenity() {
            if (!this.editDormData?.newAmenities || this.editDormData.newAmenities.trim() === '') {
                this.errors.editDormData ??= {};
                this.errors.editDormData.newAmenities = ['Please fill in the amenity field before submitting.'];
                return;
            }
            this.errors.amenities = [];

            const confirmed = await this.$refs.modal.show({
                title: 'Adding new Aminity',
                message: `Confirm adding this amenity to your dorm?`,
                functionName: 'Add new Aminity (Optional)'
            });

            if (!confirmed) {
                this.$refs.loader.loading = false;
                return;
            }

            this.$refs.loader.loading = true;
            const formData = new FormData();
            formData.append('amenities', this.editDormData.newAmenities);
            formData.append('dorm_id', this.editDormData.dorm_id);

            try {
                const response = await axios.post('/add-amenities', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const res = response.data;

                if (res.status === 'success') {
                    this.$refs.loader.loading = false;

                    this.$refs.toast.showToast(this.editDormData.newAmenities + 'added successfully.');
                    //this.editDormData.newAmenities = '';
                    this.VisibleUpdateModal = false;
                    this.errors = {};
                    this.fetchDorms();

                } else {
                    this.$refs.loader.loading = false;

                    this.errors.amenities = [res.message || 'Failed to add amenity.'];
                    this.$refs.toast.showToast(this.errors.amenities[0], 'error');
                }

            } catch (error) {
                if (error.response && error.response.data) {
                    this.$refs.loader.loading = false;

                    const res = error.response.data;

                    // Laravel's default validation error structure
                    if (res.errors) {
                        this.errors.editDormData ??= {};

                        if (res.errors.amenities) {
                            this.errors.editDormData.amenities = res.errors.amenities;
                            this.$refs.toast.showToast(res.errors.amenities[0], 'error');
                        }

                    } else if (res.message) {

                        this.errors.editDormData ??= {};
                        this.errors.editDormData.newAmenities = [res.message];


                    } else {
                        // Unexpected format
                        this.errors.editDormData ??= {};
                        this.errors.editDormData.amenities = ['An unexpected error occurred.'];
                        this.$refs.toast.showToast('An unexpected error occurred.', 'error');
                    }
                } else {
                    this.errors.editDormData ??= {};
                    this.errors.editDormData.amenities = ['Network or unknown error occurred.'];
                    this.$refs.toast.showToast('Network or unknown error occurred.', 'error');
                }
            }


        },
        addRulesAndPoliciesModal() {
            this.rulesandpoliciesModal = true;
            this.errors = {};
            this.rules = [''];
        },
        async addRulesAndpolicy() {
            const formData = new FormData();
            formData.append('rules', this.rules.join(','));
            formData.append('dorm_id', this.dormId);
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Adding Rules and Policies',
                    message: `Are you sure you want to Add Rules and Policies?`,
                    functionName: 'Add new Rules and Policies (Optional)'
                });

                if (!confirmed) {
                    this.$refs.loader.loading = false;
                    return;
                }

                this.$refs.loader.loading = true;

                const response = await axios.post('/add-rules', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.rules = [''];
                    this.errors = {};
                    this.inputrules = '';
                } else {
                    this.$refs.loader.loading = false;
                    this.$refs.toast.showToast(response.data.message, 'danger');
                }
            } catch (error) {
                this.$refs.loader.loading = false;

                if (error.response && error.response.status === 422) {
                    const validationErrors = error.response.data.message;
                    // let messages = Object.values(validationErrors).flat().join('\n');
                    this.errors.rules = [validationErrors];

                } if (error.response.status === 400) {
                    const message = error.response.data.message || 'Something went wrong.';
                    this.errors.rules = [message];
                }
            }
        },
        async deleteRulesAndPolicies(id) {
            this.rulesId = id;
            const confirmed = await this.$refs.modal.show({
                title: 'Delete Rule',
                message: 'Are you sure you want to delete this Rule?',
                functionName: 'Delete Rule',

            });

            if (!confirmed) return;

            this.$refs.loader.loading = true;
            if (this.rulesId) {

                try {
                    const response = await axios.delete(`/delete-rules/${this.rulesId}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    this.$refs.loader.loading = false;

                    if (response.data.status === "success") {
                        this.$refs.toast.showToast(response.data.message, 'success');
                        this.fetchDorms();
                        this.VisibleUpdateModal = false;
                    } else {
                        this.$refs.toast.showToast('Failed to delete rules. Please try again.', 'error');
                    }
                } catch (error) {
                    this.$refs.loader.loading = false;
                    console.error("Error deleting rules:", error);
                    console.log("Deleting rule with ID:", this.rulesId);

                }
            } else {
                console.warn("No dorm ID provided for deleting amenities.");
            }
        },
        async addNewRule() {
            if (!this.newRule === "") {
                this.$refs.toast.showToast('Please enter a rule before adding.', 'danger');
                return;
            }
            this.rules.push(this.newRule);
            this.newRule = '';
            this.errors = {};
            const confirmed = await this.$refs.modal.show({
                title: 'Adding New Rule',
                message: `Are you sure you want to add this rule?`,
                functionName: 'Add new Rule (Optional)'
            });
            if (!confirmed) {
                this.rules.pop();
                return;
            }
            this.$refs.loader.loading = true;
            try {
                const response = await axios.post('/add-rules', {
                    rules: this.newrules,
                    dorm_id: this.editDormData.dorm_id,
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === 'success') {
                    this.$refs.loader.loading = false;
                    this.$refs.toast.showToast(`${this.newrules} added successfully.`, 'success');
                    this.VisibleUpdateModal = false;
                    this.newrules = '';
                } else if (response.data.errors) {
                    this.errors.rules = [response.data.message];
                }

            } catch (error) {
                this.$refs.loader.loading = false;

                // Show Laravel validation or custom error message
                if (error.response && error.response.data) {
                    const res = error.response.data;

                    if (res.errors && res.errors.rules) {
                        this.errors.newrules = res.errors.rules;
                    } else if (res.message) {
                        this.errors.newrules = [res.message];

                        this.$refs.loader.loading = false;
                    } else {
                        this.$refs.toast.showToast('An unexpected error occurred.', 'error');
                        this.$refs.loader.loading = false;
                    }
                } else {
                    this.$refs.toast.showToast('An unexpected error occurred.', 'error');
                    this.$refs.loader.loading = false;

                }
            } finally {
                this.$refs.loader.loading = false;
            }


        },
        async UploadImages1() {
            try {
                this.$refs.loader.loading = true;
                const formData = new FormData();
                formData.append('roomImage1File', this.roomImage1File);
                const response = await axios.post('/upload-main-image', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.currentStep = 1;
                    this.$refs.loader.loading = false;

                }
            }
            catch (error) {
                if (error.response && error.response.status === 422) {
                    // Validation errors from backend
                    this.errors = error.response.data.errors;
                    this.$refs.loader.loading = false;
                } else {
                    // Other errors
                    const message = error.response?.data?.message || 'Something went wrong';
                    this.$refs.toast.showToast(message, 'danger');
                    console.error(error);
                }
            }

            finally { 
                this.$refs.loader.loading = false;

            }
        },
        async UploadImages2() {
            try {
                this.$refs.loader.loading = true;

                const formData = new FormData();
                formData.append('roomImage2File', this.roomImage2File);
                const response = await axios.post('/upload-secondary-image', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;
                    this.currentStep = 2;
                }
            }
            catch (error) {
                this.$refs.loader.loading = false;
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    this.$refs.loader.loading = false;

                } else {
                    this.$refs.toast.showToast(response.data.messages, 'danger');
                    console.error(error);
                }
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async addAmenity() {

            if (this.amenities.some(a => a.trim() === '')) {
                this.errors.amenities = ['Please fill in all amenity fields before submitting.'];
                return;
            }
            this.errors = {};
            const confirmed = await this.$refs.modal.show({
                title: 'Adding Dorm',
                message: `Are you sure you want to Add Dorm?`,
                functionName: 'Add new Aminity (Optional)'
            });

            if (!confirmed) {
                this.$refs.loader.loading = false;
                return;
            }
            this.$refs.loader.loading = true;


            for (const amenity of this.amenities) {
                try {
                    const response = await axios.post('/add-amenities', {
                        amenities: amenity.trim(),
                        dorm_id: this.dormId,
                    }, {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.data.status === 'success') {
                        this.$refs.loader.loading = false;
                        this.amenities = [''];
                        this.inputamenities = '';
                        this.amenitiesModal = true;

                        this.$refs.toast.showToast(`${amenity} added successfully.`, 'success');
                    } else if (response.data.errors) {
                        this.errors.amenities = [response.data.message];
                    }

                } catch (error) {
                    this.$refs.loader.loading = false;

                    // Show Laravel validation or custom error message
                    if (error.response && error.response.data) {
                        const res = error.response.data;

                        if (res.errors && res.errors.amenities) {
                            this.errors.amenities = res.errors.amenities;
                        } else if (res.message) {
                            this.errors.amenities = [res.message];
                            this.$refs.loader.loading = false;
                        } else {
                            this.$refs.toast.showToast('An unexpected error occurred.', 'error');
                            this.$refs.loader.loading = false;
                        }
                    } else {
                        this.$refs.toast.showToast('An unexpected error occurred.', 'error');
                        this.$refs.loader.loading = false;

                    }
                }
            }
        },
        closeaminitiemodal() {
            this.amenities = [''];
            this.inputamenities = '';
            this.amenitiesModal = false;
            this.errors = {};
            this.addRulesAndPoliciesModal();
        },

        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
            }
        },
        goToStep(index) {
            if (index <= this.currentStep) {
                this.currentStep = index;
            }
        },
        nextStep() {
            if (this.currentStep < this.steps.length - 1) {
                let isValid = true;

                if (this.currentStep === 0) {
                    isValid = this.UploadImages1();



                } else if (this.currentStep === 1) {
                    isValid = this.UploadImages2();
                }
            }
        },
        //end adding function
        //updating dorm functions
        async editDorm(dormId) {
            try {
                const response = await axios.get(`/view-dorm/${dormId}`);
                if (response.data.status === "success") {
                    this.editDormData = {
                        ...response.data.dorm,
                        dorm_id: dormId,
                        roomImage1Preview: response.data.dorm.images?.mainImage || null,
                        roomImage1: response.data.dorm.images?.mainImage || null,
                        roomImage1File: null,
                        roomImage2Preview: response.data.dorm.images?.secondaryImage || null,
                        roomImage2: response.data.dorm.images?.secondaryImage || null,
                        roomImage2File: null,
                        roomImage3Preview: response.data.dorm.images?.thirdImage || null,
                        roomImage3: response.data.dorm.images?.thirdImage || null,
                        roomImage3File: null,
                        image_id: response.data.dorm.images?.imagesID || null,
                    };
                    this.VisibleUpdateModal = true;
                } else {
                    console.error("Failed to fetch dorm details:", response.data.message);
                    alert("Failed to load dorm details for editing");
                }
            } catch (error) {
                console.error("Error fetching dorm details:", error);
                alert("An error occurred while loading dorm details");
            }
        },
        updateImages() {
            this.VisibleUpdateImagePostModal = true;
        },
        async updateDorm() {
            // Hide spinner
            
            const formData = new FormData();
            formData.append('dormName', this.editDormData.dormName);
            formData.append('address', this.editDormData.address);
            formData.append('description', this.editDormData.description);
            formData.append('latitude', this.editDormData.latitude);
            formData.append('longitude', this.editDormData.longitude);
            formData.append('totalRooms', this.editDormData.totalRooms);
            formData.append('contactEmail', this.editDormData.contactEmail);
            formData.append('contactPhone', this.editDormData.contactPhone);
            formData.append('availability', this.editDormData.availability);
            formData.append('occupancyType', this.editDormData.occupancyType);
            formData.append('buildingType', this.editDormData.buildingType);
            formData.append('gcashNumber', this.editDormData.gcashNumber);

            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Update Dorm',
                    message: `Confirm update to this dorm’s information?`,
                    functionName: 'Update Dormitory',
                });

                if (!confirmed) {
                    return;
                }
                this.$refs.loader.loading = true;

                const response = await axios.post(`/UpdateDorm/${this.editDormData.dorm_id}`, formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                // Hide spinner
                if (response.data.status === "success") {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.$refs.loader.loading = false;
                    this.VisibleUpdateModal = false;
                    this.errors = {};
                    await this.fetchDorms(); // Refresh dorm list
                }
            } catch (error) {
                this.$refs.loader.loading = false; // Hide spinner

                if (error.response && error.response.status === 422) {
                    if (error.response.data.errors) {
                        // Validation errors from Laravel
                        this.errors.editDormData = error.response.data.errors;

                        console.log('Validation errors:', this.errors);
                    } else if (error.response && error.response.data.message) {
                        this.errors = { general: error.response.data.message };
                        this.$refs.toast.showToast(error.response.data.message, 'error');
                    } else {
                        console.error("Error updating dorm:", error);
                        alert('An unexpected error occurred. Please try again.');
                    }
                } else {
                    console.error("Error updating dorm:", error);
                    alert('An error occurred while updating the dorm. Please try again.');
                }
            }
        },
        updateincreamentRooms() {
            this.editDormData.totalRooms++;
        },
        updatedecreamnentRooms() {
            if (this.editDormData.totalRooms <= 1) {

            }
            else {
                this.editDormData.totalRooms--;
            }
        },
        async editUploadImages1() {
            try {
                this.$refs.loader.loading = true;

                const formData = new FormData();

                if (this.editDormData.roomImage1File) {
                    formData.append('roomImage1File', this.editDormData.roomImage1File);
                    formData.append('isNewImage', 'true');
                } else if (this.editDormData.roomImage1Preview) {
                    formData.append('existingImage', this.editDormData.roomImage1Preview);
                    formData.append('isNewImage', 'false');
                } else {
                    formData.append('existingImage', '');
                    formData.append('isNewImage', 'false');
                }

                const response = await axios.post('/edit-main-image', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === "success") {
                    this.editcurrentStep = 1;
                    this.$refs.loader.loading = false;
                }
            } catch (error) {
                this.$refs.loader.loading = false;
                if (error.response && error.response.status === 422) {
                    const validationErrors = error.response.data.message;
                    let messages = Object.values(validationErrors).flat().join('\n');
                    this.$refs.toast.showToast(messages, 'danger');
                } else {
                    const msg = error.response?.data?.message || error.message || "An error occurred";
                    this.$refs.toast.showToast(msg, 'danger');
                    console.error(error);
                }
            }
        },

        async editUploadImages2() {
            try {
                this.$refs.loader.loading = true;


                const formData = new FormData();
                if (this.editDormData.roomImage2File) {
                    formData.append('roomImage2File', this.editDormData.roomImage2File);
                    formData.append('isNewImage', 'true'); // Optional flag to indicate new image uploaded
                } else if (this.editDormData.roomImage2Preview) {
                    formData.append('existingImage', this.editDormData.roomImage2Preview);
                    formData.append('isNewImage', 'false'); // Optional flag
                } else {
                    formData.append('existingImage', '');
                    formData.append('isNewImage', 'false');
                }
                const response = await axios.post('/edit-secondary-image', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;
                    this.editcurrentStep = 2;
                }
            }
            catch (error) {
                this.$refs.loader.loading = false;
                if (error.response && error.response.status === 422) {
                    const validationErrors = error.response.data.message;
                    let messages = Object.values(validationErrors).flat().join('\n');
                    this.$refs.toast.showToast(messages, 'danger');

                } else {
                    this.$refs.toast.showToast(response.data.messages, 'danger');
                    console.error(error);
                }
            }
        },
        updateprevStep() {
            if (this.editcurrentStep > 0) {
                this.editcurrentStep--;
            }
        },
        updategoToStep(index) {
            if (index <= this.editcurrentStep) {
                this.editcurrentStep = index;
            }
        },
        updatenextStep() {
            if (this.editcurrentStep < this.steps.length - 1) {
                let isValid = true;

                if (this.editcurrentStep === 0) {
                    isValid = this.editUploadImages1();



                } else if (this.editcurrentStep === 1) {
                    isValid = this.editUploadImages2();
                }
            }
        },
        edithandleroomImage1(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.editDormData.roomImage1Preview) {
                    URL.revokeObjectURL(this.editDormData.roomImage1Preview);
                }
                this.editDormData.roomImage1File = file;

                this.editDormData.roomImage1Preview = URL.createObjectURL(file);
            }
        },
        edittriggerRoomImage1() {
            if (this.$refs.editRoomsImages1Input) {
                this.$refs.editRoomsImages1Input.click();
            }
        },

        editremoveRoomImages1() {
            if (this.editDormData.roomImage1Preview) {
                URL.revokeObjectURL(this.editDormData.roomImage1Preview);
            }
            this.editDormData.roomImage1Preview = null;
            this.editDormData.roomImage1File = "";

            // Add null check for safety
            if (this.$refs.editRoomsImages1Input) {
                this.$refs.editRoomsImages1Input.value = ''; // Reset file input
            }

        },
        //image 2
        edithandleroomImage2(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.editDormData.roomImage2Preview) {
                    URL.revokeObjectURL(this.editDormData.roomImage2Preview);
                }
                this.editDormData.roomImage2File = file;

                this.editDormData.roomImage2Preview = URL.createObjectURL(file);
            }
        },
        edittriggerRoomImage2() {
            if (this.$refs.editRoomsImages2Input) {
                this.$refs.editRoomsImages2Input.click();
            }
        },

        editremoveRoomImages2() {
            if (this.editDormData.roomImage2Preview) {
                URL.revokeObjectURL(this.editDormData.roomImage2Preview);
            }
            this.editDormData.roomImage2Preview = null;
            this.editDormData.roomImage2File = "";

            // Add null check for safety
            if (this.$refs.editRoomsImages2Input) {
                this.$refs.editRoomsImages2Input.value = ''; // Reset file input
            }
        },
        //image 3
        edithandleroomImage3(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.editDormData.roomImage3Preview) {
                    URL.revokeObjectURL(this.editDormData.roomImage3Preview);
                }
                this.editDormData.roomImage3File = file;
                this.editDormData.roomImage3Preview = URL.createObjectURL(file);
            }
        },
        edittriggerRoomImage3() {
            if (this.$refs.editRoomsImages3Input) {
                this.$refs.editRoomsImages3Input.click();
            }
        },
        editremoveRoomImages3() {
            if (this.editDormData.roomImage3Preview) {
                URL.revokeObjectURL(this.editDormData.roomImage3Preview);
            }
            this.editDormData.roomImage3Preview = null;
            this.editDormData.roomImage3File = "";

            // Add null check for safety
            if (this.$refs.editRoomsImages2Input) {
                this.$refs.editRoomsImages2Input.value = ''; // Reset file input
            }
        },
        async editImages() {

            const formData = new FormData();

            if (this.editDormData.roomImage1File instanceof File) {
                formData.append("roomImage1File", this.editDormData.roomImage1File);
            }
            if (this.editDormData.roomImage2File instanceof File) {
                formData.append("roomImage2File", this.editDormData.roomImage2File);
            }
            if (this.editDormData.roomImage3File instanceof File) {
                formData.append("roomImage3File", this.editDormData.roomImage3File);
            }
            formData.append("dorm_id", this.editDormData.dorm_id);
            const confirmed = await this.$refs.modal.show({
                title: 'Update Dorm',
                message: `Confirm update to this dorm’s Images?`,
                functionName: 'Update Dormitories Image'
            });
            if(confirmed === false){
                return;
            }
            this.$refs.loader.loading = true;

            try {
                const response = await axios.post(`/edit-images/${this.editDormData.image_id}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === 'success') {
                    this.$refs.loader.loading = false;
                    this.VisibleUpdateImagePostModal = false;
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.editDormData.roomImage1File = null;
                    this.editDormData.roomImage2File = null;
                    this.editDormData.roomImage3File = null;
                    this.editcurrentStep = 0;

                }
            } catch (error) {
                this.$refs.loader.loading = false;
            }
        },


        //end updating dorms functions
        //deleting dorm functions 

        async deleteAmenity(id) {

            this.amenitiesId = id;
            const confirmed = await this.$refs.modal.show({
                title: 'Delete Aminity',
                message: 'Are you sure you want to delete this Aminity?',
                functionName: 'Delete Aminity',

            });

            if (!confirmed) return;

            this.$refs.loader.loading = true;
            if (this.amenitiesId) {

                try {
                    const response = await axios.delete(`/delete-amenities/${this.amenitiesId}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    this.$refs.loader.loading = false;

                    if (response.data.status === "success") {
                        this.$refs.toast.showToast(response.data.message, 'success');
                        this.fetchDorms();
                        this.VisibleUpdateModal = false;
                    } else {
                        this.$refs.toast.showToast('Failed to delete amenities. Please try again.', 'error');
                    }
                } catch (error) {
                    this.$refs.loader.loading = false;
                    console.error("Error deleting amenities:", error);
                    console.log("Deleting amenity with ID:", this.amenitiesId);

                }
            } else {
                console.warn("No dorm ID provided for deleting amenities.");
            }
        },
        async deleteDorm(dormId) {
            this.currentDormId = dormId;

            try {
                // Show confirmation modal and wait for user to confirm
                const confirmed = await this.$refs.modal.show({
                    title: 'Delete Dorm',
                    message: 'Are you sure you want to delete this dorm?',
                    functionName: 'Confirm Delete',

                });

                if (!confirmed) return;

                this.$refs.loader.loading = true;

                const response = await axios.delete(`/DeleteDorm/${this.currentDormId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                this.$refs.loader.loading = false;

                if (response.data.status === "success") {
                    this.fetchDorms(); // Refresh dorm list
                    this.$refs.toast.showToast(response.data.message, 'success');
                } else {
                    this.$refs.toast.showToast('Failed to delete dorm. Please try again.', 'danger');
                }
            } catch (err) {
                this.$refs.loader.loading = false;
                if (err !== false) {
                    console.error("Error deleting dorm:", err);
                    this.$refs.toast.showToast('Server error while deleting.', 'danger');
                }
                // else: user cancelled, no need to show error
            }
        },



        // end deleting functions
        //google map api function
        getAssetPath(path) {
            return `/` + path;
        },
        initMap() {
            const centerPoint = { lat: 10.32, lng: 123.93 };
            const defaultZoom = 13;

            // Invisible polygons for Mandaue and Lapu-Lapu/Mactan
            const mandauePolygon = new google.maps.Polygon({
                paths: [
                    { lat: 10.338, lng: 123.897 },
                    { lat: 10.348, lng: 123.935 },
                    { lat: 10.310, lng: 123.940 },
                    { lat: 10.295, lng: 123.900 },
                ],
                visible: false
            });

            const lapuLapuPolygon = new google.maps.Polygon({
                paths: [
                    { lat: 10.260, lng: 123.945 },
                    { lat: 10.280, lng: 123.945 },
                    { lat: 10.310, lng: 123.920 },
                    { lat: 10.335, lng: 123.945 },
                    { lat: 10.325, lng: 123.975 },
                    { lat: 10.300, lng: 123.980 },
                ],
                visible: false
            });

            const mapStyle = [
                { featureType: "all", elementType: "all", stylers: [{ saturation: -20 }, { lightness: 20 }] },
                { featureType: "poi", stylers: [{ visibility: "off" }] },
                { featureType: "transit", stylers: [{ visibility: "off" }] }
            ];

            const initializeMap = (elementId, initialPosition, isUpdate = false) => {
                const mapElement = document.getElementById(elementId);
                if (!mapElement || mapElement._map) return;

                const map = new google.maps.Map(mapElement, {
                    center: initialPosition,
                    zoom: defaultZoom,
                    styles: mapStyle
                });

                // Add polygons to map (invisible)
                mandauePolygon.setMap(map);
                lapuLapuPolygon.setMap(map);

                const geocoder = new google.maps.Geocoder();

                const updateLocation = (latLng) => {
                    const insideMandaue = google.maps.geometry.poly.containsLocation(latLng, mandauePolygon);
                    const insideLapuLapu = google.maps.geometry.poly.containsLocation(latLng, lapuLapuPolygon);

                    if (!insideMandaue && !insideLapuLapu) {
                        this.$refs.toast.showToast(
                            "Selected location is outside Mandaue, Lapu-Lapu, or Mactan.",
                            "danger"
                        );
                        return false;
                    }

                    geocoder.geocode({ location: latLng }, (results, status) => {
                        if (status === "OK" && results[0]) {
                            const address = results[0].formatted_address;
                            const latitude = latLng.lat();
                            const longitude = latLng.lng();

                            if (isUpdate) {
                                this.editDormData.address = address;
                                this.editDormData.latitude = latitude;
                                this.editDormData.longitude = longitude;
                                this.$refs.toast.showToast(address, "success");
                            } else {
                                this.address = address;
                                this.latitude = latitude;
                                this.longitude = longitude;
                                this.$refs.toast.showToast(address, "success");
                            }
                        }
                    });
                };

                const draggableMarker = new google.maps.Marker({
                    position: initialPosition,
                    map: map,
                    draggable: true,
                    title: "Drag to select location",
                    icon: {
                        url: "https://maps.google.com/mapfiles/ms/icons/yellow-dot.png",
                        scaledSize: new google.maps.Size(50, 50)
                    }
                });

                draggableMarker.addListener("dragend", (event) => {
                    const latLng = event.latLng;
                    const inside = google.maps.geometry.poly.containsLocation(latLng, mandauePolygon) ||
                        google.maps.geometry.poly.containsLocation(latLng, lapuLapuPolygon);

                    if (!inside) {
                        // Snap back to previous valid location
                        draggableMarker.setPosition(new google.maps.LatLng(
                            parseFloat(this.editDormData.latitude) || centerPoint.lat,
                            parseFloat(this.editDormData.longitude) || centerPoint.lng
                        ));
                        this.$refs.toast.showToast(
                            "Cannot place marker outside allowed cities.",
                            "danger"
                        );
                    } else {
                        updateLocation(latLng);
                    }
                });

                mapElement._map = map;
                mapElement._draggableMarker = draggableMarker;
            };

            if (this.VisibleMap) {
                initializeMap("AddMap", centerPoint, false);
            }

            if (this.UpdateVisibleMap) {
                const initialPosition = this.editDormData.latitude && this.editDormData.longitude
                    ? { lat: parseFloat(this.editDormData.latitude), lng: parseFloat(this.editDormData.longitude) }
                    : centerPoint;
                initializeMap("map", initialPosition, true);
            }
        },



        formatDate(dateStr) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString('en-US', options);
        }
    },
    // computed: {
    //     this.fetchDorms();
    // },
    mounted() {
        if (!window.google) {
            const script = document.createElement("script");
            script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBZgqadX1d4wnviOKzUMNStd0DG2X7GA6s&callback=initMap";

            script.async = true;
            script.defer = true;
            // Set up callback
            window.initMap = this.initMap;
            script.onerror = () => {
                console.error("Google Maps failed to load.");
            };
            document.head.appendChild(script);
        } else {
            this.initMap(); // If already loaded
        }
        const element = document.getElementById("landlorddormManagement"); // ✅ define it
        this.landlord_id = element.dataset.landlordId;

        this.fetchDorms();
        this.subscribeToNotifications();
        this.getlandlordVerifiedStatus();
        window.vueInstance = this; // after mounting

    },


    watch: {
        selectedLocation(newVal) {
            if (newVal !== '') {
                this.selectedAvailability = ''; // Reset availability only when location is changed
                this.filterMode = 'location';
                this.handlePagination(1);
            }
        },
        selectedAvailability(newVal) {
            if (newVal !== '') {
                this.selectedLocation = ''; // Reset location only when availability is changed
                this.filterMode = 'availability';
                this.handlePagination(1);
            }
        },
        searchTerm: {
            handler: debounce(function (newVal) {
                this.filterMode = 'search';
                this.handlePagination(1);
            }, 300),
            immediate: false
        },

        VisibleMap(val) {
            if (val) {
                this.$nextTick(() => {
                    this.initMap();
                });
            }
        },
        UpdateVisibleMap(val) {
            if (val) {
                this.$nextTick(() => {
                    this.initMap();
                });
            }
        },
    },

};
</script>

<style scoped>
.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 6px solid #ccc;
    border-top-color: #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    /* smooth scrolling on iOS */
    max-width: 100%;
}

.table-responsive table {
    min-width: 700px;
    /* Adjust based on your table's total width */
}

.file-upload-btn:hover {
    background: #1AA059;
    color: #ffffff;
    transition: all .2s ease;
    cursor: pointer;
}

.file-upload-btn:active {
    border: 0;
    transition: all .2s ease;
}

.file-upload-content {
    display: none;
    text-align: center;
}

.file-upload-input {
    position: absolute;
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    outline: none;
    opacity: 0;
    cursor: pointer;
}

.image-upload-wrap {
    margin-top: 20px;
    border: 4px dashed #4edce2;
    position: relative;
}

.image-dropping,
.image-upload-wrap:hover {
    background-color: #4edce2;
    border: 4px dashed #ffffff;
}

.image-title-wrap {
    padding: 0 15px 15px 15px;
    color: #222;
}

.drag-text {
    text-align: center;
}

.drag-text h3 {
    font-weight: 100;
    text-transform: uppercase;
    color: black;
    padding: 60px 0;
}

.file-upload-image {
    max-height: 200px;
    max-width: 200px;
    margin: auto;
    padding: 20px;
}

.remove-image {
    width: 200px;
    margin: 0;
    color: black;
    background: #4edce2;
    border: none;
    padding: 10px;
    border-radius: 4px;
    border-bottom: 4px solid #b02818;
    transition: all .2s ease;
    outline: none;
    text-transform: uppercase;
    font-weight: 700;
}

.remove-image:hover {
    background: #4edce2;
    color: black;
    transition: all .2s ease;
    cursor: pointer;
}

.remove-image:active {
    border: 0;
    transition: all .2s ease;
}

.file-upload-input {
    display: none;
}

.image-upload-wrap {
    border: 2px dashed #ddd;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    margin-bottom: 20px;
}

.file-upload-content {
    display: block;
    margin-top: 20px;
}

.file-upload-image {
    max-width: 100%;
    max-height: 300px;
    margin: 0 auto;
    display: block;
}

.remove-image {
    background: none;
    border: none;
    color: #ff0000;
    cursor: pointer;
    margin-top: 10px;
}

.image-wrapper {
    width: 100%;
    aspect-ratio: 4 / 3;
    /* You can adjust ratio like 1 / 1 for square */
    overflow: hidden;
    border-radius: 0.5rem;
    background-color: #f8f9fa;
}

.uniform-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}
</style>