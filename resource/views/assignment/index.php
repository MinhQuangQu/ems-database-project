<?php
session_start();
include '../../app/core/db_connection.php';
if(!isset($_SESSION['admin_id'])){
    header("Location: ../../public/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assignment Module</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<?php include '../../parts/header.php'; ?>
<?php include '../../parts/sidebar.php'; ?>

<div class="ml-64 p-6">
    <h1 class="text-3xl font-bold mb-6">Assignment Module</h1>
    <p>Content for Assignment management goes here.</p>
</div>

<?php include '../../parts/footer.php'; ?>
</body>
</html>
