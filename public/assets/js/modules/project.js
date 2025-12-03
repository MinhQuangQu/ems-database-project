document.addEventListener("DOMContentLoaded", function () {

    const bulkForm = document.getElementById("project-bulk-form");
    const bulkActionSelect = document.getElementById("bulk-action");
    const checkboxes = document.querySelectorAll(".project-checkbox");
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
            if (!action) { alert("Vui lòng chọn hành động."); return; }

            const selectedIds = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            if (!selectedIds.length) { alert("Chưa chọn dự án nào."); return; }

            if (action === "delete" && !confirm("Bạn có chắc muốn xóa các dự án?")) return;

            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            fetch("/project/bulkAction", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({ action, ids: selectedIds })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) { alert(data.message || "Thao tác thành công."); location.reload(); }
                else { alert(data.message || "Có lỗi xảy ra."); }
            })
            .catch(err => { console.error(err); alert("Lỗi máy chủ."); });
        });
    }

    // Individual delete
    document.querySelectorAll(".project-delete-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            if (!confirm("Bạn có chắc muốn xóa dự án này?")) return;

            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            fetch(`/project/delete/${id}`, {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) { alert("Xóa thành công."); location.reload(); }
                else { alert(data.message || "Xóa thất bại."); }
            })
            .catch(err => { console.error(err); alert("Lỗi máy chủ."); });
        });
    });
});
