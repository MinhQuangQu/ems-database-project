<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="container">
    <h1>Quên mật khẩu</h1>
    <p>Nhập email của bạn để nhận link đặt lại mật khẩu.</p>

    <form method="post" action="/auth/send-reset-link">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Gửi link đặt lại mật khẩu</button>
        <a href="/login" class="btn btn-secondary">Quay lại đăng nhập</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
