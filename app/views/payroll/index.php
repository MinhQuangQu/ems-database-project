<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-blue-800 mb-6">Salary Payment</h1>

    <!-- Payroll Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-blue-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Employee Name</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Month/Year</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Payment Date</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Total Amount</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-blue-700 uppercase">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
            <?php if (!empty($payrolls)): ?>
                <?php foreach ($payrolls as $p): ?>
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-6 py-3"><?= htmlspecialchars($p['payment_id']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($p['full_name']) ?></td>
                        <td class="px-6 py-3"><?= htmlspecialchars($p['month']) ?>/<?= htmlspecialchars($p['year']) ?></td>
                        <td class="px-6 py-3"><?= (new DateTime($p['payment_date']))->format('d/m/Y') ?></td>
                        <td class="px-6 py-3"><?= number_format($p['total_amount'], 0, ',', '.') ?></td>
                        <td class="px-6 py-3">
                            <?php if ($p['payment_status'] === 'Paid'): ?>
                                <span class="px-3 py-1 bg-green-200 text-green-700 rounded-full text-sm">Paid</span>
                            <?php elseif ($p['payment_status'] === 'Pending'): ?>
                                <span class="px-3 py-1 bg-yellow-200 text-yellow-700 rounded-full text-sm">Pending</span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-red-200 text-red-700 rounded-full text-sm">Unpaid</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-3 space-x-2">
                            <!-- Edit icon → tracking -->
                            <a href="<?= $base_url ?>/payroll/tracking/<?= $p['payment_id'] ?>"
                               class="text-blue-600 hover:text-blue-800"><i class="fa fa-edit"></i></a>

                            <!-- Delete -->
                            <form method="post" action="<?= $base_url ?>/payroll/delete/<?= $p['payment_id'] ?>"
                                  class="inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">No payroll data found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Return Button -->
    <div class="mt-6">
        <a href="<?= $base_url ?>/dashboard"
           class="text-blue-700 font-semibold hover:underline">
            Return
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
