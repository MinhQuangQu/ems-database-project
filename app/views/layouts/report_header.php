<?php
// report_header.php
// File này chỉ include các CSS/JS chung, navbar, không check login

$base_url = $base_url ?? '/CSDL/public'; // đảm bảo có biến base_url
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report | EMS NEU</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gradient-to-r from-blue-100 to-blue-300 min-h-screen flex flex-col items-center justify-start">

<!-- Navbar cơ bản -->
<header class="w-full bg-blue-700 text-white shadow-md py-4 mb-6">
    <div class="container mx-auto flex justify-between items-center px-4">
        <h1 class="text-xl font-bold">EMS Report System</h1>
        <nav>
            <a href="<?= $base_url ?>/dashboard" class="px-3 py-1 hover:bg-blue-600 rounded">Dashboard</a>
            <a href="<?= $base_url ?>/employee/index" class="px-3 py-1 hover:bg-blue-600 rounded">Employees</a>
            <a href="<?= $base_url ?>/report/index" class="px-3 py-1 hover:bg-blue-600 rounded">Reports</a>
            <a href="<?= $base_url ?>/auth/logout" class="px-3 py-1 hover:bg-blue-600 rounded">Logout</a>
        </nav>
    </div>
</header>

<main class="flex-1 w-full container mx-auto px-4">
