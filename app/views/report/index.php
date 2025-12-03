<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../partials/flash_message.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl rounded-2xl p-10 w-full max-w-4xl text-blue-800 mx-auto my-10">

    <h1 class="text-3xl font-bold text-center mb-8">Employee Report</h1>

    <!-- SEARCH EMPLOYEE -->
    <div class="flex items-center space-x-3 mb-6">
        <form method="get" class="flex w-full">
            <input id="searchEmployee" name="search" type="text" placeholder="Search employee by name..."
                   value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                   class="flex-1 px-4 py-3 bg-white/40 border border-white/30 rounded-lg text-blue-800 placeholder-blue-500 focus:outline-none focus:border-blue-600">
            <button type="submit" class="px-6 py-3 bg-white text-blue-800 font-semibold rounded-lg shadow-lg hover:bg-blue-100 transition">
                Search
            </button>
        </form>
    </div>

    <!-- EMPLOYEE BASIC INFO -->
    <div id="employeeInfo" class="bg-white/50 p-6 border border-white/40 rounded-xl mb-6">
        <h2 class="text-xl font-semibold mb-3">Employee Information</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($employee['full_name'] ?? '—') ?></p>
        <p><strong>Employee ID:</strong> <?= htmlspecialchars($employee['employee_id'] ?? '—') ?></p>
        <p><strong>Department:</strong> <?= htmlspecialchars($employee['department_name'] ?? '—') ?></p>
    </div>

    <!-- ATTENDANCE -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-3">Attendance Overview</h2>
        <canvas id="attendanceChart" class="w-full h-64"></canvas>
    </div>

    <!-- SALARY INFO -->
    <div class="bg-white/50 p-6 border border-white/40 rounded-xl mb-6">
        <h2 class="text-xl font-semibold mb-3">Salary Details</h2>
        <p><strong>Most Recent Salary:</strong></p>
        <p><strong>Amount:</strong> <?= $payroll['total_amount'] ?? '—' ?></p>
        <p><strong>Date:</strong> <?= $payroll['payment_date'] ?? '—' ?></p>
        <p><strong>Status:</strong> <?= $payroll['payment_status'] ?? '—' ?></p>
    </div>

    <div class="flex justify-between items-center mt-6">
        <a href="<?= $base_url ?>/employee/index" class="text-blue-700 font-semibold hover:underline">Return</a>
        <button id="btnExportPDF" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-lg hover:bg-green-700 transition">
            Export PDF
        </button>
    </div>

    <footer class="text-center py-4 text-gray-700">&copy; 2025 EMS System. All rights reserved.</footer>

</div>

<script>
const attendanceChart = new Chart(document.getElementById("attendanceChart"), {
    type: "bar",
    data: {
        labels: ["Present", "Absent", "On Leave"],
        datasets: [{
            label: "Attendance",
            backgroundColor: "#2563eb",
            data: [
                <?= $attendanceStats['Present'] ?? 0 ?>,
                <?= $attendanceStats['Absent'] ?? 0 ?>,
                <?= $attendanceStats['On Leave'] ?? 0 ?>
            ],
        }],
    },
});

</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
