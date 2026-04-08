$(document).ready(function() {
    // Check if table has data rows (not just empty state)
    const tableBody = document.getElementById('characterTableBody');
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
                    { "orderable": false, "targets": 6 } // Disable sorting on Actions column
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

// Alignment Filter Logic
const alignmentFilter = document.getElementById('alignmentFilter');
if (alignmentFilter) {
    alignmentFilter.addEventListener('change', function() {
        const selectedAlignment = this.value.toLowerCase();
        const table = $('#myTable').DataTable();
        
        if (table) {
            if (selectedAlignment === 'all') {
                table.column(3).search('').draw();
            } else {
                table.column(3).search(selectedAlignment).draw();
            }
        }
    });
}
