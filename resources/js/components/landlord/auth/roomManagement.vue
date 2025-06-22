<template>

    <div class="container mt-5">
        <Loader ref="loader" />
        <Toastcomponents ref="toast" />
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary fw-bold">Post Room</h1>
            <div>
                <button class="btn btn-outline-primary" @click="VisibleAddModal = true">
                    Add Room
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <input type="text" v-model="searchTerm" placeholder="🔍 Search Room..." class="form-control mb-4 shadow-sm" />

        <!-- Table -->
        <div class="table-responsive shadow-sm rounded mt-4">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Room ID</th>
                        <th>Room No</th>
                        <th>Room Type</th>
                        <th>Price (₱)</th>
                        <th>Availability</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center align-middle" v-for="room in rooms" :key="room.room_id">
                        <td>{{ room.room_id }}</td>
                        <td>{{ room.room_number }}</td>
                        <td>{{ room.room_type }}</td>
                        <td>{{ room.price }}</td>
                        <td>
                            <span class="badge"
                                :class="room.availability === 'Available' ? 'bg-success' : room.availability === 'Occupied' ? 'bg-danger' : 'bg-warning'">
                                {{ room.availability }}
                            </span>
                        </td>
                        <td>{{ room.capacity }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-success" @click="ViewRoom(room.room_id)">
                                    View
                                </button>
                                <button class="btn btn-sm btn-outline-primary" @click="editRoom(room.room_id)">
                                    Update
                                </button>
                                <button class="btn btn-sm btn-outline-danger" @click="deleteRoom(room.room_id)">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!--modal add room-->
        <div v-if="VisibleAddModal" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4">
                    <div class="modal-header bg-outline-primary text-black">
                        <h5 class="modal-title">Add Room</h5>
                        <button type="button" class="btn-close" @click="VisibleAddModal = false"></button>
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
                                    <li v-for="dorm in dorms" :key="dorm.dorm_id">
                                        <a class="dropdown-item d-flex justify-content-between align-items-center"
                                            href="#" @click.prevent="dormId(dorm)">
                                            <span>{{ dorm.dorm_name }}</span>
                                            <span class="badge bg-secondary">ID: {{ dorm.dorm_id }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="border border-secondary rounded-3 p-4 mb-3 text-center" style="cursor: pointer;"
                            @click="triggerroomImagePreview3">
                            <input ref="RoomsImages3Input" class="d-none" type="file" accept="image/*"
                                @change="handleroomImagePreview3" />

                            <!-- Icon + Text -->
                            <div class="d-flex flex-column align-items-center text-center mb-3">
                                <h5 class="text-secondary mt-2">Click to Upload Room Image</h5>
                                <small class="text-muted">Click to browse and select an image file</small>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div v-if="roomImagePreview" class="text-center mb-3">
                            <img :src="roomImagePreview" alt="Uploaded Room Image" class="img-fluid rounded mb-2"
                                style="max-height: 250px;" />
                            <div>
                                <button type="button" @click="removeroomImagePreviews3" class="btn btn-sm">
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
                                        <option value="Loft Room / Mezzanine Type">Loft Room / Mezzanine Type</option>
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
                                <div class="mb-2 mt-2">
                                    <label for="capacity">Capacity</label>

                                </div>
                                <div class="d-flex align-items-center mb-2">

                                    <button class="btn btn-outline-danger me-2" @click="decrementcapacity">-</button>
                                    <div class="flex-grow-1">
                                        <input type="text" readonly class="form-control" id="capacity"
                                            v-model="capacity" placeholder="0">
                                    </div>
                                    <button class="btn btn-outline-success ms-2" @click="incrementcapacity">+</button>
                                </div>

                                <span class="text-danger small" v-if="errors.capacity">{{ errors.capacity[0]
                                }}</span>
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
                    <div v-if="editData.roomImagePreview" class="text-center mb-3">
                        <img :src="editData.roomImagePreview" alt="Uploaded Room Image" class="img-fluid rounded mb-2"
                            style="max-height: 250px;" />

                    </div>
                    <div class="row g-4">
                        <!-- Column 1 -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" readonly placeholder="Dorm ID"
                                    v-model="editData.dormitory_id">
                                <label>Dorm ID</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="roomNumber" placeholder="Room Number"
                                    v-model="editData.room_number">
                                <label for="roomNumber">Room Number</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.room_number">{{
                                errors.editData.room_number[0]
                            }}</span>
                            <div class="form-floating mb-2 mt-2">
                                <input type="number" class="form-control" id="areaSqm" v-model="editData.area_sqm"
                                    min="1" placeholder="Enter area in sqm" required>
                                <label for="areaSqm">Area (sqm)</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.area_sqm">{{
                                errors.editData.area_sqm[0]
                            }}</span>


                            <div class="form-floating mb-2 mt-2">
                                <select class="form-select" id="room_type" v-model="editData.room_type">
                                    <option value="" disabled>
                                        Select Room Type</option>
                                    <option value="Single Room">Single Room</option>
                                    <option value="Double Room / Shared Room">Double Room / Shared Room</option>
                                    <option value="Bedspace / Multi-Sharing Room">Bedspace / Multi-Sharing Room</option>
                                    <option value="Studio-Type Room">Studio-Type Room</option>
                                    <option value="Partitioned Bedspace (Cubicle Style)">Partitioned Bedspace (Cubicle
                                        Style)</option>
                                    <option value="Loft Room / Mezzanine Type">Loft Room / Mezzanine Type</option>
                                </select>
                                <label for="room_type">Room Type</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.room_type">{{
                                errors.editData.room_type[0]
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
                        <div class="col-md-6">
                            <div class="form-floating mb-2">
                                <input type="number" class="form-control" id="price" placeholder="Price"
                                    v-model="editData.price" min="0" step="0.01">
                                <label for="price">Price (₱)</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.price">{{
                                errors.editData.price[0]
                            }}</span>
                            <div class="form-floating mb-2 mt-2">
                                <select class="form-select" id="bedType" v-model="editData.listing_type" required>
                                    <option value="" disabled>Select Bed Type</option>
                                    <option v-for="bed in editfilteredBeds" :key="bed" :value="bed">
                                        {{ bed }}
                                    </option>
                                </select>
                                <label for="bedType">Bed Type</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.listing_type">{{
                                errors.editData.listing_type[0]
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
                                <select class="form-select" id="genderPreference" v-model="editData.gender_preference"
                                    required>
                                    <option value="" disabled selected>Select Gender Preference</option>
                                    <option value="Male Only">Male Only</option>
                                    <option value="Female Only">Female Only</option>
                                    <option value="Any Gender">Any Gender</option>
                                </select>
                                <label for="genderPreference">Gender Preference</label>
                            </div>
                            <span class="text-danger small" v-if="errors.editData?.gender_preference">{{
                                errors.editData.gender_preference[0]
                            }}</span>

                            <div class="d-flex align-items-center mb-2">
                                <button class="btn btn-outline-danger me-2" @click="editdecrementcapacity">-</button>
                                <div class="flex-grow-1">
                                    <input type="text" readonly class="form-control" id="capacity"
                                        v-model="editData.capacity" placeholder="0">
                                </div>
                                <button class="btn btn-outline-success ms-2" @click="editincrementcapacity">+</button>
                            </div>

                            <span class="text-danger small" v-if="errors.editData?.capacity">{{
                                errors.editData.capacity[0]
                            }}</span>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" @click="updateRoom" class="btn btn-outline-primary btn-lg">
                                    Update Room
                                </button>
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
                    <h5 class="modal-title">Room Details</h5>
                    <button type="button" class="btn-close btn-close-black" @click="VisibleDisplayDataModal = false"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding: 1.5rem;">
                    <img :src="selectedRoom?.room_images" class="img-fluid mb-3" alt="...">
                    <div class="row g-4 ">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Room Number:</label>
                                <div class="p-2 border rounded bg-light text-break">{{ selectedRoom?.room_number
                                }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Room Type:</label>
                                <div class="p-2 border rounded bg-light text-break">{{ selectedRoom?.room_type
                                }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Price:</label>
                                <div class="p-2 border rounded bg-light text-break">₱{{ selectedRoom?.price }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Capacity:</label>
                                <div class="p-2 border rounded bg-light text-break">{{ selectedRoom?.capacity }}
                                </div>
                            </div>
                            <label class="form-label fw-bold">Dorm Name:</label>
                            <div class="p-2 border rounded bg-light text-break w-100"
                                style="max-height: 100px;  overflow-y: auto;">
                                {{ selectedRoom?.dorm_name
                                }}

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
                                    selectedRoom?.contact_email
                                }}

                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Contact Phone:</label>
                                <div class="p-2 border rounded bg-light text-break">{{
                                    selectedRoom?.contact_phone
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
                                        {{ selectedRoom?.address
                                        }}

                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-success px-4"
                        @click="VisibleDisplayDataModal = false">OK</button>
                </div>
            </div>
        </div>
    </div>


    <!--Images Post Modal-->



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
            searchTerm: "",
            VisibleAddModal: false,
            VisibleDeleteModal: false,
            VisibleUpdateModal: false,
            VisibleDisplayDataModal: false,
            loading: false,
            dorms: window.allRooms || [],
            dormsId: "",
            roomNumber: "",
            roomType: "",
            availability: '',
            availibilityArray: ['Available'],
            editavailibilityArray: ['Available', 'Under Maintenance'],
            price: "",
            capacity: '',
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
            editData:
            {
                dormitory_id: '',
                room_number: '',
                room_type: '',
                availability: '',
                price: '',
                amenities: '',
                capacity: '',
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
                capacity: '',
                roomImagePreview: '',
                roomImageFile: '',
            },
            errors: {},
            rooms: [],
            selectedRoom: null,
            currentRoomID: null,
        };
    },
    methods: {
        dormId(dorm) {
            this.dormsId = dorm.dorm_id;
        },


        decrementcapacity() {
            if (this.capacity > this.roomCapacityRange.min) {
                this.capacity--;
            }
        },
        incrementcapacity() {
            if (this.capacity < this.roomCapacityRange.max) {
                this.capacity++;
            }
        },
        handleroomImagePreview3(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.roomImagePreview) {
                    URL.revokeObjectURL(this.roomImagePreview);
                }
                this.roomImageFile = file;


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
                formData.append('capacity', this.capacity);
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
                } else if (response.data.errors) {
                    this.$refs.loader.loading = false;

                    this.errors = response.data.errors;
                }

            } catch (error) {
                this.$refs.loader.loading = false;

                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                    this.$refs.toast.showToast('Please check your fields', 'danger');


                } else {
                    this.$refs.loader.loading = false;

                    console.error('Unexpected error:', error);
                    this.$refs.toast.showToast('Something went wrong.', 'error');
                }
            }
        },

        async fetchRooms() {
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get('/ListRooms', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                this.rooms = response.data.rooms || [];
                this.$refs.loader.loading = false;

            } catch (error) {
                console.error('Error fetching rooms:', error);
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

        editdecrementcapacity() {
            if (this.editData.capacity > this.editroomCapacityRange.min) {
                this.editData.capacity--;
            }
        },
        editincrementcapacity() {
            if (this.editData.capacity < this.editroomCapacityRange.max) {
                this.editData.capacity++;
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
                        roomImagePreview: response.data.room.room_images || "",


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
        editroom_type(newType) {
            const ranges = {
                'Single Room': { min: 1, max: 1 },
                'Double Room / Shared Room': { min: 1, max: 2 },
                'Studio-Type Room': { min: 1, max: 3 },
                'Loft Room / Mezzanine Type': { min: 2, max: 4 },
                'Partitioned Bedspace (Cubicle Style)': { min: 1, max: 1 },
                'Bedspace / Multi-Sharing Room': { min: 4, max: 6 },
            };
            const selectedRange = ranges[newType] || { min: 0, max: 0 };
            this.editData.capacity = selectedRange.min;
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

            formData.append('dormitory_id', this.editData.dormitory_id);
            formData.append('room_number', this.editData.room_number);
            formData.append('room_type', this.editData.room_type);
            formData.append('availability', this.editData.availability);
            formData.append('price', this.editData.price);
            formData.append('capacity', this.editData.capacity);
            formData.append('listing_type', this.editData.listing_type);
            formData.append('area_sqm', this.editData.area_sqm);
            formData.append('gender_preference', this.editData.gender_preference);
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
                    this.fetchRooms();
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
                capacity: '',
                listing_type: '',
                area_sqm: '',
                gender_preference: '',
                capacity: '',
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
            this.capacity = '';
            this.amenities = "";
            this.listing_type = '';
            this.area_sqm = '';
            this.gender_preference = '';
            this.roomImagePreview = '';
            this.roomImageFile = '';
            this.errors = {};
        }




    },
    mounted() {
        this.fetchRooms();

    },
    computed: {
        filteredRooms() {
            if (!this.searchTerm) return this.rooms;
            return this.rooms.filter(room =>
                Object.values(room).some(val =>
                    String(val).toLowerCase().includes(this.searchTerm.toLowerCase())
                )
            );
        },
        filteredBeds() {
            return this.bedOptions[this.roomType] || [];
        },
        editfilteredBeds() {
            return this.bedOptions[this.editData.room_type] || [];
        },

        roomCapacityRange() {
            const ranges = {
                'Single Room': { min: 1, max: 1 },
                'Double Room / Shared Room': { min: 1, max: 2 },
                'Studio-Type Room': { min: 1, max: 3 },
                'Loft Room / Mezzanine Type': { min: 2, max: 4 },
                'Partitioned Bedspace (Cubicle Style)': { min: 1, max: 1 },
                'Bedspace / Multi-Sharing Room': { min: 4, max: 6 },
            };
            return ranges[this.roomType] || { min: 0, max: 0 };
        },

        editroomCapacityRange() {
            const ranges = {
                'Single Room': { min: 1, max: 1 },
                'Double Room / Shared Room': { min: 1, max: 2 },
                'Studio-Type Room': { min: 1, max: 3 },
                'Loft Room / Mezzanine Type': { min: 2, max: 4 },
                'Partitioned Bedspace (Cubicle Style)': { min: 1, max: 1 },
                'Bedspace / Multi-Sharing Room': { min: 4, max: 6 },
            };
            return ranges[this.editData.room_type] || { min: 0, max: 0 };
        },
    },
    watch: {

        roomType(newType) {
            const ranges = {
                'Single Room': { min: 1, max: 1 },
                'Double Room / Shared Room': { min: 1, max: 2 },
                'Studio-Type Room': { min: 1, max: 3 },
                'Loft Room / Mezzanine Type': { min: 2, max: 4 },
                'Partitioned Bedspace (Cubicle Style)': { min: 1, max: 1 },
                'Bedspace / Multi-Sharing Room': { min: 4, max: 6 },
            };
            const selectedRange = ranges[newType] || { min: 0, max: 0 };
            this.capacity = selectedRange.min;
        },

    },

};
</script>

<style scoped>
.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}
</style>
