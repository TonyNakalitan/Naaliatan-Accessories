$(document).ready(function() {
    // Check if table has data rows (not just empty state)
    const tableBody = document.getElementById('orderTableBody');
    const hasData = tableBody && tableBody.querySelectorAll('tr').length > 0 && 
                    !tableBody.querySelector('td[colspan]');
    
    if (hasData) {
        const headerCols = $('#myTable thead tr th').length;
        const firstRowCols = $('#myTable tbody tr:first td').length;
        
        if (headerCols === firstRowCols) {
            $('#myTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": false,
                "info": true,
                "lengthChange": true,
                "order": [[6, 'desc']], // Sort by date descending
                "columnDefs": [
                    { "orderable": false, "targets": 7 }
                ]
            });
        }
    }
});

// Global search functionality
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

// Delivery Filter Logic
const deliveryFilter = document.getElementById('deliveryFilter');
if (deliveryFilter) {
    deliveryFilter.addEventListener('change', function() {
        const selectedDelivery = this.value.toLowerCase();
        const table = $('#myTable').DataTable();
        
        if (table) {
            if (selectedDelivery === 'all') {
                table.column(3).search('').draw();
            } else {
                table.column(3).search(selectedDelivery).draw();
            }
        }
    });
}
