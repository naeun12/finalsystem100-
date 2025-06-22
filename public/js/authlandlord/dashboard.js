const sidebar = document.getElementById('sidebarMenu');
const toggleBtn = document.getElementById('sidebarToggle');

// Toggle sidebar visibility and apply overlay class on body
toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevent the click from bubbling up to the document
    sidebar.classList.toggle('show-sidebar');
    document.body.classList.toggle('sidebar-open');
});

document.addEventListener('click', (e) => {
    if (sidebar.classList.contains('show-sidebar') &&
        !sidebar.contains(e.target) &&
        !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('show-sidebar');
        document.body.classList.remove('sidebar-open');
    }
});

sidebar.addEventListener('click', (e) => {
    e.stopPropagation();
});