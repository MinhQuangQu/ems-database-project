// assets/js/departments.js
document.addEventListener('DOMContentLoaded', function() {

  window.confirmDelete = function(id) {
    const ok = confirm('Bạn có chắc muốn xóa phòng ban này? (Nếu có nhân viên trong phòng ban, bạn phải chuyển họ trước.)');
    if (ok) {
      window.location.href = 'department_delete.php?id=' + encodeURIComponent(id);
    }
  }

  // Load departments into select (if element exists)
  const deptSelect = document.getElementById('departmentSelect');
  if (deptSelect) {
    fetch('departments_api.php')
      .then(r => r.json())
      .then(data => {
        // clear
        deptSelect.innerHTML = '<option value="">Select department</option>';
        data.forEach(d => {
          const opt = document.createElement('option');
          opt.value = d.department_id;
          opt.textContent = d.department_name;
          deptSelect.appendChild(opt);
        });
      })
      .catch(err => {
        console.error('Failed to load departments', err);
      });
  }

});
