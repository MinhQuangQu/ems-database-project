<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<main id="mainContent" class="flex-1 p-8 transition-all duration-300">
    <div class="w-full max-w-6xl mx-auto">
        <h2 class="text-3xl text-blue-800 font-bold text-center mb-8">Dashboard Overview</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white shadow-xl rounded-2xl p-6 text-center">
                <h2 class="text-gray-500 font-semibold">Total Employees</h2>
                <p class="text-3xl font-bold text-blue-700 mt-2"><?= $dashboardData['totalEmployees'] ?? 0 ?></p>
            </div>
            <div class="bg-white shadow-xl rounded-2xl p-6 text-center">
                <h2 class="text-gray-500 font-semibold">Departments</h2>
                <p class="text-3xl font-bold text-blue-700 mt-2"><?= $dashboardData['totalDepartments'] ?? 0 ?></p>
            </div>
            <div class="bg-white shadow-xl rounded-2xl p-6 text-center">
                <h2 class="text-gray-500 font-semibold">Total Payroll</h2>
                <p class="text-3xl font-bold text-blue-700 mt-2">$<?= number_format($dashboardData['totalPayroll'] ?? 0,2) ?></p>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-2xl p-6">
            <h2 class="text-xl font-bold mb-6">Recent Employees</h2>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Department</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($dashboardData['recentEmployees'])): ?>
                        <?php foreach ($dashboardData['recentEmployees'] as $emp): ?>
                            <tr>
                                <td class="px-6 py-4"><?= htmlspecialchars($emp['id']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($emp['name']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($emp['email']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($emp['department']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="px-6 py-4 text-center" colspan="4">No employees found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
