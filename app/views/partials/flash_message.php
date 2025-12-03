<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Các loại thông báo
$flashTypes = ['success', 'error', 'warning', 'info'];

foreach ($flashTypes as $type) {
    if (!empty($_SESSION['flash'][$type])) {
        $messages = (array) $_SESSION['flash'][$type];
        foreach ($messages as $message) {
            $alertClass = match ($type) {
                'success' => 'alert-success',
                'error'   => 'alert-danger',
                'warning' => 'alert-warning',
                'info'    => 'alert-info',
                default   => 'alert-secondary'
            };
            echo "<div class='alert {$alertClass} alert-dismissible fade show' role='alert'>";
            echo htmlspecialchars($message);
            echo "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>";
            echo "</div>";
        }
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['flash'][$type]);
    }
}
