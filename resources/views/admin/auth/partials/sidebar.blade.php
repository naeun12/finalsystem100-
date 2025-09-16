@include('admin.auth.partials.header')

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column p-3 shadow-lg">
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-person-badge-fill fs-3 me-2 text-warning"></i>
            <h4 class="text-white mb-0">Admin Panel</h4>
        </div>
        <hr class="text-secondary">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard', ['admin_id' => session('admin_id')]) }}" 
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.tenantmanagemnt', ['admin_id' => session('admin_id')]) }}" 
                   class="nav-link {{ request()->routeIs('admin.tenantmanagemnt') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Tenant Management
                </a>
            </li>
            <li>
                <a href="{{ route('admin.landlordmanagement', ['admin_id' => session('admin_id')]) }}" 
                   class="nav-link {{ request()->routeIs('admin.landlordmanagement') ? 'active' : '' }}">
                    <i class="bi bi-building me-2"></i> Landlord Management
                </a>
            </li>
        </ul>
        <hr class="text-secondary">
        <div>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger w-100">Logout</button>
        </form>

        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <admin-dashboard></admin-dashboard> <!-- Vue component mounted here -->
    </div>
</div>

<style>
body {
    min-height: 100vh;
    margin: 0;
    font-family: 'Arial', sans-serif;
    background: #f8f9fa;
}
.sidebar {
    width: 260px;
    background: linear-gradient(180deg, #212529 0%, #343a40 100%);
    color: #fff;
    flex-shrink: 0;
    height: 100vh;
    border-right: 1px solid #495057;
    position: sticky;
    top: 0;
}
.sidebar h4 {
    font-weight: bold;
}
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
.content {
    flex-grow: 1;
    padding: 30px;
    
}
</style>
