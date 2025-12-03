<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container">
    <h1>Thêm điểm danh mới</h1>

    <form method="post" action="/attendance/store">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <!-- Nhân viên -->
        <div class="form-group mb-3">
            <label for="employee_id">Nhân viên</label>
            <select id="employee_id" name="employee_id" class="form-control" required>
                <option value="">-- Chọn nhân viên --</option>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= (($_POST['employee_id'] ?? '') == $emp['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['full_name']) ?> (<?= htmlspecialchars($emp['employee_code']) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Ngày làm việc -->
        <div class="form-group mb-3">
            <label for="work_date">Ngày làm việc</label>
            <input type="date" id="work_date" name="work_date" class="form-control"
                value="<?= htmlspecialchars($_POST['work_date'] ?? date('Y-m-d')) ?>" required>
        </div>

        <!-- Check-in -->
        <div class="form-group mb-3">
            <label for="check_in">Check-in (HH:MM)</label>
            <input type="time" id="check_in" name="check_in" class="form-control"
                value="<?= htmlspecialchars($_POST['check_in'] ?? '') ?>">
        </div>

        <!-- Check-out -->
        <div class="form-group mb-3">
            <label for="check_out">Check-out (HH:MM)</label>
            <input type="time" id="check_out" name="check_out" class="form-control"
                value="<?= htmlspecialchars($_POST['check_out'] ?? '') ?>">
        </div>

        <!-- Trạng thái -->
        <div class="form-group mb-3">
            <label for="status">Trạng thái</label>
            <select id="status" name="status" class="form-control" required>
                <?php
                $statuses = ['present' => 'Có mặt', 'absent' => 'Vắng mặt', 'late' => 'Đi muộn',
                             'half_day' => 'Nửa ngày', 'holiday' => 'Nghỉ lễ', 'leave' => 'Nghỉ phép'];
                foreach ($statuses as $key => $label):
                ?>
                    <option value="<?= $key ?>" <?= (($_POST['status'] ?? '') === $key) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Thêm mới</button>
        <a href="/attendance" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
