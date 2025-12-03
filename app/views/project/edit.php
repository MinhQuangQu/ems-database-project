<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<div class="bg-gradient-to-r from-blue-100 to-blue-300 min-h-screen flex items-center justify-center py-10">
    <div class="bg-white shadow-2xl rounded-2xl p-10 w-full max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center text-blue-800 mb-8">Edit Project</h1>

        <form action="<?= $base_url ?>/project/edit/<?= $project['project_id'] ?>" method="POST" class="grid grid-cols-1 gap-6">
            <!-- Project Name -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Project Name</label>
                <input name="project_name" type="text"
                       value="<?= htmlspecialchars($project['project_name'] ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required>
            </div>

            <!-- Start Date -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Start Date</label>
                <input name="start_date" type="date"
                       value="<?= htmlspecialchars($project['start_date'] ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required>
            </div>

            <!-- End Date -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">End Date</label>
                <input name="end_date" type="date"
                       value="<?= htmlspecialchars($project['end_date'] ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
            </div>

            <!-- Budget -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Budget ($)</label>
                <input name="budget" type="number" step="0.01"
                       value="<?= htmlspecialchars($project['budget'] ?? '') ?>"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required>
            </div>

            <!-- Department -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Department</label>
                <select name="department_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['department_id'] ?>" 
                            <?= ($project['department_id'] ?? '') == $dept['department_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-4">
                <a href="<?= $base_url ?>/project" class="text-blue-700 font-semibold hover:underline">Return</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Update</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
