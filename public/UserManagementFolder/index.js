$(document).ready(function() {
    // Check if table has data rows (not just empty state)
    const tableBody = document.getElementById('userTableBody');
    const hasData = tableBody && tableBody.querySelectorAll('tr').length > 0 && 
                    !tableBody.querySelector('td[colspan]');
    
    if (hasData) {
        $('#myTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "searching": false, // Disable built-in search, we use custom
            "info": true,
            "lengthChange": true,
            "order": [[0, 'desc']], // Sort by ID descending
            "columnDefs": [
                { "orderable": false, "targets": 8 } // Disable sorting on Actions column
            ]
        });
    }
});

// Real-time search filter
const globalSearch = document.getElementById('globalSearch');
if (globalSearch) {
    globalSearch.addEventListener('keyup', function() {
        const table = $('#myTable').DataTable();
        if (table) {
            table.search(this.value).draw();
        }
    });
}

// Role Filter Logic
const roleFilter = document.getElementById('roleFilter');
if (roleFilter) {
    roleFilter.addEventListener('change', function() {
        const selectedRole = this.value.toLowerCase();
        const table = $('#myTable').DataTable();
        
        if (table) {
            if (selectedRole === 'all roles') {
                table.column(4).search('').draw();
            } else {
                table.column(4).search(selectedRole).draw();
            }
        }
    });
}