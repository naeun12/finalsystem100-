<template>
    <div class="container-fluid h-100">
        <!-- Main Layout -->
        <div class="row h-100">
            <!-- Sidebar -->
            <div class="col-md-3 bg-light border-end">
                <div class="p-3">
                    <h5>Tenants</h5>
                    <input type="text" class="form-control mb-3" placeholder="Search Tenants" />
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <img src="https://via.placeholder.com/30 " alt="Profile Icon" class="rounded-circle me-2" />
                            Lance Monsanto
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 p-3">
                <!-- Tenant Profile Section -->
                <div class="d-flex align-items-center mb-3">
                    <img src="https://via.placeholder.com/30 " alt="Tenant Profile Icon" class="rounded-circle me-2" />
                    <span>Tenants</span>
                </div>

                <!-- Chat Section -->
                <div class="card flex-grow-1 overflow-auto" style="height: calc(100% - 120px);">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-2">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">Landlord</span>
                                <img src="https://via.placeholder.com/30 " alt="Landlord Profile Icon"
                                    class="rounded-circle" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Field -->
                <div class="input-group mt-3">
                    <input type="text" class="form-control" placeholder="Type a message" />
                    <button class="btn btn-primary">Send</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ChatComponent",
};
</script>

<style scoped>
html,
body {
    height: 100%;
}

.container-fluid {
    height: 100%;
}

.row {
    margin: 0;
}
</style>