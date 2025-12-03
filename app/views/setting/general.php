<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container">
    <h1>Cài đặt chung</h1>

    <form method="post" action="/setting/general">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <div class="mb-3">
            <label for="company_name" class="form-label">Tên công ty</label>
            <input type="text" class="form-control" id="company_name" name="company_name" 
                   value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="system_email" class="form-label">Email hệ thống</label>
            <input type="email" class="form-control" id="system_email" name="system_email" 
                   value="<?= htmlspecialchars($settings['system_email'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="timezone" class="form-label">Múi giờ</label>
            <select name="timezone" id="timezone" class="form-select" required>
                <?php
                $timezones = timezone_identifiers_list();
                foreach ($timezones as $tz) {
                    $selected = ($settings['timezone'] ?? '') === $tz ? 'selected' : '';
                    echo "<option value=\"$tz\" $selected>$tz</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="date_format" class="form-label">Định dạng ngày</label>
            <select name="date_format" id="date_format" class="form-select">
                <?php
                $formats = ['Y-m-d', 'd-m-Y', 'm/d/Y'];
                foreach ($formats as $format) {
                    $selected = ($settings['date_format'] ?? '') === $format ? 'selected' : '';
                    echo "<option value=\"$format\" $selected>$format</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="time_format" class="form-label">Định dạng giờ</label>
            <select name="time_format" id="time_format" class="form-select">
                <?php
                $timeFormats = ['H:i', 'h:i A'];
                foreach ($timeFormats as $format) {
                    $selected = ($settings['time_format'] ?? '') === $format ? 'selected' : '';
                    echo "<option value=\"$format\" $selected>$format</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
