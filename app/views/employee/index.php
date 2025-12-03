<?php 
$title = "Employees | EMS NEU";
require_once __DIR__ . '/../layouts/header.php';
$baseUrl = $this->config['base_url'] ?? ''; // lấy base_url từ controller
?>

<div class="container mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-blue-800 mb-6">Employees</h1>

    <!-- Search + Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-3 md:space-y-0">

        <!-- Search -->
        <form method="get" action="<?= $baseUrl ?>/employee" class="relative w-full md:w-1/3">
            <input type="text" 
                   name="search" 
                   placeholder="Search employees..."
                   value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                   class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-300 focus:outline-none">
            <i class="fa fa-search absolute right-3 top-3 text-gray-500"></i>
        </form>

        <!-- Buttons -->
        <div class="flex items-center space-x-3">
            <a href="<?= $baseUrl ?>/employee/create"
               class="px-4 py-2 bg-white text-blue-700 font-semibold rounded-lg shadow hover:bg-gray-200 transition">
                <i class="fa fa-plus"></i> Add
            </a>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-blue-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Department</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Actions</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
            <?php if (!empty($employees)) : ?>
                <?php foreach ($employees as $emp) : ?>
                    <?php $empId = $emp['id'] ?? 0; ?>
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-6 py-3"><?= htmlspecialchars($empId) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($emp['full_name'] ?? '') ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($emp['email'] ?? '') ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($emp['department_name'] ?? '') ?></td>
                        <td class="px-6 py-3 space-x-3">
                            <a href="<?= $baseUrl ?>/employee/edit/<?= $empId ?>" 
                               class="text-blue-600 hover:text-blue-800">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form method="post" action="<?= $baseUrl ?>/employee/delete/<?= $empId ?>" 
                                  onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này?');"
                                  class="inline">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-600">
                        No data available
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    
        
    <!-- Pagination -->
    <?php if (!empty($totalPages) && $totalPages > 1) : ?>
        <div class="mt-6 flex justify-center">
            <div class="flex space-x-2">
                <?php for ($p = 1; $p <= $totalPages; $p++) : ?>
                    <a href="<?= $baseUrl ?>/employee?page=<?= $p ?>&search=<?= urlencode($filter['search'] ?? '') ?>"
                       class="px-4 py-2 rounded-lg shadow 
                       <?= ($p == $currentPage) 
                            ? 'bg-blue-600 text-white' 
                            : 'bg-white text-blue-600 hover:bg-gray-200' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
