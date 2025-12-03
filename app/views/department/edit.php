<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<?php
// Lấy base_url từ config
$config = require __DIR__ . '/../../config/config.php';
$base_url = $config['base_url'] ?? '';
?>

<div class="min-h-screen bg-gradient-to-r from-blue-100 to-blue-300 flex items-center justify-center py-10">

    <div class="bg-white shadow-2xl rounded-2xl p-10 w-full max-w-3xl mx-auto">

        <h1 class="text-3xl font-bold text-center text-blue-800 mb-8">
            Update Department Information
        </h1>

        <form action="<?= $base_url ?>/department/update/<?= htmlspecialchars($department['department_id']) ?>"
              method="POST"
              class="grid grid-cols-1 gap-6">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <!-- Department -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Department</label>
                <input name="department_name"
                       type="text"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['department_name'] ?? $department['department_name']) ?>"
                       required>
            </div>

            <!-- Location -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Location</label>
                <input name="location"
                       type="text"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none"
                       value="<?= htmlspecialchars($_POST['location'] ?? $department['location']) ?>"
                       required>
            </div>

            <!-- Manager -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Manager</label>
                <input type="text"
                    value="<?= htmlspecialchars($department['manager_name'] ?? 'Chưa có') ?>"
                    class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600"
                    disabled>
            </div>


            <!-- Buttons -->
            <div class="flex justify-between items-center mt-4">
                <a href="<?= $base_url ?>/department"
                   class="text-blue-700 font-semibold hover:underline">
                    Return
                </a>

                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
