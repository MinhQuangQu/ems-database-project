document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // 1. Initialize all DataTables
    // ==========================
    const tables = document.querySelectorAll(".datatable");
    tables.forEach(table => {
        $(table).DataTable({
            responsive: true,
            pageLength: 15,
            lengthMenu: [5, 10, 15, 25, 50, 100],
            order: [], // disable initial sorting
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json"
            },
            dom: 'Bfrtip', // Show buttons
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        });
    });

    // ==========================
    // 2. Bulk select checkboxes
    // ==========================
    const selectAllCheckboxes = document.querySelectorAll(".select-all");
    selectAllCheckboxes.forEach(selectAll => {
        selectAll.addEventListener("change", function () {
            const tableId = this.dataset.table;
            const checkboxes = document.querySelectorAll(`#${tableId} .row-checkbox`);
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });

    // ==========================
    // 3. Confirm delete action
    // ==========================
    const deleteBtns = document.querySelectorAll(".delete-btn");
    deleteBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            if (!confirm("Bạn có chắc muốn xóa bản ghi này?")) {
                return false;
            }
        });
    });

    // ==========================
    // 4. Filter form submit
    // ==========================
    const filterForms = document.querySelectorAll(".datatable-filter-form");
    filterForms.forEach(form => {
        form.addEventListener("submit", function () {
            // Optional: validate inputs before submit
        });
    });

});
