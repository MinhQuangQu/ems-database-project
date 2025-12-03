<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container mx-auto px-4 py-8">

    <!-- Title -->
    <h1 class="text-3xl font-bold text-blue-800 mb-6">Departments</h1>

    <!-- Search + Add Button -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 space-y-3 md:space-y-0">

        <!-- Search -->
        <form method="get" class="relative w-full md:w-1/3">
            <input type="text" name="search"
                   placeholder="Search departments..."
                   value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                   class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
            <i class="fa fa-search absolute right-3 top-3 text-gray-500"></i>
        </form>

        <!-- Add Button -->
        <div class="flex items-center space-x-3">
            <a href="<?= $base_url ?>/department/create"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                <i class="fa fa-plus"></i> Add
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-blue-100">
            <tr>
                <?php if (!empty($departments ?? null)): ?>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Department Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Employees</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Actions</th>
                <?php elseif (!empty($employees ?? null)): ?>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Full Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Department</th>
                <?php endif; ?>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
            <?php if (!empty($departments ?? null)) : ?>
                <?php foreach ($departments as $dept) : ?>
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-6 py-3"><?= htmlspecialchars($dept['department_id']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($dept['department_name']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($dept['location'] ?? '') ?></td>
                        <td class="px-6 py-3"><?= $dept['employee_count'] ?? 0 ?></td>
                        <td class="px-6 py-3 space-x-3">
                            <a href="<?= $base_url ?>/department/edit/<?= $dept['department_id'] ?>" class="text-blue-600 hover:text-blue-800">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form method="post"
                                  action="<?= $base_url ?>/department/delete/<?= $dept['department_id'] ?>"
                                  class="inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa phòng ban này?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php elseif (!empty($employees ?? null)) : ?>
                <?php foreach ($employees as $emp) : ?>
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-6 py-3"><?= $emp['employee_id'] ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($emp['full_name']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($emp['email'] ?? '') ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($emp['phone_number'] ?? '') ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($emp['position'] ?? '') ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($emp['department_name'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>

            <?php else : ?>
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-500">No data found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
