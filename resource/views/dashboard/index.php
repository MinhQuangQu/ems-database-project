<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard - Employee Management System</h1>
            <p class="text-gray-600"><?php echo htmlspecialchars($currentDate); ?></p>
        </div>

        <!-- Welcome Card -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <p class="text-green-600 font-bold text-lg">Welcome, <?php echo htmlspecialchars($adminName); ?>!</p>
            <p class="mt-2 text-gray-600">Admin ID: <?php echo htmlspecialchars($adminId); ?></p>
            <p class="mb-4 text-gray-600">You are successfully logged in.</p>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Employees</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo $stats['total_employees']; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Present Today</h3>
                <p class="text-3xl font-bold text-green-600"><?php echo $stats['present_today']; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Departments</h3>
                <p class="text-3xl font-bold text-purple-600"><?php echo $stats['total_departments']; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Active Employees</h3>
                <p class="text-3xl font-bold text-orange-600"><?php echo $stats['active_employees']; ?></p>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-xl font-semibold mb-4">Recent Activities</h3>
            <div class="space-y-3">
                <?php foreach ($recentActivities as $activity): ?>
                <div class="flex justify-between items-center border-b pb-2">
                    <span class="text-gray-700"><?php echo htmlspecialchars($activity['action']); ?></span>
                    <span class="text-sm text-gray-500"><?php echo htmlspecialchars($activity['time']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="/CSDL/public/index.php?path=/employees" 
               class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition duration-200">
                Manage Employees
            </a>
            <a href="/CSDL/public/index.php?path=/attendance" 
               class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-200">
                View Attendance
            </a>
            <a href="/CSDL/public/index.php?path=/logout" 
               class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition duration-200">
                Logout
            </a>
        </div>
    </div>

    <script>
    // Có thể thêm JavaScript để load dynamic data
    document.addEventListener('DOMContentLoaded', function() {
        // Load additional data via AJAX nếu cần
        fetch('/CSDL/public/index.php?path=/api/dashboard-data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Dashboard data loaded:', data.data);
                }
            });
    });
    </script>
</body>
</html>