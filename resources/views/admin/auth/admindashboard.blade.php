@include('admin.auth.partials.header')

<div class="wrapper d-flex">
    <!-- Sidebar -->
    @include('admin.auth.partials.sidebar')

    <!-- Main Content -->
    <div class="content">
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h2 class="fw-bold">Welcome, {{ session('admin_name') }}</h2>
            <p class="text-muted">Choose an option from the sidebar to get started.</p>
        </div>

        <div id="admindashboard">
            <admin-dashboard></admin-dashboard>
        </div>
    </div>
</div>

@include('admin.auth.partials.footer')

<style>
/* Wrapper flexbox */
.wrapper {
    display: flex;
    min-height: 100vh;
    flex-direction: row;
    background: #f8f9fa;
}

/* Sidebar */
.sidebar {
    width: 260px;
    background: linear-gradient(180deg, #212529 0%, #343a40 100%);
    color: #fff;
    flex-shrink: 0;
    height: 100vh;
    border-right: 1px solid #495057;
    position: sticky;
    top: 0;
    transition: all 0.3s ease-in-out;
}
.sidebar h4 { font-weight: bold; }
.sidebar .nav-link {
    color: #adb5bd;
    padding: 10px 15px;
    margin: 4px 0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    transition: all 0.3s ease-in-out;
}
.sidebar .nav-link.active,
.sidebar .nav-link:hover {
    color: #fff;
    background-color: #495057;
    transform: translateX(5px);
}

/* Content */
.content {
    flex-grow: 1;
    padding: 30px;
}

/* Responsive: mobile */
@media (max-width: 992px) {
    .wrapper { flex-direction: column; }
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        border-right: none;
        border-bottom: 1px solid #495057;
    }
    .content { padding: 20px; }
}
</style>
