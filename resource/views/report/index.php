<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="p-6 ml-64">

    <!-- PAGE TITLE -->
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Reports & Analytics</h1>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Average Salary -->
        <div class="bg-white shadow rounded-xl p-5 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Average Salary</p>
            <h2 class="text-2xl font-bold text-gray-800">
                <?php echo number_format($avgSalary['avg_salary'], 0); ?> VND
            </h2>
        </div>

        <!-- Total Bonus -->
        <div class="bg-white shadow rounded-xl p-5 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Total Bonus This Month</p>
            <h2 class="text-2xl font-bold text-gray-800">
                <?php echo number_format($totalBonus['total_bonus'], 0); ?> VND
            </h2>
        </div>

        <!-- Employee Count -->
        <div class="bg-white shadow rounded-xl p-5 border-l-4 border-orange-500">
            <p class="text-gray-500 text-sm">Total Employees in Projects</p>
            <h2 class="text-2xl font-bold text-gray-800">
                <?php echo $employeeProjectCount['total_employee']; ?>
            </h2>
        </div>
    </div>


    <!-- TABLE SECTION -->
    <div class="bg-white shadow-lg rounded-xl p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Salary Summary by Department</h2>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Print Report
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Department</th>
                        <th class="px-4 py-2 text-left">Total Salary</th>
                        <th class="px-4 py-2 text-left">Month</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $viewData->fetch_assoc()): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo $row['department_name']; ?></td>
                            <td class="px-4 py-2"><?php echo number_format($row['total_salary'], 0); ?> VND</td>
                            <td class="px-4 py-2"><?php echo $row['month']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>


    <!-- SP RESULT TABLE -->
    <div class="bg-white shadow-lg rounded-xl p-6 mt-8">

        <h2 class="text-xl font-semibold mb-4">Payroll Report</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-green-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2">Department</th>
                        <th class="px-4 py-2">Total Employees</th>
                        <th class="px-4 py-2">Total Salary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $spData->fetch_assoc()): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo $row['department_name']; ?></td>
                            <td class="px-4 py-2"><?php echo $row['total_employees']; ?></td>
                            <td class="px-4 py-2"><?php echo number_format($row['total_salary'], 0); ?> VND</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>


    <!-- CHART SECTION -->
    <div class="bg-white shadow-lg rounded-xl p-6 mt-8">
        <h2 class="text-xl font-semibold mb-4">Salary Cost by Department</h2>
        <canvas id="salaryChart" height="110"></canvas>
    </div>

</div>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('salaryChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: "Total Salary",
            data: <?php echo json_encode($chartValues); ?>,
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
