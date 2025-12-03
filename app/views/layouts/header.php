<?php
$config = require __DIR__ . '/../../config/config.php';
$base_url = $config['base_url'] ?? '';
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        .menu-item:hover {
            background-color: rgba(158, 189, 252, 0.876);
            border-radius: 6px;
            padding-left: 10px;
            transition: 0.3s;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-100 to-blue-300 min-h-screen">

<header class="w-full bg-white/60 backdrop-blur-md shadow-md p-4 flex justify-between items-center">
    <button id="menuBtn" class="text-2xl text-blue-800 p-2 rounded-lg hover:bg-blue-300 transition">
        <i class="fa-solid fa-bars"></i>
    </button>

    <h1 class="text-3xl flex-1 text-center text-blue-800 font-bold">EMPLOYEE SYSTEM</h1>

    <div class="text-lg font-semibold text-blue-700">
        Hello, <span class="font-semibold text-blue-800"><?= htmlspecialchars($user['username'] ?? 'Guest') ?></span>!
    </div>
</header>

<aside id="sidebar"
       class="w-64 bg-blue/80 backdrop-blur-md shadow-xl min-h-screen p-6 fixed left-0 top-0 transform
              -translate-x-full transition-all duration-300 z-50">
    <button id="closeBtn" class="absolute top-4 right-4 text-2xl text-white">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <h2 class="text-xl text-white font-bold mb-4">Menu</h2>
    <ul class="space-y-4 font-semibold">
        <li class="menu-item text-white"><a href="<?= $base_url ?>/attendance/index">Attendance</a></li>
        <li class="menu-item text-white"><a href="<?= $base_url ?>/dashboard/index">Dashboard</a></li>
        <li class="menu-item text-white"><a href="<?= $base_url ?>/employee/index">Employees</a></li>
        <li class="menu-item text-white"><a href="<?= $base_url ?>/department/index">Departments</a></li>
        <li class="menu-item text-white"><a href="<?= $base_url ?>/project/index">Project</a></li>
        <li class="menu-item text-white"><a href="<?= $base_url ?>/payroll/index">Payroll</a></li>
        <li class="menu-item text-white"><a href="<?= $base_url ?>/reports/index">Reports</a></li>
        <li class="menu-item text-blue-800"><a href="<?= $base_url ?>/auth/login">Log out</a></li>
    </ul>
</aside>

<div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-40 z-40"></div>

<script>
const menuBtn = document.getElementById("menuBtn");
const closeBtn = document.getElementById("closeBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const mainContent = document.getElementById("mainContent");

menuBtn.addEventListener("click", () => {
    sidebar.classList.remove("-translate-x-full");
    overlay.classList.remove("hidden");
    mainContent?.classList.add("ml-64");
});

function closeSidebar() {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
    mainContent?.classList.remove("ml-64");
}

closeBtn.addEventListener("click", closeSidebar);
overlay.addEventListener("click", closeSidebar);
</script>
