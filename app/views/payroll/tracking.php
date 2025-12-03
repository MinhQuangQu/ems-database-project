<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container mx-auto py-10">
    <!-- Flash Messages -->
    <?php if ($msg = $this->getFlash('success')): ?>
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-6 text-center font-semibold">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <?php if ($msg = $this->getFlash('error')): ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-6 text-center font-semibold">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <h1 class="text-3xl font-bold text-blue-800 mb-8 text-center">Salary Tracking & Update Payroll</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <!-- LEFT COLUMN – UPDATE FORM -->
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-semibold text-blue-700 mb-4">Update Salary</h2>

            <form action="<?= $base_url ?>/payroll/update/<?= $payroll['payment_id'] ?>" method="POST" class="grid grid-cols-1 gap-4">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <!-- Employee -->
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Employee</label>
            <!-- Hiển thị tên nhân viên, không sửa được -->
            <input type="text" value="<?= htmlspecialchars($payroll['full_name']) ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100" disabled>
            <!-- Hidden input để gửi employee_id khi submit -->
            <input type="hidden" name="employee_id" value="<?= $payroll['employee_id'] ?>">
        </div>


                <!-- Payment Date -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pay Date</label>
                    <?php $date = new DateTime($payroll['payment_date']); ?>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="number" name="pay_day" min="1" max="31" placeholder="DD" class="px-3 py-2 border rounded-lg" required value="<?= $date->format('d') ?>">
                        <input type="number" name="pay_month" min="1" max="12" placeholder="MM" class="px-3 py-2 border rounded-lg" required value="<?= $date->format('m') ?>">
                        <input type="number" name="pay_year" min="2000" max="2100" placeholder="YYYY" class="px-3 py-2 border rounded-lg" required value="<?= $date->format('Y') ?>">
                    </div>
                </div>

                <!-- Total Amount -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Total Salary (USD)</label>
                    <input type="number" name="total_amount" step="0.01" class="w-full px-4 py-2 border rounded-lg" required value="<?= htmlspecialchars($payroll['total_amount']) ?>">
                </div>

                <!-- Payment Status -->
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Payment Status</label>
                    <select name="payment_status" class="w-full px-4 py-2 border rounded-lg" required>
                        <option value="paid" <?= $payroll['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="pending" <?= $payroll['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="unpaid" <?= $payroll['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                    </select>
                </div>

                <div class="flex justify-between mt-4">
                    <a href="<?= $base_url ?>/payroll" class="text-blue-700 font-semibold hover:underline">Return</a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN – EMPLOYEE SALARY INFO -->
        <div class="bg-blue-50 p-6 rounded-xl shadow-md border border-blue-300">
            <h2 class="text-xl font-semibold text-blue-700 mb-5">Employee Salary Details</h2>
            <div class="space-y-2 text-gray-700">
                <p><strong>Full Name:</strong> <?= htmlspecialchars($payroll['full_name']) ?></p>
                <p><strong>Department:</strong> <?= htmlspecialchars($payroll['department_name']) ?></p>
                <p><strong>Month/Year:</strong> <?= sprintf('%02d/%04d', $payroll['month'], $payroll['year']) ?></p>
                <p><strong>Payment Date:</strong> <?= $date->format('d/m/Y') ?></p>
                <p><strong>Total Amount:</strong> $<?= number_format($payroll['total_amount'], 2) ?></p>
                <p><strong>Status:</strong>
                    <span class="font-bold <?= $payroll['payment_status']=='paid' ? 'text-green-600' : ($payroll['payment_status']=='pending' ? 'text-yellow-600' : 'text-red-600') ?>">
                        <?= ucfirst($payroll['payment_status']) ?>
                    </span>
                </p>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
