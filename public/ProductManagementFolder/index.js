$(document).ready(function() {
    // Check if table has data rows (not just empty state)
    const tableBody = document.getElementById('productTableBody');
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
                    { "orderable": false, "targets": 7 } // Disable sorting on Actions column
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

// Status Filter Logic
const statusFilter = document.getElementById('statusFilter');
if (statusFilter) {
    statusFilter.addEventListener('change', function() {
        const selectedStatus = this.value.toLowerCase();
        const table = $('#myTable').DataTable();
        
        if (table) {
            if (selectedStatus === 'all') {
                table.column(5).search('').draw();
            } else {
                table.column(5).search(selectedStatus).draw();
            }
        }
    });
}

// Delete product function
function deleteProduct(url, productName, token) {
    if (confirm('Are you sure you want to delete "' + productName + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = token;
        
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
