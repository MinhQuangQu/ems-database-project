<?php
$user = $_SESSION['user'] ?? null;
$base_url = $base_url ?? ''; // từ header.php
?>

<!-- Toggle Button -->
<button id="sidebarToggle" class="fixed top-4 left-4 z-50 bg-blue-600 text-white p-3 rounded-md shadow-lg dark:bg-gray-800">
    <i class="fa fa-bars fa-lg"></i>
</button>

<!-- Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-64 bg-gray-900 text-white transform -translate-x-full transition-transform duration-300 shadow-lg dark:bg-gray-800">
    <!-- Logo / App Name -->
    <div class="flex items-center justify-center py-6 border-b border-gray-700 px-6">
        <img src="<?= $base_url ?>/assets/images/neu-logo.png" alt="EMS Logo" class="h-10 w-auto">
        <span class="ml-2 font-bold text-lg">EMS System</span>
    </div>

    <!-- User info -->
    <?php if ($user): ?>
    <div class="px-6 py-4 border-b border-gray-700">
        <p class="font-semibold"><?= htmlspecialchars($user['full_name']) ?></p>
        <p class="text-sm text-gray-400"><?= htmlspecialchars($user['role'] ?? 'User') ?></p>
    </div>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="flex-1 px-6 py-6">
        <ul class="space-y-2">
            <li><a href="/dashboard" class="flex items-center px-2 py-2 rounded hover:bg-gray-700 transition-colors"><i class="fa fa-home"></i><span class="ml-3">Dashboard</span></a></li>
            <li><a href="/employee" class="flex items-center px-2 py-2 rounded hover:bg-gray-700 transition-colors"><i class="fa fa-users"></i><span class="ml-3">Nhân viên</span></a></li>
            <li><a href="/department" class="flex items-center px-2 py-2 rounded hover:bg-gray-700 transition-colors"><i class="fa fa-building"></i><span class="ml-3">Phòng ban</span></a></li>
            <li><a href="/attendance" class="flex items-center px-2 py-2 rounded hover:bg-gray-700 transition-colors"><i class="fa fa-calendar-check"></i><span class="ml-3">Điểm danh</span></a></li>
            <li><a href="/payroll" class="flex items-center px-2 py-2 rounded hover:bg-gray-700 transition-colors"><i class="fa fa-file-invoice-dollar"></i><span class="ml-3">Bảng lương</span></a></li>
            <li><a href="/report" class="flex items-center px-2 py-2 rounded hover:bg-gray-700 transition-colors"><i class="fa fa-chart-bar"></i><span class="ml-3">Báo cáo</span></a></li>
            <li><a href="/setting" class="flex items-center px-2 py-2 rounded hover:bg-gray-700 transition-colors"><i class="fa fa-cogs"></i><span class="ml-3">Cài đặt</span></a></li>
        </ul>
    </nav>

    <!-- Logout -->
    <?php if ($user): ?>
    <div class="px-6 py-4 border-t border-gray-700">
        <a href="/auth/logout" class="flex items-center px-2 py-2 rounded hover:bg-red-600 transition-colors">
            <i class="fa fa-sign-out-alt"></i>
            <span class="ml-3">Đăng xuất</span>
        </a>
    </div>
    <?php endif; ?>
</aside>

<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggleBtn = document.getElementById('sidebarToggle');

// Mở / đóng sidebar
toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
});

// Click vào overlay để đóng sidebar
overlay.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
});
</script>
