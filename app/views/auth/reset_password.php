<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container">
    <h1>Đặt lại mật khẩu</h1>
    <p>Nhập mật khẩu mới của bạn.</p>

    <form method="post" action="/auth/reset-password/<?= htmlspecialchars($token) ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <div class="form-group mb-3">
            <label for="password">Mật khẩu mới</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="password_confirm">Xác nhận mật khẩu</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
        <a href="/login" class="btn btn-secondary">Quay lại đăng nhập</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
