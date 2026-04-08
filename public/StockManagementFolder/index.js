$(document).ready(function() {
    // Initialize DataTables for Stock Inventory table
    const stockTableBody = document.getElementById('stockTableBody');
    const hasStockData = stockTableBody && stockTableBody.querySelectorAll('tr').length > 0 && 
                        !stockTableBody.querySelector('td[colspan]');
    
    if (hasStockData) {
        const headerCols = $('#stockInventoryTable thead tr th').length;
        const firstRowCols = $('#stockInventoryTable tbody tr:first td').length;
        
        if (headerCols === firstRowCols) {
            $('#stockInventoryTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": false,
                "info": true,
                "lengthChange": true,
                "order": [[5, 'desc']], // Sort by created date descending
                "columnDefs": [
                    { "orderable": false, "targets": 7 }
                ]
            });
        }
    }

    // Initialize DataTables for Low Stock table
    const lowStockTableBody = document.getElementById('lowStockTableBody');
    const hasLowStockData = lowStockTableBody && lowStockTableBody.querySelectorAll('tr').length > 0 && 
                           !lowStockTableBody.querySelector('td[colspan]');
    
    if (hasLowStockData) {
        const headerCols = $('#lowStockTable thead tr th').length;
        const firstRowCols = $('#lowStockTable tbody tr:first td').length;
        
        if (headerCols === firstRowCols) {
            $('#lowStockTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": false,
                "info": true,
                "lengthChange": true,
                "order": [[3, 'asc']], // Sort by current stock ascending
                "columnDefs": [
                    { "orderable": false, "targets": 6 }
                ]
            });
        }
    }
});

// Global search functionality
const globalSearch = document.getElementById('globalSearch');
if (globalSearch) {
    globalSearch.addEventListener('keyup', function() {
        const activeTab = document.querySelector('.tab-content.active');
        const tableId = activeTab.querySelector('table').id;
        const table = $('#' + tableId).DataTable();
        
        if (table) {
            table.search(this.value).draw();
        }
    });
}

// Tab switching functionality
const tabButtons = document.querySelectorAll('.tab-btn[data-tab]');
const tabContents = document.querySelectorAll('.tab-content');

tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        const targetTab = button.getAttribute('data-tab');
        
        // Remove active class from all buttons and contents
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked button and corresponding content
        button.classList.add('active');
        document.getElementById(targetTab).classList.add('active');
        
        // Recalculate column widths when switching tabs
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        
        // Clear global search when switching tabs
        if (globalSearch) {
            globalSearch.value = '';
        }
    });
});
