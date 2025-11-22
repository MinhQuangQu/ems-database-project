<?php
include 'db_connection.php';
include 'parts/header.php';
include 'parts/sidebar.php';
?>
<div class="flex-1 p-6 ml-64">
  <h1 class="text-2xl font-bold mb-4">Add Employee</h1>

  <form action="employee_add_process.php" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded shadow max-w-lg">
    <input name="full_name" type="text" placeholder="Full name" class="w-full border p-2 rounded mb-3" required>
    <select id="departmentSelect" name="department_id" class="w-full border p-2 rounded mb-3" required>
      <option value="">Loading departments...</option>
    </select>
    <select name="gender" class="w-full border p-2 rounded mb-3" required>
      <option value="">Select gender</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
      <option value="Other">Other</option>
    </select>
    <input name="dob" type="date" class="w-full border p-2 rounded mb-3" required>
    <input name="phone_number" type="text" placeholder="Phone" class="w-full border p-2 rounded mb-3">
    <input name="email" type="email" placeholder="Email" class="w-full border p-2 rounded mb-3" required>
    <input name="position" type="text" placeholder="Position" class="w-full border p-2 rounded mb-3">
    <input name="base_salary" type="number" step="0.01" placeholder="Base salary" class="w-full border p-2 rounded mb-3" required>
    <label class="block mb-2">Avatar (optional)</label>
    <input name="avatar" type="file" accept="image/*" class="mb-4">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Employee</button>
  </form>
</div>

<?php include 'parts/footer.php'; ?>
