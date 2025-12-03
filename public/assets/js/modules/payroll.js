document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // 1. Bulk action checkboxes
    // ==========================
    const bulkForm = document.getElementById("payroll-bulk-form");
    const bulkActionSelect = document.getElementById("bulk-action");
    const checkboxes = document.querySelectorAll(".payroll-checkbox");
    const selectAllCheckbox = document.getElementById("select-all");

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", () => {
            checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        });
    }

    // ==========================
    // 2. Bulk form submit
    // ==========================
    if (bulkForm) {
        bulkForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const action = bulkActionSelect.value;
            if (!action) {
                alert("Vui lòng chọn hành động hàng loạt.");
                return;
            }

            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (!selectedIds.length) {
                alert("Vui lòng chọn ít nhất một bản ghi bảng lương.");
                return;
            }

            if (action === "delete" && !confirm("Bạn có chắc muốn xóa các bản ghi đã chọn?")) return;

            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            fetch("/payroll/bulkAction", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ action, ids: selectedIds })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || "Thao tác thành công.");
                    location.reload();
                } else {
                    alert(data.message || "Có lỗi xảy ra.");
                }
            })
            .catch(err => {
                console.error("Bulk action error:", err);
                alert("Lỗi máy chủ khi thực hiện thao tác hàng loạt.");
            });
        });
    }

    // ==========================
    // 3. Individual delete
    // ==========================
    const deleteBtns = document.querySelectorAll(".payroll-delete-btn");
    deleteBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            if (!confirm("Bạn có chắc muốn xóa bản ghi này?")) return;

            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            fetch(`/payroll/delete/${id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Xóa thành công.");
                    location.reload();
                } else {
                    alert(data.message || "Xóa thất bại.");
                }
            })
            .catch(err => {
                console.error("Delete error:", err);
                alert("Lỗi máy chủ khi xóa bản ghi.");
            });
        });
    });

    // ==========================
    // 4. Export CSV
    // ==========================
    const exportBtn = document.getElementById("payroll-export-btn");
    if (exportBtn) {
        exportBtn.addEventListener("click", function () {
            const month = document.getElementById("filter-month").value || "";
            const departmentId = document.getElementById("filter-department").value || 0;
            window.location.href = `/payroll/export?month=${month}&department_id=${departmentId}`;
        });
    }

    // ==========================
    // 5. Filter form submit
    // ==========================
    const filterForm = document.getElementById("payroll-filter-form");
    if (filterForm) {
        filterForm.addEventListener("submit", function () {
            // Optional: validate month format YYYY-MM
        });
    }

});
