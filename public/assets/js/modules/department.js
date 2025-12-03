document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // Bulk action checkboxes
    // ==========================
    const bulkForm = document.getElementById("department-bulk-form");
    const bulkActionSelect = document.getElementById("bulk-action");
    const checkboxes = document.querySelectorAll(".department-checkbox");
    const selectAllCheckbox = document.getElementById("select-all");

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", () => {
            checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        });
    }

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
                alert("Vui lòng chọn ít nhất một phòng ban.");
                return;
            }

            if (action === "delete" && !confirm("Bạn có chắc muốn xóa các phòng ban đã chọn?")) {
                return;
            }

            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            fetch("/department/bulkAction", {
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

    // Individual delete
    const deleteBtns = document.querySelectorAll(".department-delete-btn");
    deleteBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            if (!confirm("Bạn có chắc muốn xóa phòng ban này?")) return;

            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            fetch(`/department/delete/${id}`, {
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
                alert("Lỗi máy chủ khi xóa phòng ban.");
            });
        });
    });
});
