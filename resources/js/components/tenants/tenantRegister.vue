<template>


    <div>
        <loader ref="loader" />
        <div :class="['container-toast mt-5', { show: toaster }]">
            <!-- Toast Container -->
            <div :class="['toast-child', `bg-${toastColor}`]">
                <div class="toast-body d-flex justify-content-between align-items-center text-white fw-bold py-3 px-4">
                    <span class="text-wrap">{{ messageToaster }}</span>
                    <button type="button" class="btn-close btn-close-white ms-3" @click="ExitToaster"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitTenant">

            <!-- <div v-if="successMessage" class="alert alert-success text-center"><span class="text-black">{{successMessage}}</span></div> -->
            <!-- Profile Picture -->
            <div class="d-flex justify-content-center container-image my-4">
                <div class="avatar-wrapper text-center">
                    <img class="profile-pic rounded-circle" :src="profilePic" alt="Profile Picture" width="150">

                    <div class="upload-button btn btn-primary mt-3" @click="triggerFileInput">
                        Upload Image
                        <input ref="fileInput" class="file-input" name="profile_pic" id="profile-pic" type="file"
                            accept="image/*" style="display: none;" @change="handleImageUpload">
                    </div>
                </div>
            </div>

            <!-- First Name and Last Name -->
            <div class="row px-4">
                <div class="col-md-6 mb-3">
                    <label for="firstname" class="form-label">First Name</label>
                    <input type="text" id="firstname" name="firstname" v-model="firstname" class="form-control "
                        style="border: 2px solid #4edce2 ;" placeholder="Enter your First Name">
                    <span v-if="errors.firstname" class="error">{{ errors.firstname[0] }}</span>



                </div>

                <div class="col-md-6 mb-3">
                    <label for="lastname" class="form-label">Last Name</label>
                    <input type="text" id="lastname" name="lastname" v-model="lastname" class="form-control"
                        style="border: 2px solid #4edce2 ;" placeholder="Enter your Last Name" required>
                    <span v-if="errors.lastname" class="error">{{ errors.lastname[0] }}</span>

                </div>
            </div>

            <!-- Email -->
            <div class="mb-3 px-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" style="border: 2px solid #4edce2 ;" v-model="email"
                    class="form-control " placeholder="Enter your Email Address" required>
                <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>

            </div>

            <!-- Password and Confirm Password -->
            <div class="row px-4">
                <div class="col-md-6 mb-0">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" v-model="password" class="form-control "
                        style="border: 2px solid #4edce2 ;" placeholder="Enter your Password" required>
                    <span class="error" v-if="errors.password">{{ errors.password[0] }}</span>

                </div>
                <div class="col-md-6 mb-0">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="password_confirmation"
                        v-model="password_confirmation" class="form-control " style="border: 2px solid #4edce2 ;"
                        placeholder="Confirm your Password" required>
                    <span v-if="errors.password_confirmation" class="error">{{ errors.password_confirmation[0] }}</span>
                </div>
            </div>

            <!-- Show Password Checkbox -->
            <div class="mb-3 px-4">
                <div class="input-group-text bg-transparent border-0">
                    <input type="checkbox" id="show-password" class="me-2 bg-transparent"
                        @click="togglePasswordVisibility">
                    <label for="show-password" class="form-check-label bg-transparent">Show Password</label>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="mb-3 px-4">
                <label for="phonenumber" class="form-label">Phone Number</label>
                <input type="tel" id="phonenumber" name="phonenumber" v-model="phonenumber" class="form-control"
                    style="border: 2px solid #4edce2 ;" placeholder="Enter your Phone Number" required
                    pattern="^(?:\+63|09)\d{9}$">

                <span v-if="errors.phonenumber" class="error">{{ errors.phonenumber[0] }}</span>

            </div>

            <!-- Gender -->
            <div class="mb-3 px-4">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" v-model="gender" class="form-select"
                    style="border: 2px solid #4edce2 ;" required>
                    <option value="">-- Select Gender --</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <span class="error" v-if="errors.gender">{{ errors.gender[0] }}</span>
            </div>

            <!-- Address Fields -->
            <div class="row px-4">
                <!-- Region -->
                <div class="col-md-4 mb-3">
                    <label for="region" class="form-label">Region</label>
                    <select id="region" name="region" v-model="selectedRegion" class="form-select "
                        style="border: 2px solid #4edce2 ;" @change="updateProvinces">
                        <option value="">-- Select Region --</option>
                        <option v-for="(name, code) in regions" :key="code" :value="code">{{ name }}</option>
                    </select>
                    <span class="error" v-if="errors.selectedRegion">{{ errors.selectedRegion[0] }}</span>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="province" class="form-label">Province</label>
                    <select id="province" name="province" v-model="selectedProvince" class="form-select "
                        style="border: 2px solid #4edce2 ;" @change="updateCities">
                        <option value="">-- Select Province --</option>
                        <option v-if="provinces.length === 0" disabled>No provinces available</option>
                        <option v-for="province in provinces" :key="province" :value="province">{{ province }}</option>
                    </select>
                    <span class="error" v-if="errors.selectedProvince">{{ errors.selectedProvince[0] }}</span>

                </div>


                <div class="col-md-4 mb-3">
                    <label for="city" class="form-label">City</label>
                    <select id="city" name="city" v-model="selectedCity" class="form-select " style="border: 2px solid #4edce2 ;">
                        <option value="">-- Select City --</option>
                        <option v-if="cities.length === 0" disabled>No City available</option>
                        <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                    </select>
                    <span class="error" v-if="errors.selectedCity">{{ errors.selectedCity[0] }}</span>
                </div>
            </div>
            <div class="mb-3 px-4">
                <label for="postal_code" class="form-label">Postal Code</label>
                <input type="text" id="postalcode" name="postalcode" v-model="postalcode" class="form-control "
                    style="border: 2px solid #4edce2 ;" placeholder="Postal Code" required>
                <span class="error" v-if="errors.postalcode">{{ errors.postalcode[0] }}</span>
            </div>

            <div class="mb-3 px-4">
                <label for="current_address" class="form-label">Current Address</label>
                <textarea id="current_address" name="currentaddress" v-model="currentaddress" class="form-control "
                    style="border: 2px solid #4edce2 ;" rows="3" placeholder="Current Address" required></textarea>
                <span class="error" v-if="errors.currentaddress">{{ errors.currentaddress[0] }}</span>
            </div>
            <div class="d-flex justify-content-center mb-4">
                <button type="button" @click="openModal" class="btn  w-50 sign-in-style">Sign-up</button>
            </div>





        </form>
    </div>
    <div v-if="modalVisible" class="custom-modal">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title text-center">Email OTP</h5>
                <button type="button" class="btn-close" @click="closeModal">×</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <p>Please enter the verification OTP sent to your email.</p>
                <div class="otp-inputs">
                    <input v-for="(digit, index) in otpdigits" :key="index" type="text" :ref="'otpInput' + index"
                        maxlength="1" class="form-control" name="codeotp" v-model="otpdigits[index]"
                        @input="handleInput(index)" @keydown.backspace="handleBackspace(index)" required />
                </div>
                <div class="otp_timer mb-3">
                    <p class="primary" v-if="otpTimer > 0">OTP expires in: {{ formattedTime }}</p>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary" @click="submitTenant">Verify Email</button>
                    <button type="button" @click="resendOtp" class="btn btn-success">Resend OTP</button>
                </div>
            </div>
        </div>

    </div>

    <!--Register Error Modal-->

    <!--Register Success Modal-->




