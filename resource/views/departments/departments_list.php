<?php
// department_edit.php
include 'db_connection.php';
session_start(); // nếu bạn dùng session auth

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header('Location: departments.php');
    exit;
}

$message = '';
// Xử lý POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['department_name'])) {
    $name = trim($_POST['department_name']);
    if ($name === '') {
        $message = "Department name cannot be empty.";
    } else {
        $sql = "UPDATE DEPARTMENT SET department_name = ? WHERE department_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $name, $id);
        if ($stmt->execute()) {
            $message = "Department updated successfully.";
        } else {
            $message = "Update failed: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Lấy dữ liệu để show form
$sql = "SELECT department_id, department_name FROM DEPARTMENT WHERE department_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$dept = $res->fetch_assoc();
if (!$dept) {
    $stmt->close();
    $conn->close();
    header('Location: departments.php');
    exit;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Department</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="min-h-screen flex">
    <?php include 'parts/sidebar.php'; ?>
    <div class="flex-1 p-6 ml-64">
        <h1 class="text-2xl font-bold mb-4">Edit Department</h1>

        <?php if ($message): ?>
            <div class="mb-4 p-3 rounded <?= strpos($message, 'successfully') ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded shadow max-w-lg">
            <label class="block mb-2 font-medium">Department name</label>
            <input type="text" name="department_name" value="<?= htmlspecialchars($dept['department_name']) ?>" class="w-full border p-2 rounded mb-4" required>
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                <a href="departments.php" class="px-4 py-2 rounded border">Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
