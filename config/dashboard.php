<?php
session_start();

// Nếu chưa login, redirect về login
if (!isset($_SESSION['user'])) {
    header('Location: /CSDL/resource/views/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-4">Chào mừng, <?= htmlspecialchars($_SESSION['user']) ?>!</h1>
        <p>Đây là dashboard đơn giản.</p>

        <a href="/CSDL/public/logout.php"
           class="inline-block mt-4 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
            Đăng xuất
        </a>
    </div>
</body>
</html>
