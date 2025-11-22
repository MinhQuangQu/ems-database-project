<?php
session_start();
include '../app/core/db_connection.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $admin = $res->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center justify-center min-h-screen">
<div class="bg-white p-10 rounded-lg shadow-md w-full max-w-md">
    <h1 class="text-2xl font-bold mb-4">Admin Login</h1>
    <?php if ($error): ?>
        <div class="mb-4 p-2 bg-red-500 text-white rounded"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required class="w-full p-2 mb-3 border rounded">
        <input type="password" name="password" placeholder="Password" required class="w-full p-2 mb-3 border rounded">
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded">Login</button>
    </form>
</div>
</body>
</html>
