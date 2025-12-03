<?php
$title = "Edit Employee | EMS NEU";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../partials/flash_message.php';

// Lấy base URL từ config
$baseUrl = $this->config['base_url'] ?? '';
?>

<main id="mainContent" class="flex-1 p-8 transition-all duration-300 min-h-screen">
    <div class="w-full max-w-3xl mx-auto bg-white shadow-2xl rounded-2xl p-10">

        <h1 class="text-3xl font-bold text-center text-blue-800 mb-8">
            Update Employee Information
        </h1>

        <form action="<?= $baseUrl ?>/employee/update/<?= htmlspecialchars($employee['employee_id'] ?? '') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- Full name -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Full name</label>
                <input name="full_name" type="text"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['full_name'] ?? ($employee['full_name'] ?? '')) ?>" required>
            </div>

            <!-- Email -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Email</label>
                <input name="email" type="email"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['email'] ?? ($employee['email'] ?? '')) ?>" required>
            </div>

            <!-- Phone number -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Mobile number</label>
                <input name="phone_number" type="text"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['phone_number'] ?? ($employee['phone_number'] ?? '')) ?>">
            </div>

            <!-- Date of birth -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Date of birth</label>
                <input name="date_of_birth" type="date"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['date_of_birth'] ?? ($employee['date_of_birth'] ?? '')) ?>">
            </div>

            <!-- Address -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Address</label>
                <input name="address" type="text"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['address'] ?? ($employee['address'] ?? '')) ?>">
            </div>

            <!-- Gender -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Gender</label>
                <?php $gender = $_POST['gender'] ?? ($employee['gender'] ?? ''); ?>
                <select name="gender" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= $gender === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <!-- Department -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Department</label>
                <select name="department_id"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required>
                    <option value="">-- Please choose your department --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['department_id'] ?>"
                            <?= (($_POST['department_id'] ?? ($employee['department_id'] ?? '')) == $dept['department_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Position -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Position</label>
                <input name="position" type="text"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['position'] ?? ($employee['position'] ?? '')) ?>">
            </div>

            <!-- Hire date -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Hire date</label>
                <input name="hire_date" type="date"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['hire_date'] ?? ($employee['hire_date'] ?? '')) ?>">
            </div>

            <!-- Base Salary -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Base Salary (USD)</label>
                <input name="base_salary" type="number" step="0.01" min="0"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['base_salary'] ?? ($employee['base_salary'] ?? '')) ?>"
                       placeholder="Example: 1200.00">
            </div>

            <!-- Buttons -->
            <div class="col-span-1 md:col-span-2 flex justify-between items-center mt-4">
                <a href="<?= $baseUrl ?>/employee"
                   class="text-blue-700 font-semibold hover:underline">
                    Return
                </a>

                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    Update
                </button>
            </div>

        </form>
    </div>
</main>

<footer class="text-center py-4 text-gray-700">
    © 2025 EMS System. All rights reserved.
</footer>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