</template>
<script>
import axios from 'axios';
import loader from '@/components/loader.vue';
import Toastcomponents from '@/components/Toastcomponents.vue';
export default {
    components: {
        loader,
        Toastcomponents,

    },
    name: "TenantRegistration",
    data() {
        return {
            profilePic: "/images/registertenant/Profile-PNG-Photo.png",
            profileFile: null,
            firstname: '',
            lastname: '',
            email: '',
            password: '',
            password_confirmation: '',
            phonenumber: '',
            gender: '',
            postalcode: '',
            currentaddress: '',
            message: '',
            errors: {},
            modalVisible: false,
            isLoading: false,
            otpTimer: 0,
            selectedRegion: "",
            selectedProvince: "",
            selectedCity: "",
            cities: [],
            toaster: false,
            toastColor: 'success', // Default color
            messageToaster: '',
            RegisterErrorChildMessage: "",
            otpdigits: Array(6).fill(''),
            provinces: [],
            regions: {
                "NCR": "National Capital Region (NCR)",
                "CAR": "Cordillera Administrative Region (CAR)",
                "Region I": "Region I - Ilocos Region",
                "Region II": "Region II - Cagayan Valley",
                "Region III": "Region III - Central Luzon",
                "Region IV-A": "Region IV-A - CALABARZON",
                "MIMAROPA": "MIMAROPA Region",
                "Region V": "Region V - Bicol Region",
                "Region VI": "Region VI - Western Visayas",
                "Region VII": "Region VII - Central Visayas",
                "Region VIII": "Region VIII - Eastern Visayas",
                "Region IX": "Region IX - Zamboanga Peninsula",
                "Region X": "Region X - Northern Mindanao",
                "Region XI": "Region XI - Davao Region",
                "Region XII": "Region XII - SOCCSKSARGEN",
                "Region XIII": "Region XIII - Caraga",
                "BARMM": "BARMM - Bangsamoro Autonomous Region"
            },
            provinceData: {
                "NCR": ["Metro Manila"],
                "CAR": ["Abra", "Apayao", "Benguet", "Ifugao", "Kalinga", "Mountain Province"],
                "Region I": ["Ilocos Norte", "Ilocos Sur", "La Union", "Pangasinan"],
                "Region II": ["Batanes", "Cagayan", "Isabela", "Nueva Vizcaya", "Quirino"],
                "Region III": ["Aurora", "Bataan", "Bulacan", "Nueva Ecija", "Pampanga", "Tarlac", "Zambales"],
                "Region IV-A": ["Batangas", "Cavite", "Laguna", "Quezon", "Rizal"],
                "MIMAROPA": ["Marinduque", "Occidental Mindoro", "Oriental Mindoro", "Palawan", "Romblon"],
                "Region V": ["Albay", "Camarines Norte", "Camarines Sur", "Catanduanes", "Masbate", "Sorsogon"],
                "Region VI": ["Aklan", "Antique", "Capiz", "Guimaras", "Iloilo", "Negros Occidental"],
                "Region VII": ["Bohol", "Cebu", "Negros Oriental", "Siquijor"],
                "Region VIII": ["Biliran", "Eastern Samar", "Leyte", "Northern Samar", "Samar", "Southern Leyte"],
                "Region IX": ["Zamboanga del Norte", "Zamboanga del Sur", "Zamboanga Sibugay"],
                "Region X": ["Bukidnon", "Camiguin", "Lanao del Norte", "Misamis Occidental", "Misamis Oriental"],
                "Region XI": ["Davao de Oro", "Davao del Norte", "Davao del Sur", "Davao Occidental", "Davao Oriental"],
                "Region XII": ["Cotabato", "Sarangani", "South Cotabato", "Sultan Kudarat"],
                "Region XIII": ["Agusan del Norte", "Agusan del Sur", "Dinagat Islands", "Surigao del Norte", "Surigao del Sur"],
                "BARMM": ["Basilan", "Lanao del Sur", "Maguindanao", "Sulu", "Tawi-Tawi"]
            },
            cityData: {

                "Metro Manila": ["Manila", "Quezon City", "Caloocan", "Las Piñas", "Makati", "Malabon", "Mandaluyong", "Marikina", "Muntinlupa", "Navotas", "Parañaque", "Pasay", "Pasig", "Pateros", "San Juan", "Taguig"],
                "Abra": ["Bangued", "Boliney", "Bucay", "Bucloc", "Daguioman", "Danglas", "Dolores", "La Paz", "Lacub", "Lagangilang", "Langiden", "Licuan-Baay", "Luba", "Malibcong", "Manabo", "Peñarrubia", "Pidigan", "Pilar", "Sallapadan", "San Isidro", "San Juan", "San Quintin", "Tayum", "Tineg", "Tubo"],
                "Apayao": ["Calanasan", "Conner", "Flora", "Kabugao", "Luna", "Pudtol", "Santa Marcela"],
                "Benguet": ["Atok", "Bakun", "Bokod", "Buguias", "Itogon", "Kabayan", "Kapangan", "Kibungan", "La Trinidad", "Mankayan", "Sablan", "Tuba", "Tublay"],
                "Ifugao": ["Aguinaldo", "Alfonso Lista", "Asipulo", "Banaue", "Hingyon", "Hungduan", "Kiangan", "Lagawe", "Lamut", "Mayoyao", "Tinoc"],
                "Kalinga": ["Tabuk City", "Balbalan", "Lubuagan", "Pasil", "Pinukpuk", "Rizal", "Tinglayan", "Tanudan"],
                "Mountain Province": ["Barlig", "Bauko", "Besao", "Bontoc", "Natonin", "Paracelis", "Sabangan", "Sadanga", "Sagada", "Tadian"],
                "Ilocos Norte": ["Laoag City", "Batac"],
                "Ilocos Sur": ["Vigan City", "Candon City"],
                "La Union": ["San Fernando City"],
                "Pangasinan": ["Dagupan City", "Urdaneta City", "Alaminos City", "San Carlos City"],
                "Batanes": ["Basco"],
                "Cagayan": ["Tuguegarao City", "Santiago City", "Ilagan City", "Cauayan City"],
                "Isabela": ["Ilagan City", "Cauayan City", "Santiago City"],
                "Nueva Vizcaya": ["Bayombong", "Solano"],
                "Quirino": ["Aglipay", "Cabarroguis", "Diffun", "Maddela", "Nagtipunan", "Saguday"],
                "Nueva Ecija": ["Aliaga", "Bongabon", "Cabiao", "Cabanatuan City", "Carranglan", "Cuyapo", "Gabaldon", "Gapan City", "General Mamerto Natividad", "General Tinio", "Guimba", "Jaen", "Laur", "Licab", "Llanera", "Lupao", "Nampicuan", "Palayan City", "Pantabangan", "Peñaranda", "Quezon", "Rizal", "San Antonio", "San Isidro", "San Jose City", "San Leonardo", "Santa Rosa", "Santo Domingo", "Talavera", "Talugtug", "Zaragoza"],
                "Pampanga": ["Angeles City", "City of San Fernando", "Mabalacat City", "San Luis", "San Simon", "Santa Ana", "Santo Tomas", "Apalit", "Bacolor", "Floridablanca", "Guagua", "Lubao", "Macabebe", "Magalang", "Masantol", "Mexico", "Porac"],
                "Tarlac": ["Tarlac City", "Concepcion", "Gerona", "La Paz", "Moncada", "Paniqui", "Pura", "Ramos", "San Clemente", "San Jose", "San Manuel", "Santa Ignacia", "Victoria"],
                "Bohol": ["Tagbilaran City", "Antequera", "Baclayon", "Balilihan", "Bilar", "Bohol", "Carmen", "Catigbian", "Cortes", "Dauis", "Dimiao", "Duero", "Garcia Hernandez", "Guindulman", "Inabanga", "Jagna", "Loboc", "Loon", "Mabini", "Panglao", "Pilar", "Sagbayan", "San Isidro", "San Miguel", "Sevilla", "Sierra Bullones", "Tauran", "Trinidad"],
                "Cebu": ["Cebu City", "Carcar City", "Danao City", "Lapu-Lapu City", "Mandaue City", "Naga City", "Toledo City", "Alcoy", "Alegria", "Aloguinsan", "Argao", "Asturias", "Badian", "Balamban", "Bantayan", "Barili", "Bogo City", "Boljoon", "Borbon", "Carmen", "Catmon", "Compostela", "Consolacion", "Cordova", "Dumanjug", "Ginatilan", "Liloan", "Minglanilla", "Moalboal", "Oslob", "Pinamungajan", "Poro", "San Fernando", "San Francisco", "San Remigio", "Santa Fe", "Santander", "Sogod", "Tabogon", "Tabuelan", "Talisay City", "Tuban", "Tuburan"],
                "Zambales": ["Iba", "Olongapo City", "San Antonio", "San Felipe", "San Marcelino", "San Narciso", "Castillejos", "Subic", "Botolan", "Masinloc", "Palauig", "Candelaria", "Chindonan", "Kababae"],
                "Bulacan": ["Malolos", "Meycauayan", "San Jose del Monte", "Baliuag", "Angat"],
                "Batangas": ["Batangas City", "Lipa City", "Tanauan City", "Balayan", "Lemery"],
                "Davao del Sur": ["Davao City", "Tagum City", "Digos City", "Panabo City", "Samal City"],
                "South Cotabato": ["General Santos City", "Koronadal City", "Polomolok", "Tupi"],
                "Surigao del Norte": ["Surigao City", "Siargao Island"],
                "Misamis Oriental": ["Cagayan de Oro City", "Gingoog City", "El Salvador City", "Claveria"],
                "BARMM": ["Marawi City", "Cotabato City", "Lamitan City", "Malabang"],
                "Aurora": ["Baler", "Maria Aurora"],
                "Bataan": ["Balanga City", "Dinalupihan", "Hermosa"],
                "Cavite": ["Imus City", "Bacoor City", "Dasmariñas City", "Tagaytay City", "Trece Martires City"],
                "Laguna": ["Santa Rosa City", "Calamba City", "San Pablo City", "Biñan City", "Cabuyao City"],
                "Quezon": ["Lucena City", "Tayabas City", "Sariaya", "Pagbilao"],
                "Rizal": ["Antipolo City", "Cainta", "Taytay", "Binangonan"],
                "Marinduque": ["Boac", "Gasan", "Torrijos"],
                "Occidental Mindoro": ["Mamburao", "Sablayan", "San Jose", "Calintaan"],
                "Oriental Mindoro": ["Calapan City", "Puerto Galera", "Baco", "Pinamalayan"],
                "Palawan": ["Puerto Princesa City", "Coron", "El Nido", "Brooke's Point"],
                "Romblon": ["Romblon Town", "Odiongan", "Looc", "Ferrol"],
                "Albay": ["Legazpi City", "Tabaco City", "Ligao City", "Daraga", "Tiwi"],
                "Camarines Norte": ["Daet", "Labo", "Paracale", "Jose Panganiban"],
                "Camarines Sur": ["Naga City", "Sorsogon City", "Iriga City", "Pili", "Calabanga"],
                "Catanduanes": ["Virac", "Baras", "Pandan", "Bagamanoc"],
                "Masbate": ["Masbate City", "Milagros", "Mandaon", "Baleno"],
                "Sorsogon": ["Sorsogon City", "Gubat", "Bulan", "Matnog"],
                "Aklan": ["Kalibo", "Boracay", "Malay", "Nabas"],
                "Antique": ["San Jose de Buenavista", "Pandan", "Culasi", "Anini-y"],
                "Capiz": ["Roxas City", "Pontevedra", "Jamindan", "Cuartero"],
                "Guimaras": ["Jordan", "Nueva Valencia", "San Lorenzo", "Sibunag"],
                "Iloilo": ["Iloilo City", "Passi City", "Roxas City", "Silay City", "Kalibo"],
                "Negros Occidental": ["Bacolod City", "Silay City", "Cadiz City", "Escalante City", "Victorias City"],
                "Negros Oriental": ["Dumaguete City", "Bais City", "Tanjay City", "Canlaon City", "Guihulngan City"],
                "Siquijor": ["Siquijor Town", "Larena", "Enrique Villanueva", "Maria"],
                "Biliran": ["Naval", "Caibiran", "Biliran Town", "Almeria"],
                "Eastern Samar": ["Borongan City", "Sulat", "Taft", "Maydolong"],
                "Leyte": ["Tacloban City", "Ormoc City", "Baybay City", "Maasin City", "Abuyog"],
                "Northern Samar": ["Catarman", "Laoang", "Victoria", "Allen"],
                "Samar": ["Calbayog City", "Catbalogan City", "Basey", "Marabut"],
                "Southern Leyte": ["Maasin City", "Sogod", "Hinunangan", "Bontoc"],
                "Zamboanga del Norte": ["Dipolog City", "Dapitan City", "Polanco", "Manukan"],
                "Zamboanga del Sur": ["Zamboanga City", "Pagadian City", "Ipil", "Molave"],
                "Zamboanga Sibugay": ["Ipil", "Buug", "Payao", "Titay"],
                "Bukidnon": ["Malaybalay City", "Valencia City", "Manolo Fortich", "Don Carlos"],
                "Camiguin": ["Mambajao", "Mahinog", "Guinsiliban", "Catarman"],
                "Lanao del Norte": ["Iligan City", "Tubod", "Baroy", "Kapatagan"],
                "Misamis Occidental": ["Oroquieta City", "Ozamiz City", "Tangub City", "Jimenez"],
                "Misamis Oriental": ["Cagayan de Oro City", "Gingoog City", "El Salvador City", "Claveria"],
                "Davao de Oro": ["Nabunturan", "Compostela", "Montevista", "New Corella"],
                "Davao del Norte": ["Tagum City", "Panabo City", "Island Garden City of Samal", "Carmen"],
                "Davao Occidental": ["Malita", "Don Marcelino", "Santa Maria", "Sarangani"],
                "Davao Oriental": ["Mati City", "Governor Generoso", "Manay", "Boston"],
                "Agusan del Norte": ["Butuan City", "Cabadbaran City", "Jabonga", "Las Nieves"],
                "Agusan del Sur": ["Prosperidad", "Bunawan", "Bayugan City", "Rosario"],
                "Dinagat Islands": ["San Jose", "Libjo", "Loreto", "Cagdianao"],
                "Surigao del Sur": ["Tandag City", "Bislig City", "Hinatuan", "Lianga"],
                "Basilan": ["Isabela City", "Lamitan City", "Hadji Mohammad Ajul", "Sumisip"],
                "Lanao del Sur": ["Marawi City", "Malabang", "Picong", "Wao"],
                "Maguindanao": ["Cotabato City", "Buluan", "Shariff Aguak", "Datu Odin Sinsuat"],
                "Sulu": ["Jolo", "Patikul", "Indanan", "Maimbung"],
                "Tawi-Tawi": ["Bongao", "Sibutu", "Sitangkai", "Mapun"],
                "Cotabato": ["Cotabato City", "Aleosan", "Antipas", "Arakan", "Banisilan", "Carmen", "Kabacan", "Midsayap", "Matalam", "Pigcawayan", "Pikit", "President Roxas", "Tulunan"],
                "Sarangani": ["Alabel", "Glan", "Kiamba", "Maasim", "Maitum", "Malungon", "Sarangani", "Santo Niño"],
                "Sultan Kudarat": ["Isulan", "Bagumbayan", "Columbio", "Esperanza", "Kalamansig", "Lambayong", "Lebak", "Sen. Ninoy Aquino", "Tacurong"]
            },
        }
    },
    methods: {
        formattedTime() {

            const minutes = Math.floor(this.otpTimer / 60);
            const seconds = this.otpTimer % 60;
            return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        },
        showToast(message, color = 'success') {
            this.messageToaster = message;
            this.toastColor = color;
            this.toaster = true;

            // Auto-hide after 3 seconds
            setTimeout(() => {
                this.ExitToaster();
            }, 3000);
        },
        ExitToaster() {
            this.toaster = false;
        },
        updateProvinces() {
            console.log("Selected Region:", this.selectedRegion);
            this.selectedProvince = "";
            this.selectedCity = "";
            this.provinces = this.provinceData[this.selectedRegion] || [];
            console.log("Provinces:", this.provinces);
            this.cities = [];
        },
        updateCities() {
            console.log("Selected Province:", this.selectedProvince);
            this.selectedCity = "";
            this.cities = this.cityData[this.selectedProvince] || [];
            console.log("Cities:", this.cities);
        },

        startTimer() {
            this.intervalId = setInterval(() => {
                if (this.otpTimer > 0) {
                    this.otpTimer--;
                } else {
                    this.stopTimer();
                }
            }, 1000);
        },
        stopTimer() {
            clearInterval(this.intervalId);
        },
        resetTimer() {
            this.otpTimer = 60;
            this.startTimer();
        },
        mounted() {
            this.startTimer();
            
        },
        beforeUnmount() {
            this.stopTimer();
        },

        validateForm() {
            this.errors = {};
            if (!this.firstname) this.errors.firstname = ["First Name is required."];
            if (!this.lastname) this.errors.lastname = ["Last Name is required."];
            if (!this.email) this.errors.email = ["Email is required."];
            if (!this.password) this.errors.password = ["Password is required."];
            if (!this.phonenumber) this.errors.phonenumber = ["Phone Number is required."];
            if (!this.gender) this.errors.gender = ["Gender is required."];
            if (!this.selectedRegion) this.errors.selectedRegion = ["Region is required."];
            if (!this.selectedCity) this.errors.selectedCity = ["City is required."];
            if (!this.selectedProvince) this.errors.selectedProvince = ["Province is required."];
            if (!this.postalcode) this.errors.postalcode = ["Postal Code is required."];
            if (!this.currentaddress)
                this.errors.currentaddress = ["Current Address is required."];

            return Object.keys(this.errors).length === 0; // Return true if no errors
        },

        openModal() {

            this.errors = {};
            const isFormValid = this.validateForm();
            if (isFormValid) {
                this.$refs.loader.loading = true;
                this.OtpVerification()
                    .then((success) => {
                        if (success) {
                            this.showToast('OTP has been sent to your email address.', 'success');
                            this.modalVisible = true;
                            document.body.style.overflow = "hidden";

                        }
                    })
                    .catch((error) => {
                        console.error("Error during OTP verification:", error);
                    })
                    .finally(() => {
                        this.$refs.loader.loading = false;
                    });
            } else {
                this.$refs.loader.loading = false;


            }
        },
        closeModal() {
            this.modalVisible = false;
            document.body.style.overflow = "";

        },
        async OtpVerification() {
            const requestData = {
                email: this.email.trim(),
                phonenumber: this.phonenumber.trim(),
                postalcode: this.postalcode.trim(),
                password: this.password.trim(),
                password_confirmation: this.password_confirmation.trim(),
            };

            try {
                const response = await axios.post('/SendOtp', requestData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')

                    }
                });
                if (response.status === 200) {
                    this.showToast(response.data.message, 'success');

                    setTimeout(() => {
                        this.toastgmailvisible = false;

                    }, 10000);
                    this.startOtpTimer(response.data.timer);

                    return true;
                }
            }
            catch (error) {
                if (error.response && error.response.status === 422) {
                    console.error('Validation Errors:', error.response.data.errors);
                    this.errors = error.response.data.errors || {};
                } else if (error.response && error.response.status === 400) {
                    console.error('Unexpected Error:', error.response.data.message);
                    this.errors.general = [error.response.data.message];
                } else {
                    console.error('Network Error:', error);
                    this.errors.general = ['A network error occurred. Please try again.'];
                }
                return false;
            }


        },
        async submitTenant() {
            this.$refs.loader.loading = true;

            const formData = new FormData();
            formData.append('firstname', this.firstname);
            formData.append('lastname', this.lastname);
            formData.append('email', this.email);
            formData.append('password', this.password);
            formData.append('password_confirmation', this.password_confirmation);
            formData.append('phonenumber', this.phonenumber);
            formData.append('gender', this.gender);
            formData.append('city', this.selectedCity);
            formData.append('province', this.selectedProvince);
            formData.append('region', this.selectedRegion);
            formData.append('postalcode', this.postalcode);
            formData.append('currentaddress', this.currentaddress);

            if (this.profileFile) {
                formData.append('profile_pic', this.profileFile);
            } else {
                console.log('No Profile picture selected. Using default image');
            }

            formData.append('codeotp', this.otpdigits.join(''));

            try {
                const response = await axios.post('/registerTenant', formData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.status === 200) {
                    this.modalVisible = false;
                    // 
                    this.$refs.loader.loading = false;
                    this.showToast('Successfully Register', 'success');
                    this.errors = {};
                    this.fill();
                }

            } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                }
                if (error.response && error.response.data.status === "error") {
                    this.showToast(response.data.message, 'danger');

                }
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async resendOtp() {
            this.$refs.loader.loading = true;

            try {
                this.errors = {};
                this.errorMessage = '';
                this.otpTimer = 0;
                const requestData = {
                    email: this.email.trim(),
                }
                const response = await axios.post('/resendOtp', requestData);
                if (response.data.status === "success") {
                    this.showToast(response.data.message, 'success');

                    this.$refs.loader.loading = false;


                    this.startOtpTimer(response.data.timer);


                }

            }
            catch (error) {
                // Handle validation errors (status 422)
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                    this.errorMessage = 'Please correct the errors below.';
                }
                // Handle unexpected server errors (status 500)
                else if (error.response && error.response.status === 500) {
                    this.errorMessage = error.response.data.message || 'An unexpected error occurred. Please try again.';
                }
                // Handle network errors
                else {
                    this.errorMessage = 'A network error occurred. Please check your connection.';
                }
            } finally {
                // Re-enable the Resend OTP button
                // this.isResending = false;
            }
        },
        togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            confirmPasswordField.type = type;
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.profilePic = URL.createObjectURL(file);
                this.profileFile = file;
            }
            else {
                this.profilePic = "/images/registertenant/Profile-PNG-Photo.png";
            }
        },
        handleInput(index) {
            const currentInput = this.otpdigits[index];
            if (currentInput.length === 1 && index < 5) {
                this.$refs[`otpInput${index + 1}`]?.focus();
            }
        },
        getOtpCode() {
            return this.otpdigits.join('');
        },

        startOtpTimer(timerValue) {
            const expirationTime = new Date(timerValue);
            const currentTime = new Date();
            let remainingSeconds = Math.floor((expirationTime - currentTime) / 1000);

            this.otpTimer = remainingSeconds;

            if (this.otpInterval) {
                clearInterval(this.otpInterval);
            }

            this.otpInterval = setInterval(() => {
                if (remainingSeconds <= 0) {
                    clearInterval(this.otpInterval);
                    this.otpTimer = 0;
                } else {
                    this.otpTimer = remainingSeconds;
                    remainingSeconds--;
                }
            }, 1000);
        },


        fill() {
            this.profilePic = "/images/registertenant/Profile-PNG-Photo.png";
            this.profileFile = null;
            this.firstname = '';
            this.lastname = '';
            this.email = '';
            this.password = '';
            this.password_confirmation = '';
            this.phonenumber = '';
            this.gender = '';
            this.postalcode = '';
            this.currentaddress = '';
            this.message = '';
            this.errors = {};
            this.isLoading = false;
            this.otpTimer = 0;
            this.selectedRegion = "";
            this.selectedProvince = "";
            this.selectedCity = "";
            this.cities = [];
            this.otpdigits = Array(6).fill('');
            this.provinces = [];
        },
    },
    computed: {
        formattedTime() {
            const minutes = Math.floor(this.otpTimer / 60);
            const seconds = this.otpTimer % 60;
            return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        },
    },


};



</script>
<style scoped>
.container-toast {
    position: fixed;
    bottom: 1.5rem;
    /* near bottom */
    right: 1.5rem;
    /* align to right */
    transform: none;
    /* no horizontal centering */
    width: 95%;
    max-width: 30rem;
    z-index: 1000;
    opacity: 0;
    transition: all 0.5s ease;
}

@keyframes slideUpBounce {
    0% {
        bottom: -10rem;
        opacity: 0;
    }

    70% {
        bottom: 1.5rem;
    }

    100% {
        bottom: 1.5rem;
        opacity: 1;
    }
}

.container-toast.show {
    bottom: 1.5rem;
    opacity: 1;
    animation: slideUpBounce 0.6s ease forwards;
}
</style>
