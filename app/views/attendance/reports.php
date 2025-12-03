<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container">
    <h1>Báo cáo điểm danh</h1>

    <!-- Filter form -->
    <form method="get" class="mb-3">
        <label for="month">Tháng</label>
        <input type="month" id="month" name="month" value="<?= htmlspecialchars($month ?? date('Y-m')) ?>">

        <label for="department_id">Phòng ban</label>
        <select id="department_id" name="department_id">
            <option value="0">-- Tất cả --</option>
            <?php if (!empty($departments)): ?>
                <?php foreach ($departments as $dep): ?>
                    <option value="<?= $dep['id'] ?>" <?= ($departmentId ?? 0) == $dep['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dep['name']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

        <button type="submit" class="btn btn-primary">Lọc</button>
        <a href="/attendance/export?month=<?= htmlspecialchars($month ?? date('Y-m')) ?>" class="btn btn-success">Xuất CSV</a>
    </form>

    <!-- Summary statistics -->
    <div class="mb-3">
        <strong>Tổng nhân viên:</strong> <?= $monthlyStats['total_employees'] ?? 0 ?> |
        <strong>Có mặt:</strong> <?= $monthlyStats['present'] ?? 0 ?> |
        <strong>Vắng:</strong> <?= $monthlyStats['absent'] ?? 0 ?> |
        <strong>Đi muộn:</strong> <?= $monthlyStats['late'] ?? 0 ?> |
        <strong>Nghỉ phép:</strong> <?= $monthlyStats['leave'] ?? 0 ?>
    </div>

    <!-- Attendance report table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Mã NV</th>
                <th>Họ tên</th>
                <th>Ngày làm việc</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Trạng thái</th>
                <th>Giờ làm việc</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($monthlyStats['attendance'])): ?>
                <?php foreach ($monthlyStats['attendance'] as $index => $att): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($att['employee_code']) ?></td>
                        <td><?= htmlspecialchars($att['full_name']) ?></td>
                        <td><?= htmlspecialchars($att['work_date']) ?></td>
                        <td><?= htmlspecialchars($att['check_in'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($att['check_out'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($att['status']) ?></td>
                        <td><?= htmlspecialchars($att['working_hours'] ?? 'N/A') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">Không có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
