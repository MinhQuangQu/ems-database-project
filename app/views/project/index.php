<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-blue-800 mb-6">Projects</h1>

    <!-- Search + Add -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 space-y-3 md:space-y-0">
        <form method="get" class="flex w-full md:w-1/3">
            <input type="text" name="search" placeholder="Search projects..."
                   value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
                   class="w-full px-4 py-2 border rounded-l-lg focus:ring-2 focus:ring-blue-300 focus:outline-none">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">Search</button>
        </form>

        <a href="<?= $base_url ?>/project/create"
           class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 flex items-center space-x-2">
            <i class="fa fa-plus"></i><span>Add Project</span>
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-blue-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">#</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Start Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">End Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Budget</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Department</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $index => $project): ?>
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-6 py-3"><?= ($currentPage - 1) * $perPage + $index + 1 ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($project['project_name']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($project['start_date']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($project['end_date'] ?? '-') ?></td>
                        <td class="px-6 py-3">$<?= number_format($project['budget'], 2) ?></td>
                        <td class="px-6 py-3">
                            <?= htmlspecialchars($project['department_name'] ?? '-') ?>
                        </td>

                        <td class="px-6 py-3 space-x-2">
                            <a href="<?= $base_url ?>/project/edit/<?= $project['project_id'] ?>"
                               class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Edit</a>
                            <a href="<?= $base_url ?>/project/delete/<?= $project['project_id'] ?>"
                               onclick="return confirm('Are you sure?');"
                               class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">No projects found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
