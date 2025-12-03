<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../partials/flash_message.php';
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-blue-800 mb-6">Attendance</h1>

    <!-- Search -->
    <form method="get" class="flex mb-6 space-x-3">
        <input type="text" name="search" placeholder="Search attendance..."
               value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
               class="flex-1 px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
            <i class="fa fa-search"></i> Search
        </button>
        <a href="<?= $base_url ?>/attendance/tracking"
           class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
            <i class="fa fa-plus"></i> Add
        </a>
    </form>

    <!-- Attendance Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-blue-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Employee Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Work Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Status</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
            <?php foreach ($attendances as $att): ?>
                <tr class="hover:bg-blue-50 transition">
                    <td class="px-6 py-3"><?= $att['attendance_id'] ?></td>
                    <td class="px-6 py-3"><?= htmlspecialchars($att['full_name']) ?></td>
                    <td class="px-6 py-3"><?= $att['work_date'] ?></td>
                    <td class="px-6 py-3">
                        <?php
                        $statusClass = [
                            'Present' => 'bg-green-200 text-green-700',
                            'Absent' => 'bg-red-200 text-red-700',
                            'Late' => 'bg-yellow-200 text-yellow-700',
                            'Leave Approved' => 'bg-gray-200 text-gray-700'
                        ][$att['status']] ?? 'bg-gray-200 text-gray-700';
                        ?>
                        <span class="px-3 py-1 rounded-full text-sm <?= $statusClass ?>"><?= $att['status'] ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
