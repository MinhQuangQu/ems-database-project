/**
 * public/assets/js/main.js
 * Main JS entry point cho EMS
 */

document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // 1. Khởi tạo Flatpickr
    // ==========================
    if (typeof flatpickr !== "undefined") {
        const datePickers = document.querySelectorAll(".datepicker");
        datePickers.forEach(input => {
            flatpickr(input, { dateFormat: "Y-m-d", allowInput: true, defaultDate: input.value || null });
        });

        const timePickers = document.querySelectorAll(".timepicker");
        timePickers.forEach(input => {
            flatpickr(input, { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });
        });

        const rangePickers = document.querySelectorAll(".daterange");
        rangePickers.forEach(input => {
            flatpickr(input, { mode: "range", dateFormat: "Y-m-d", allowInput: true });
        });
    }

    // ==========================
    // 2. Khởi tạo DataTables
    // ==========================
    if (typeof $ !== "undefined" && $.fn.DataTable) {
        const tables = document.querySelectorAll(".datatable");
        tables.forEach(table => {
            $(table).DataTable({
                responsive: true,
                pageLength: 15,
                lengthMenu: [5, 10, 15, 25, 50, 100],
                order: [],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'print'],
                language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json" }
            });
        });
    }

    // ==========================
    // 3. Flash messages auto hide
    // ==========================
    const flashMessages = document.querySelectorAll(".flash-message");
    flashMessages.forEach(msg => {
        setTimeout(() => msg.remove(), 5000);
    });

    // ==========================
    // 4. Sidebar toggle
    // ==========================
    const sidebarToggle = document.querySelector("#sidebarToggle");
    const sidebar = document.querySelector("#sidebar");
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", () => sidebar.classList.toggle("collapsed"));
    }

    // ==========================
    // 5. Tooltips (Bootstrap / Tippy.js)
    // ==========================
    if (typeof tippy !== "undefined") {
        tippy('[data-tippy-content]', { placement: 'top', animation: 'shift-away', theme: 'light' });
    }

    // ==========================
    // 6. Initialize Charts (Chart.js)
    // ==========================
    const charts = document.querySelectorAll(".chartjs-chart");
    charts.forEach(canvas => {
        const ctx = canvas.getContext("2d");
        const chartData = JSON.parse(canvas.dataset.chart || '{}');
        const chartOptions = JSON.parse(canvas.dataset.options || '{}');

        if (ctx && chartData) {
            new Chart(ctx, {
                type: chartData.type || 'bar',
                data: chartData.data || {},
                options: chartOptions
            });
        }
    });

    // ==========================
    // 7. Bind Delete confirmation
    // ==========================
    const deleteBtns = document.querySelectorAll(".delete-btn");
    deleteBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            if (!Helpers.confirmAction("Bạn có chắc muốn xóa bản ghi này?")) {
                e.preventDefault();
            }
        });
    });

    // ==========================
    // 8. Initialize Select2 (if used)
    // ==========================
    if (typeof $ !== "undefined" && $.fn.select2) {
        $(".select2").select2({ width: '100%' });
    }

});
