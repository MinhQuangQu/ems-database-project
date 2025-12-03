document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // 1. Bulk action buttons
    // ==========================
    const bulkForm = document.getElementById("attendance-bulk-form");
    const bulkActionSelect = document.getElementById("bulk-action");
    const bulkSubmitBtn = document.getElementById("bulk-submit");
    const checkboxes = document.querySelectorAll(".attendance-checkbox");
    const selectAllCheckbox = document.getElementById("select-all");

    // Select / Deselect all
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        });
    }

    // Bulk form submit
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

            if (selectedIds.length === 0) {
                alert("Vui lòng chọn ít nhất một bản ghi.");
                return;
            }

            if (action === "delete" && !confirm("Bạn có chắc muốn xóa các bản ghi đã chọn?")) {
                return;
            }

            // Lấy CSRF token từ hidden input
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            // ==========================
            // 2. AJAX POST request
            // ==========================
            fetch("/attendance/bulkAction", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    action: action,
                    ids: selectedIds
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || "Thao tác thành công.");
                    location.reload(); // Reload trang sau khi thao tác
                } else {
                    alert(data.message || "Có lỗi xảy ra khi xử lý.");
                }
            })
            .catch(err => {
                console.error("Bulk action error:", err);
                alert("Lỗi máy chủ khi thực hiện thao tác hàng loạt.");
            });
        });
    }

    // ==========================
    // 3. Individual delete button
    // ==========================
    const deleteBtns = document.querySelectorAll(".attendance-delete-btn");
    deleteBtns.forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const id = this.dataset.id;

            if (!confirm("Bạn có chắc muốn xóa bản ghi này?")) return;

            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            fetch(`/attendance/delete/${id}`, {
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
    // 4. Date filter / search form
    // ==========================
    const searchForm = document.getElementById("attendance-search-form");
    if (searchForm) {
        searchForm.addEventListener("submit", function (e) {
            // Optional: validate date or search input
        });
    }

    // ==========================
    // 5. Export CSV button
    // ==========================
    const exportBtn = document.getElementById("attendance-export-btn");
    if (exportBtn) {
        exportBtn.addEventListener("click", function () {
            const month = document.getElementById("filter-month").value || "";
            window.location.href = `/attendance/export?month=${month}`;
        });
    }
});
