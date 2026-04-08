$(document).ready(function() {
    // Check if table has data rows (not just empty state)
    const tableBody = document.getElementById('activityTableBody');
    const hasData = tableBody && tableBody.querySelectorAll('tr').length > 0 && 
                    !tableBody.querySelector('td[colspan]');
    
    if (hasData) {
        // Count actual columns in the table
        const headerCols = $('#myTable thead tr th').length;
        const firstRowCols = $('#myTable tbody tr:first td').length;
        
        // Only initialize if column counts match
        if (headerCols === firstRowCols) {
            $('#myTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": false, // Disable built-in search, we use custom
                "info": true,
                "lengthChange": true,
                "order": [[0, 'desc']], // Sort by ID descending
                "columnDefs": [
                    { "orderable": true, "targets": "_all" }
                ]
            });
        }
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

// Action Filter Logic
const actionFilter = document.getElementById('actionFilter');
if (actionFilter) {
    actionFilter.addEventListener('change', function() {
        const selectedAction = this.value.toLowerCase();
        const table = $('#myTable').DataTable();
        
        if (table) {
            if (selectedAction === 'all') {
                table.column(2).search('').draw();
            } else {
                table.column(2).search(selectedAction).draw();
            }
        }
    });
}
