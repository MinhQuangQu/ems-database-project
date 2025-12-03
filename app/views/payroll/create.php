<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container">
    <h1>Thêm bảng lương mới</h1>

    <form method="post" action="/payroll/store">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <!-- Chọn nhân viên -->
        <div class="mb-3">
            <label for="employee_id" class="form-label">Nhân viên</label>
            <select class="form-select" id="employee_id" name="employee_id" required>
                <option value="">-- Chọn nhân viên --</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['full_name']) ?> (<?= htmlspecialchars($emp['employee_code']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Chọn tháng -->
        <div class="mb-3">
            <label for="month" class="form-label">Tháng</label>
            <input type="month" id="month" name="month" class="form-control" required value="<?= date('Y-m') ?>">
        </div>

        <!-- Lương cơ bản -->
        <div class="mb-3">
            <label for="base_salary" class="form-label">Lương cơ bản</label>
            <input type="number" id="base_salary" name="base_salary" class="form-control" min="0" step="0.01" required>
        </div>

        <!-- Thưởng -->
        <div class="mb-3">
            <label for="bonus" class="form-label">Thưởng</label>
            <input type="number" id="bonus" name="bonus" class="form-control" min="0" step="0.01">
        </div>

        <!-- Khấu trừ -->
        <div class="mb-3">
            <label for="deductions" class="form-label">Khấu trừ</label>
            <input type="number" id="deductions" name="deductions" class="form-control" min="0" step="0.01">
        </div>

        <!-- Trạng thái -->
        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select class="form-select" id="status" name="status" required>
                <option value="paid">Đã thanh toán</option>
                <option value="unpaid">Chưa thanh toán</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Lưu bảng lương</button>
        <a href="/payroll" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
