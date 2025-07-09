<template>

    <div class="container mt-5">
        <Loader ref="loader" />
        <Toastcomponents ref="toast" />
        <!-- Header -->
        <div class="d-flex justify-content-end align-items-center mb-4">
            <div class="col-md-6 col-lg-4 mb-2 d-flex justify-content-between gap-2">
                <button class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2 flex-fill"
                    @click="viewDormitories">
                    <i class="bi bi-building"></i>
                    View Dormitories
                </button>
                <button class="btn btn-outline-success d-flex align-items-center justify-content-center gap-2 flex-fill"
                    @click="AddModal()">
                    <i class="bi bi-plus-circle"></i>
                    Add New Room
                </button>
            </div>

        </div>

        <!-- Search Bar -->

        <div class="mb-1 d-flex gap-2 flex-wrap justify-content-start w-100">
            <div class="col-md-6 col-lg-2 mb-2">
                <select class="form-select shadow-sm" v-model="selectedRoomType" @change="filterRoomsByRoomType">

                    <option disabled value="">Select room type</option>
                    <option value="all">All Room Types</option>
                    <option value="Single Room">Single Room</option>
                    <option value="Double Room">Double Room</option>
                    <option value="Bedspace / Multi-Sharing Room">Bedspace / Multi-Sharing Room</option>
                    <option value="Studio-Type Room">Studio-Type Room</option>
                    <option value="Partitioned Bedspace (Cubicle Style)">Partitioned Bedspace (Cubicle Style)</option>
                    <option value="Loft Room / Mezzanine Type">Loft Room / Mezzanine Type</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-2 mb-2">
                <select class="form-select shadow-sm" v-model="selectedAvailability"
                    @change="filterRoomsByAvailability">

                    <option disabled value="">Select Availability</option>
                    <option value="all">All Availability</option>
                    <option value="Available">Available</option>
                    <option value="Not Available">Not Available</option>
                    <option value="Under Maintenance">Under Maintenance</option>

                </select>
            </div>
            <div class="col-md-6 col-lg-2 mb-2">
                <select class="form-select shadow-sm" v-model="selectedGender" @change="filterRoomsByGender"
                    :value="selectedGender">

                    <option disabled value="">Select gender preferences</option>
                    <option value="all">All Preferences</option>
                    <option value="Male Only">Male </option>
                    <option value="Female Only">Female </option>
                    <option value="Any Gender">Any Gender</option>


                </select>
            </div>
            <div class="col-md-6 col-lg-2 mb-2">
                <select class="form-select shadow-sm" v-model="selectedDormitory" @change="filterRoomsByDormitory">

                    <option disabled value="">Select Dormitories</option>
                    <option value="all">All Dormitories</option>
                    <option v-for="dorm in dorms" :key="dorm.dormID" :value="dorm.dormID">
                        <span>{{ dorm.dormName }}</span>
                    </option>
                </select>
            </div>


        </div>
        <!-- Table -->
        <div class="table-responsive shadow-sm rounded mt-4">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Room ID</th>
                        <th>Room No</th>
                        <th>Room Type</th>
                        <th>Price (₱)</th>
                        <th>Availability Status</th>
                        <th>Gender Preferences</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody v-if="rooms && rooms.length">
                    <tr class="text-center align-middle" v-for="room in rooms" :key="room.roomID">
                        <td>{{ room.roomID }}</td>
                        <td>{{ room.roomNumber }}</td>
                        <td>{{ room.roomType }}</td>
                        <td>{{ room.price }}</td>
                        <td>
                            <span class="badge"
                                :class="room.availability === 'Available' ? 'bg-success' : room.availability === 'Occupied' ? 'bg-danger' : 'bg-warning'">
                                {{ room.availability }}
                            </span>
                        </td>
                        <td>{{ room.genderPreference }}</td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-success d-flex align-items-center gap-1"
                                    @click="ViewRoom(room.roomID)">
                                    <i class="bi bi-eye"></i>

                                </button>
                                <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                    @click="editRoom(room.roomID)">
                                    <i class="bi bi-pencil-square"></i>

                                </button>
                                <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                                    @click="deleteRoom(room.roomID)">
                                    <i class="bi bi-trash"></i>

                                </button>
                            </div>

                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
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

        <!--modal add room-->
        <div v-if="VisibleAddModal" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4">
                    <div class="modal-header bg-outline-primary text-black">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>
                            Add Room
                        </h5> <button type="button" class="btn-close" @click="VisibleAddModal = false"></button>
                    </div>

                    <div class="modal-body px-3 px-md-4">
                        <!-- Dorm Selector -->
                        <div class="mb-4 d-flex align-items-center gap-2">
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-lg" type="button">Dorm Name</button>
                                <button type="button"
                                    class="btn btn-lg btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li v-for="dorm in dorms" :key="dorm.dormID">
                                        <a class="dropdown-item d-flex justify-content-between align-items-center"
                                            href="#" @click.prevent="dormId(dorm)">
                                            <span>{{ dorm.dormName }}</span>
                                            <span class="badge bg-secondary">ID: {{ dorm.dormID }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="border border-secondary rounded-3 p-4 text-center" v-if="VisibleImage"
                            style="cursor: pointer;" @click="triggerroomImagePreview3">
                            <input ref="RoomsImages3Input" class="d-none" type="file" accept="image/*"
                                @change="handleroomImagePreview3" />

                            <!-- Icon + Text -->
                            <div class="d-flex flex-column align-items-center text-center mb-3">
                                <h5 class="text-secondary mt-2">Click to Upload Room Image</h5>
                                <small class="text-muted">Click to browse and select an image file</small>
                            </div>
                        </div>
                        <div class="mb-3 d-flex justify-content-center align-items-center">
                            <span class="text-danger small mb-3" v-if="errors.roomImageFile">{{ errors.roomImageFile[0]
                                }}</span>
                        </div>


                        <!-- Image Preview -->
                        <div v-if="roomImagePreview" class="text-center mb-3">
                            <img :src="roomImagePreview" alt="Uploaded Room Image" class="img-fluid rounded mb-2"
                                style="max-height: 250px;" />
                            <div>
                                <button type="button" @click="removeroomImagePreviews3" class="btn btn-danger btn-sm">
                                    Remove Uploaded Image
                                </button>
                            </div>
                        </div>
                        <div class="row g-4">
                            <!-- Column 1 -->
                            <div class="col-md-6">
                                <div class="form-floating mb-2">
                                    <input type="text" class="form-control" readonly placeholder="Dorm ID"
                                        v-model="dormsId">
                                    <label>Dorm ID</label>
                                </div>
                                <span class="text-danger small" v-if="errors.dormsId">{{ errors.dormsId[0]
                                    }}</span>

                                <div class="form-floating mb-2 mt-2">
                                    <input type="text" class="form-control" id="roomNumber" placeholder="Room Number"
                                        v-model="roomNumber" @input="roomNumber = roomNumber.replace(/[^0-9]/g, '')" />
                                    <label for="roomNumber">Room Number</label>
                                </div>
                                <span class="text-danger small" v-if="errors.roomNumber">{{ errors.roomNumber[0]
                                    }}</span>

                                <div class="form-floating mb-2 mt-2">
                                    <select class="form-select" id="roomType" v-model="roomType">
                                        <option value="" disabled selected>Select Room Type</option>
                                        <option value="Single Room">Single Room</option>
                                        <option value="Double Room / Shared Room">Double Room / Shared Room</option>
                                        <option value="Bedspace / Multi-Sharing Room">Bedspace / Multi-Sharing Room
                                        </option>
                                        <option value="Studio-Type Room">Studio-Type Room</option>
                                        <option value="Partitioned Bedspace (Cubicle Style)">Partitioned Bedspace
                                            (Cubicle Style)</option>
                                        <option value="Loft Room / Mezzanine Type">Loft Room / Mezzanine Type
                                        </option>
                                    </select>
                                    <label for="roomType">Room Type</label>
                                </div>
                                <span class="text-danger small" v-if="errors.roomType">{{ errors.roomType[0]
                                    }}</span>

                                <div class="form-floating mb-2 mt-2">
                                    <select class="form-select" id="availability" v-model="availability">
                                        <option value="" selected>Select Availability</option>
                                        <option v-for="slot in availibilityArray" :key="slot" :value="slot">
                                            {{ slot }}
                                        </option>
                                    </select>
                                    <label for="availability">Availability Status</label>
                                </div>
                                <span class="text-danger small" v-if="errors.availability">{{ errors.availability[0]
                                    }}</span>
                                <div class="form-floating mb-2 mt-2">
                                    <input type="number" class="form-control" id="areaSqm" v-model="area_sqm" min="1"
                                        placeholder="Enter area in sqm" required>
                                    <label for="areaSqm">Area (sqm)</label>
                                </div>
                                <span class="text-danger small" v-if="errors.area_sqm">{{ errors.area_sqm[0]
                                    }}</span>

                            </div>

                            <!-- Column 2 -->
                            <div class="col-md-6">
                                <div class="form-floating mb-2">
                                    <input type="number" class="form-control" id="price" placeholder="Price"
                                        v-model="price" min="0" step="0.01">
                                    <label for="price">Price (₱)</label>
                                </div>
                                <span class="text-danger small" v-if="errors.price">{{ errors.price[0]
                                    }}</span>
                                <div class="form-floating mb-2 mt-2">
                                    <select class="form-select" id="bedType" v-model="listing_type" required>
                                        <option value="" disabled selected>Select Bed Type</option>
                                        <option v-for="bed in filteredBeds" :key="bed" :value="bed">
                                            {{ bed }}
                                        </option>
                                    </select>
                                    <label for="bedType">Bed Type</label>
                                </div>

                                <span class="text-danger small" v-if="errors.listing_type">{{ errors.listing_type[0]
                                    }}</span>
                                <div class="form-floating mb-2 mt-2">
                                    <select class="form-select" id="furnishingStatus" v-model="furnishing_status"
                                        required>
                                        <option value="" disabled selected>Select Furnishing Status</option>
                                        <option value="Fully Furnished">Fully Furnished</option>
                                        <option value="Semi Furnished">Semi Furnished</option>
                                        <option value="Unfurnished">Unfurnished</option>
                                    </select>
                                    <label for="furnishingStatus">Furnishing Status</label>
                                </div>
                                <span class="text-danger small" v-if="errors.furnishing_status">{{
                                    errors.furnishing_status[0]
                                    }}</span>

                                <div class="form-floating mb-2 mt-2">
                                    <select class="form-select" id="genderPreference" v-model="gender_preference"
                                        required>
                                        <option value="" disabled selected>Select Gender Preference</option>
                                        <option value="Male Only">Male Only</option>
                                        <option value="Female Only">Female Only</option>
                                        <option value="Any Gender">Any Gender</option>
                                    </select>
                                    <label for="genderPreference">Gender Preference</label>
                                </div>
                                <span class="text-danger small" v-if="errors.gender_preference">{{
                                    errors.gender_preference[0]
                                    }}</span>


                                <span class="text-danger small" v-if="errors.price">{{ errors.price[0] }}</span>


                            </div>
                            <div class="d-grid gap-2 mt-4">

                                <button type="submit" @click="submitRoom" class="btn btn-outline-primary btn-lg">
                                    Submit Room
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <Toastcomponents ref="toast" />

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" @click="VisibleAddModal = false">Close</button>
                </div>
            </div>
        </div>

    </div>
    <div class="modal fade show d-block w-100" v-if="visibleRoomFeaturesModal" tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);" @click.self="visibleRoomFeaturesModal = false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header  text-dark rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-plus text-primary"></i>
                        Add Room Features <span class="text-muted small">(Optional)</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-dark" @click="visibleRoomFeaturesModal = false"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div v-for="(feature, index) in roomFeatures" :key="index" class="form-floating mb-3">
                        <input type="text" class="form-control mb-2" v-model="roomFeatures[index]"
                            :id="'feature' + index" placeholder="Enter room feature" />
                        <label :for="'feature' + index">Room Feature {{ index + 1 }}</label>
                        <span class="text-danger mb-3 " v-if="errors.roomFeatures">{{ errors.roomFeatures[0]
                            }}</span>

                    </div>

                    <button class="btn btn-primary mb-4" @click="addRoomFeatures"
                        :disabled="addRoomFeatures.length >= 4"
                        :title="addRoomFeatures.length >= 4 ? 'Max 4 features allowed' : 'Add Room Feature'">
                        <i class="fa-solid fa-plus"></i> Add Room Feature
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!--Delete Modal--->
    <div v-if="VisibleDeleteModal" class="modal fade show d-block w-100" tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);" @click.self="VisibleDeleteModal = false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header  text-danger rounded-top-4">
                    <h5 class="modal-title fw-bold">Delete Confirmation</h5>
                    <button type="button" class="btn-close btn-close-black" @click="VisibleDeleteModal = false"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-3"></i>
                    <p class="fs-5">Are you sure you want to delete this Room? This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4"
                        @click="VisibleDeleteModal = false">Cancel</button>
                    <button type="button" class="btn btn-outline-danger px-4" @click="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <!---Update Modal-->
    <div v-if="VisibleUpdateModal" class="modal fade show d-block w-100" tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4">
                <div class="modal-header  text-black">
                    <h5 class="modal-title">Update Room</h5>
                    <button type="button" class="btn-close" @click="VisibleUpdateModal = false"></button>
                </div>

                <div class="modal-body px-3 px-md-4">
                    <!-- Dorm Selector -->
                    <div class="mb-4 d-flex align-items-center gap-2">

                    </div>
                    <div class="border border-secondary rounded-3 p-4 mb-3 text-center" style="cursor: pointer;"
                        @click="edittriggerroomImagePreview3">
                        <input ref="editRoomsImages3Input" class="d-none" type="file" accept="image/*"
                            @change="edithandleroomImagePreview3" />

                        <!-- Icon + Text -->
                        <div class="d-flex flex-column align-items-center text-center mb-3">
                            <h5 class="text-secondary mt-2">Click to Update Room Image</h5>
                            <small class="text-muted">Click to browse and select an image file</small>
                        </div>
                    </div>

                    <!-- Image Preview -->
                    <div v-if="editData.roomImagePreview" class="text-center mb-3 " style="height: 350px;">
                        <img :src="editData.roomImagePreview" alt="Uploaded Room Image" class="img-fluid rounded mb-2"
                            style="max-height: 350px; width: 100%;" />

                    </div>
                    <div class="row g-4">
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" readonly placeholder="Dorm ID"
                                    v-model="editData.fkdormID">
                                <label>Dorm ID</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="roomNumber" placeholder="Room Number"
                                    v-model="editData.roomNumber">
                                <label for="roomNumber">Room Number</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.roomNumber">{{
                                errors.editData.roomNumber[0]
                            }}</span>
                            <div class="form-floating mb-2 mt-2">
                                <input type="number" class="form-control" id="areaSqm" v-model="editData.areaSqm"
                                    min="1" placeholder="Enter area in sqm" required>
                                <label for="areaSqm">Area (sqm)</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.areaSqm">{{
                                errors.editData.areaSqm[0]
                                }}</span>


                            <div class="form-floating mb-2 mt-2">
                                <select class="form-select" id="room_type" v-model="editData.roomType">
                                    <option value="" disabled>
                                        Select Room Type</option>
                                    <option value="Single Room">Single Room</option>
                                    <option value="Double Room / Shared Room">Double Room / Shared Room</option>
                                    <option value="Bedspace / Multi-Sharing Room">Bedspace / Multi-Sharing Room
                                    </option>
                                    <option value="Studio-Type Room">Studio-Type Room</option>
                                    <option value="Partitioned Bedspace (Cubicle Style)">Partitioned Bedspace
                                        (Cubicle
                                        Style)</option>
                                    <option value="Loft Room / Mezzanine Type">Loft Room / Mezzanine Type</option>
                                </select>
                                <label for="room_type">Room Type</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.roomType">{{
                                errors.editData.roomType[0]
                            }}</span>
                            <div class="form-floating mb-2">
                                <select class="form-select" id="availability" v-model="editData.availability">
                                    <option value="" selected>Select Availability</option>
                                    <option v-for="slot in editavailibilityArray" :key="slot" :value="slot">
                                        {{ slot }}
                                    </option>
                                </select>
                                <label for="availability">Availability Status</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.availability">{{
                                errors.editData.availability[0]
                                }}</span>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <div class="form-floating mb-2">
                                <input type="number" class="form-control" id="price" placeholder="Price"
                                    v-model="editData.price" min="0" step="0.01">
                                <label for="price">Price (₱)</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.price">{{
                                errors.editData.price[0]
                                }}</span>
                            <div class="form-floating mb-2 mt-2">
                                <select class="form-select" id="bedType" v-model="editData.listingType" required>
                                    <option value="" disabled>Select Bed Type</option>
                                    <option v-for="bed in editfilteredBeds" :key="bed" :value="bed">
                                        {{ bed }}
                                    </option>
                                </select>
                                <label for="bedType">Bed Type</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.listingType">{{
                                errors.editData.listingType[0]
                                }}</span>

                            <div class="form-floating mb-2 mt-2">
                                <select class="form-select" id="furnishingStatus" v-model="editData.furnishing_status"
                                    required>
                                    <option value="" disabled selected>Select Furnishing Status</option>
                                    <option value="Fully Furnished">Fully Furnished</option>
                                    <option value="Semi Furnished">Semi Furnished</option>
                                    <option value="Unfurnished">Unfurnished</option>
                                </select>
                                <label for="furnishingStatus">Furnishing Status</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.furnishing_status">{{
                                errors.editData.furnishing_status[0]
                                }}</span>
                            <div class="form-floating mb-2 mt-2">
                                <select class="form-select" id="genderPreference" v-model="editData.genderPreference"
                                    required>
                                    <option value="" disabled selected>Select Gender Preference</option>
                                    <option value="Male Only">Male Only</option>
                                    <option value="Female Only">Female Only</option>
                                    <option value="Any Gender">Any Gender</option>
                                </select>
                                <label for="genderPreference">Gender Preference</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.genderPreference">{{
                                errors.editData.genderPreference[0]
                            }}</span>
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" @click="updateRoom" class="btn btn-outline-primary btn-lg">
                                    Update Room
                                </button>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="Optional" v-model="newRoomFeature"
                                    placeholder="Optional Add Rooms Features">
                                <label for="Optional">(Optional) Add Rooms Features</label>
                            </div>
                            <span class="text-danger small" v-if="errors.newRoomFeature">
                                {{ errors.newRoomFeature[0] }}
                            </span>
                            <div class="mb-2">
                                <button type="button" class="btn btn-outline-secondary w-100"
                                    @click="addnewRoomFeatures()">
                                    <i class="fas fa-plus me-2"></i> Add Room Features
                                </button>
                            </div>
                            <div class="border rounded p-2">
                                <div class="row fw-bold border-bottom py-2 text-center">
                                    <div class="col">Room Feature</div>
                                    <div class="col-3">Actions</div>
                                </div>

                                <div v-for="feature in editData.features" :key="feature.id"
                                    class="row align-items-center py-2 border-bottom text-center">
                                    <div class="col">
                                        <input type="text" readonly class="form-control text-center"
                                            v-model="feature.featureName" placeholder="Feature name" />
                                    </div>
                                    <div class="col-3 d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-danger"
                                            @click.prevent="deleteRoomFeatures(feature.id)">
                                            Delete
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>



                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" @click="VisibleUpdateModal = false">Close</button>
                </div>
            </div>

        </div>
    </div>
    <!--Display Data Modal-->
    <div v-if="VisibleDisplayDataModal" class="modal fade show d-block w-100" tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);" @click.self="VisibleDisplayDataModal = false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <!-- Modal Header -->
                <div class="modal-header bg-outline-primary text-black  rounded-top-4">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Room Details
                    </h5> <button type="button" class="btn-close btn-close-black"
                        @click="VisibleDisplayDataModal = false" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding: 1.5rem;">
                    <img :src="selectedRoom?.roomImages" class="img-fluid mb-3 rounded " alt="Room Image"
                        style="width: 100%;  height: 300px; object-fit: cover;">
                    <div class="row g-4 ">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Room Number:</label>
                                <div class="p-2 border rounded bg-light text-break">{{ selectedRoom?.roomNumber
                                    }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Room Type:</label>
                                <div class="p-2 border rounded bg-light text-break">{{ selectedRoom?.roomType
                                    }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Price:</label>
                                <div class="p-2 border rounded bg-light text-break">₱{{ selectedRoom?.price }}
                                </div>
                            </div>

                            <label class="form-label fw-bold">Dorm Name:</label>
                            <div class="p-2 border rounded bg-light text-break w-100"
                                style="max-height: 100px;  overflow-y: auto;">
                                {{ selectedRoom?.dorm.dormName
                                }}

                            </div>
                            <label class="form-label fw-bold">Room Features:</label>
                            <div>
                                <div class="d-flex flex-wrap gap-2">
                                    <span v-for="feature in selectedRoom?.features" :key="feature.id"
                                        class="badge bg-primary text-white px-3 py-2 shadow-sm"
                                        style="font-size: 0.9rem;">
                                        {{ feature.featureName }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 ">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Availability:</label>
                                <div class="p-2 border rounded bg-light text-break">
                                    <span class="badge"
                                        :class="selectedRoom?.availability === 'Available' ? 'bg-success' : selectedRoom?.availability === 'Occupied' ? 'bg-danger' : 'bg-warning'">
                                        {{ selectedRoom?.availability }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Contact Email:</label>
                                <div class="p-2 border rounded bg-light text-break"> {{
                                    selectedRoom?.dorm.contactEmail
                                    }}

                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Contact Phone:</label>
                                <div class="p-2 border rounded bg-light text-break">{{
                                    selectedRoom?.dorm.contactPhone
                                    }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Registration Date:</label>
                                <div class="p-2 border rounded bg-light text-break">2023-01-01</div>

                                <div class="mb-0 mt-3">

                                    <label class="form-label fw-bold">Address:</label>
                                    <div class="p-2 border rounded bg-light text-break w-100"
                                        style="max-height: 100px;  overflow-y: auto;">
                                        {{ selectedRoom?.dorm.address
                                        }}

                                    </div>

                                </div>
                            </div>

                        </div>


                    </div>
                </div>

                <!-- Modal Footer -->

            </div>
        </div>
    </div>


    <!--Images Post Modal-->


    <Toastcomponents ref="toast" />

    <Modalconfirmation ref="modal" />

</template>

<script>
import axios from 'axios';
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
            VisibleAddModal: false,
            VisibleDeleteModal: false,
            VisibleUpdateModal: false,
            VisibleDisplayDataModal: false,
            VisibleImage: false,
            selectedDormitory: '',
            selectedGender: '',
            selectedAvailability: '',
            selectedRoomType: '',
            currentPage: 1,
            lastPage: 1,
            dorms: window.allRooms || [],
            dormsId: "",
            roomNumber: "",
            roomType: "",
            availability: '',
            availibilityArray: ['Available'],
            editavailibilityArray: ['Available', 'Under Maintenance'],
            price: "",
            listing_type: '',
            bedOptions: {
                'Single Room': ['Single Bed', 'Private Room with Bed'],
                'Double Room / Shared Room': ['Single Bed', 'Shared Bed'],
                'Bedspace / Multi-Sharing Room': ['Bunk Bed', 'Double Deck (Lower)', 'Double Deck (Upper)'],
                'Studio-Type Room': ['Private Room with Bed'],
                'Partitioned Bedspace (Cubicle Style)': ['Single Bed'],
                'Loft Room / Mezzanine Type': ['Single Bed', 'Bunk Bed']
            },
            area_sqm: '',
            gender_preference: '',
            furnishing_status: '',
            roomImagePreview: '',
            roomImageFile: '',
            getRoomID: '',
            editSelectedID: '',
            roomFeatures: [''],
            visibleRoomFeaturesModal: false,
            editData:
            {
                fkdormID: '',
                roomNumber: '',
                roomType: '',
                availability: '',
                price: '',
                amenities: '',
                listingType: '',
                bedOptions: {
                    'Single Room': ['Single Bed', 'Private Room with Bed'],
                    'Double Room / Shared Room': ['Single Bed', 'Shared Bed'],
                    'Bedspace / Multi-Sharing Room': ['Bunk Bed', 'Double Deck (Lower)', 'Double Deck (Upper)'],
                    'Studio-Type Room': ['Private Room with Bed'],
                    'Partitioned Bedspace (Cubicle Style)': ['Single Bed'],
                    'Loft Room / Mezzanine Type': ['Single Bed', 'Bunk Bed']
                },
                areaSqm: '',
                genderPreference: '',
                roomImagePreview: '',
                roomImageFile: '',
            },
            errors: {},
            rooms: [],
            selectedRoom: {},
            newRoomFeature: '',
            currentRoomID: null,
        };
    },
    methods: {
        dormId(dorm) {
            this.dormsId = dorm.dormID;
        },
        handlePagination(page) {
            if (page < 1 || page > this.lastPage) return;
            this.currentPage = page;

            // 👇 Priority check (only one filter is expected at a time)
            if (this.selectedAvailability && this.selectedAvailability !== 'all') {
                this.filterRoomsByAvailability(page);
            } else if (this.selectedDormitory && this.selectedDormitory !== 'all') {
                this.filterRoomsByDormitory(page);
            } else if (this.selectedGender && this.selectedGender !== 'all') {
                this.filterRoomsByGender(page);
            } else if (this.selectedRoomType && this.selectedRoomType !== 'all') {
                this.filterRoomsByRoomType(page);
            } else {
                this.fetchRooms(page);
            }
        },

        async fetchRooms(page = 1) {
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get('/ListRooms', {
                    params: { page: page },
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                this.rooms = response.data.rooms.data;
                this.currentPage = response.data.rooms.current_page;
                this.lastPage = response.data.rooms.last_page;
                this.$refs.loader.loading = false;

            } catch (error) {
                console.error('Error fetching rooms:', error);
            }
        },
        async filterRoomsByDormitory(page = 1) {
            try {
                this.$refs.loader.loading = true;
                if (this.selectedDormitory === 'all') {
                    this.fetchRooms(1);
                } else {
                    const response = await axios.get(`/get-rooms-by-dorm/${this.selectedDormitory}`,
                        {
                            params: { page: page },
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                    this.rooms = response.data.rooms.data;
                    this.currentPage = response.data.rooms.current_page;
                    this.lastPage = response.data.rooms.last_page;
                    this.selectedAvailability = '';
                    this.selectedGender = '';
                    this.selectedRoomType = '';
                    this.$refs.loader.loading = false;

                }
            }
            catch (error) {

            }
        },
        async filterRoomsByGender(page = 1) {
            try {
                this.$refs.loader.loading = true;
                if (this.selectedGender === 'all') {
                    this.fetchRooms(1);
                    return;
                }
                const response = await axios.get(`/get-rooms-by-gender/${this.selectedGender}`, {
                    params: { page: page },
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                this.selectedAvailability = '';
                this.selectedRoomType = '';
                this.selectedDormitory = '';
                this.rooms = response.data.rooms.data;
                this.currentPage = response.data.rooms.current_page;
                this.lastPage = response.data.rooms.last_page;
                this.$refs.loader.loading = false;

            } catch (error) {
                console.error('Error fetching rooms by gender:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async filterRoomsByAvailability(page = 1) {
            try {
                this.$refs.loader.loading = true;
                if (this.selectedAvailability === 'all') {
                    await this.fetchRooms(1);
                    return;
                }
                const response = await axios.get(`/get-rooms-by-availability/${this.selectedAvailability}`, {
                    params: { page: page },
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                this.rooms = response.data.rooms.data;
                this.currentPage = response.data.rooms.current_page;
                this.lastPage = response.data.rooms.last_page;
                this.selectedGender = '';
                this.selectedRoomType = '';
                this.selectedDormitory = '';
                this.$refs.loader.loading = false;

            } catch (error) {
                console.error('Error fetching rooms by availability:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async filterRoomsByRoomType(page = 1) {
            try {
                this.$refs.loader.loading = true;
                if (this.selectedRoomType === 'all') {
                    this.fetchRooms(1);
                    return;
                }
                const response = await axios.get('/get-rooms-by-room-type', {
                    params: { page: page, roomType: this.selectedRoomType },
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                this.selectedAvailability = '';
                this.selectedGender = '';
                this.selectedDormitory = '';
                this.rooms = response.data.rooms.data;
                this.currentPage = response.data.rooms.current_page;
                this.lastPage = response.data.rooms.last_page;
                this.$refs.loader.loading = false;

            } catch (error) {
                console.error('Error fetching rooms by room type:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        clearFiltered() {
            this.selectedAvailability = '';
            this.selectedDormitory = '';
            this.selectedGender = '';
            this.selectedRoomType = '';
        },
        AddModal() {
            this.VisibleAddModal = true;
            this.VisibleImage = true;
            this.emptyfill();
            this.clearFiltered();
        },
        handleroomImagePreview3(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.roomImagePreview) {
                    URL.revokeObjectURL(this.roomImagePreview);
                }
                this.roomImageFile = file;
                this.VisibleImage = false;
                this.roomImagePreview = URL.createObjectURL(file);
            }
        },
        triggerroomImagePreview3() {
            if (this.$refs.RoomsImages3Input) {
                this.$refs.RoomsImages3Input.click();
            }
        },

        removeroomImagePreviews3() {
            if (this.roomImagePreview) {
                URL.revokeObjectURL(this.roomImagePreview);
            }
            this.roomImagePreview = null;
            this.roomImageFile = '';
            this.VisibleImage = true;

            // Add null check for safety
            if (this.$refs.roomImagePreview) {
                this.$refs.roomImagePreview.value = ''; // Reset file input
            }
        },

        async submitRoom() {
            const confirmed = await this.$refs.modal.show({
                title: 'Confirm New Room',
                message: 'Do you want to add this room now?',
                functionName: 'Add Room'
            });

            if (!confirmed) {
                this.$refs.loader.loading = false;
                return;
            }
            this.$refs.loader.loading = true;

            try {
                const formData = new FormData();
                formData.append('dormsId', this.dormsId);
                formData.append('roomNumber', this.roomNumber);
                formData.append('roomType', this.roomType);
                formData.append('availability', this.availability);
                formData.append('price', this.price);
                formData.append('listing_type', this.listing_type);
                formData.append('area_sqm', this.area_sqm);
                formData.append('gender_preference', this.gender_preference);
                formData.append('furnishing_status', this.furnishing_status);
                formData.append('roomImageFile', this.roomImageFile);
                const response = await axios.post('/addRoom', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === "success") {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.$refs.loader.loading = false;
                    this.fetchRooms();
                    this.emptyfill();
                    this.getRoomID = response.data.room_id;
                    console.log(this.getRoomID);
                    this.roomFeaturesModal();
                    this.VisibleAddModal = false;
                } else if (response.data.errors) {
                    this.$refs.loader.loading = false;
                    this.errors = response.data.errors;
                }

            } catch (error) {
                this.$refs.loader.loading = false;

                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    this.$refs.loader.loading = false;

                    console.error('Unexpected error:', error);
                    this.$refs.toast.showToast('Something went wrong.', 'error');
                }
            }
        },
        roomFeaturesModal() {
            this.visibleRoomFeaturesModal = true;
            this.errors = {};
        },
        async addRoomFeatures() {
            const formData = new FormData();
            formData.append('features', this.roomFeatures.join(','));
            formData.append('room_id', this.getRoomID);
            console.log(this.getRoomID);
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Adding Room Features',
                    message: `Are you sure you want to Add Room Features?`,
                    functionName: 'Add new Room Features (Optional)'
                });

                if (!confirmed) {
                    this.$refs.loader.loading = false;
                    return;
                }

                this.$refs.loader.loading = true;

                const response = await axios.post('/add-roomfeatures', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.roomFeatures = ['']; // Reset room features
                    this.errors = {};
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

        async ViewRoom(roomId) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/ViewRoom/${roomId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;
                    this.fetchRooms();
                    this.selectedRoom = response.data.room;
                    this.VisibleDisplayDataModal = true;
                } else {
                    console.error("Failed to fetch dorm details:", response.data.message);

                }
            } catch (error) {
                console.error("Error fetching dorm details:", error);
            }

        },
        async editRoom(roomId) {

            this.$refs.loader.loading = true;

            try {
                const response = await axios.get(`/ViewRoom/${roomId}`);
                if (response.data.status === "success") {
                    this.$refs.loader.loading = false;
                    this.editData = {

                        ...response.data.room,
                        room_id: roomId,
                        roomImagePreview: response.data.room.roomImages || "",


                    };

                    this.VisibleUpdateModal = true;
                    this.editSelectedID = roomId;

                } else {
                    console.error("Failed to fetch dorm details:", response.data.message);
                    alert("Failed to load dorm details for editing");
                }
            } catch (error) {
                console.error("Error fetching dorm details:", error);
                alert("An error occurred while loading dorm details");
            }
        },
        async deleteRoomFeatures(id) {
            this.rulesId = id;
            const confirmed = await this.$refs.modal.show({
                title: 'Delete Feature',
                message: 'Are you sure you want to delete this Feature?',
                functionName: 'Delete Feature',

            });

            if (!confirmed) return;

            this.$refs.loader.loading = true;
            if (this.rulesId) {

                try {
                    const response = await axios.delete(`/delete-roomfeatures/${this.rulesId}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    this.$refs.loader.loading = false;

                    if (response.data.status === "success") {
                        this.$refs.toast.showToast(response.data.message, 'success');
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
        async addnewRoomFeatures() {


            this.errors = {};
            const confirmed = await this.$refs.modal.show({
                title: 'Adding New Room Feature',
                message: `Are you sure you want to add this room feature?`,
                functionName: 'Add new Room Feature (Optional)'
            });

            this.$refs.loader.loading = true;
            try {
                const response = await axios.post('/add-roomfeatures', {
                    features: this.newRoomFeature,
                    room_id: this.editData.room_id,
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.data.status === 'success') {
                    this.$refs.loader.loading = false;
                    this.$refs.toast.showToast(`${this.newRoomFeature} added successfully.`, 'success');
                    this.VisibleUpdateModal = false;
                    this.newRoomFeature = '';
                    this.errors = {};
                } else if (response.data.errors) {
                    this.errors.newRoomFeature = [response.data.message];
                }

            } catch (error) {
                this.$refs.loader.loading = false;

                // Show Laravel validation or custom error message
                if (error.response && error.response.data) {
                    const res = error.response.data;

                    if (res.errors && res.errors.newRoomFeature) {
                        this.errors.newRoomFeature = res.errors.newRoomFeature;
                    } else if (res.message) {
                        this.errors.newRoomFeature = [res.message];

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

        edithandleroomImagePreview3(event) {
            const file = event.target.files[0];
            if (file) {
                // Update both preview and file
                this.editData.roomImagePreview = URL.createObjectURL(file);
                this.editData.roomImageFile = file;
            } else {
                // If no file is selected, reset to existing preview and clear file
                this.editData.roomImageFile = null;
            }
        },
        edittriggerroomImagePreview3() {
            if (this.$refs.editRoomsImages3Input) {
                this.$refs.editRoomsImages3Input.click();
            }
        },
        async updateRoom() {
            const confirmed = await this.$refs.modal.show({
                title: 'Confirm Update',
                message: 'Are you sure you want to update the details of this room? This action will overwrite the existing information.',
                functionName: 'Update Room'
            });

            if (!confirmed) {
                this.$refs.loader.loading = false;
                return;
            }

            this.$refs.loader.loading = true;

            const formData = new FormData();

            formData.append('dormitory_id', this.editData.fkdormID);
            formData.append('room_number', this.editData.roomNumber);
            formData.append('room_type', this.editData.roomType);
            formData.append('availability', this.editData.availability);
            formData.append('price', this.editData.price);
            formData.append('listing_type', this.editData.listingType);
            formData.append('area_sqm', this.editData.areaSqm);
            formData.append('gender_preference', this.editData.genderPreference);
            formData.append('furnishing_status', this.editData.furnishing_status);
            if (this.editData.roomImageFile) {
                // User selected a new image file, send it
                formData.append('roomImageFile', this.editData.roomImageFile);
                formData.append('isNewImage', 'true'); // Optional flag to indicate new image uploaded
            } else if (this.editData.roomImagePreview) {
                // No new file, send the current image path or name to keep it
                formData.append('existingImage', this.editData.roomImagePreview);
                formData.append('isNewImage', 'false'); // Optional flag
            } else {
                // No image at all — handle accordingly (maybe send empty or alert user)
                formData.append('existingImage', '');
                formData.append('isNewImage', 'false');
            }
            try {
                const response = await axios.post(`/update-room/${this.editSelectedID}`, formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),


                    },
                });

                if (response.data.status === "success") {
                    this.handlePagination(this.currentPage);
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.VisibleUpdateModal = false;
                    this.errors = {};
                } else if (response.data.errors) {
                    this.errors = response.data.errors;
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = {
                        editData: error.response.data.errors
                    };
                    this.$refs.toast.showToast('Please check your fields', 'danger');

                    this.$refs.loader.loading = false;
                }
            } finally {
                this.$refs.loader.loading = false;
            }



        },
        async deleteRoom(roomId) {
            this.currentRoomID = roomId;
            const confirmed = await this.$refs.modal.show({
                title: 'Delete Room?',
                message: 'This will permanently remove the room. Proceed?',
                functionName: 'Delete Room'
            });

            if (!confirmed) {
                this.$refs.loader.loading = false;
                return;
            }
            this.$refs.loader.loading = true; // Show loading indicator
            try {
                const response = await axios.delete(`/DeleteRoom/${this.currentRoomID}`, {}, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === "success") {
                    this.fetchRooms();

                    this.$refs.loader.loading = false;

                    this.$refs.toast.showToast(response.data.message, 'success');

                    this.VisibleDeleteModal = false; // Close the modal
                }
                else {
                    this.$refs.toast.showToast(response.data.message, 'error');
                }
            }
            catch (error) {
                console.error('Error deleting room:', error);
                this.$refs.toast.showToast('Failed to delete room.', 'error');
            } finally {
                this.$refs.loader.loading = false; // Hide loading indicator

            }
        },
        updateEmpty() {
            this.editData = {
                room_id: '',
                room_number: '',
                room_type: '',
                availability: '',
                price: '',
                listing_type: '',
                area_sqm: '',
                gender_preference: '',
                roomImagePreview: '',
                roomImageFile: '',

            };
            this.errors = {};
        },
        emptyfill() {

            this.dormsId = "";
            this.roomNumber = "";
            this.roomType = "";
            this.availability = '';
            this.price = "";
            this.furnishing_status = "";
            this.listing_type = '';
            this.area_sqm = '';
            this.gender_preference = '';
            this.roomImagePreview = '';
            this.roomImageFile = '';
            this.errors = {};
        }




    },
    mounted() {
        this.handlePagination();
        // this.fetchRooms();
    },
    watch:
    {
        ListRooms(newVal) {
            if (newVal !== '') {
                this.filterMode = 'list';
                this.handlePagination(1);
            }
        },
        selectedDormitory(newVal) {
            this.filterRoomsByDormitory(newVal);
        },
        selectedGender(newVal) {
            this.filterRoomsByGender(newVal);
        },
        selectedAvailability(newVal) {
            this.filterRoomsByAvailability(newVal);
        },
        selectedRoomType(newVal) {
            this.filterRoomsByRoomType(newVal);
        }
    },
    computed: {

        filteredBeds() {
            return this.bedOptions[this.roomType] || [];
        },
        editfilteredBeds() {
            return this.bedOptions[this.editData.roomType] || [];
        },


    },
    watch: {



    },

};
</script>

<style scoped>
.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}
</style>
