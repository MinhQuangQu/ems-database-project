<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../partials/flash_message.php';
?>

<div class="bg-white shadow-2xl rounded-2xl p-10 w-full max-w-4xl mx-auto my-10">
    <h1 class="text-3xl font-bold text-center text-blue-800 mb-8">Attendance Tracking</h1>

    <form method="POST" class="grid grid-cols-1 gap-6">
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Employee</label>
            <select name="employee_id" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                <option value="">-- Select Employee --</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Work Date</label>
            <input type="date" name="work_date" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Check-in Time</label>
            <input type="time" name="checkin_time" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Check-out Time</label>
            <input type="time" name="checkout_time" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
        </div>

        <div>
            <label class="block font-semibold text-gray-700 mb-1">Status</label>
            <select name="status" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
                <option value="">Select Status</option>
                <option value="Present">Present</option>
                <option value="Late">Late</option>
                <option value="Absent">Absent</option>
                <option value="Leave Approved">Leave Approved</option>
            </select>
        </div>

        <div class="flex justify-between items-center mt-6">
            <a href="<?= $base_url ?>/attendance/index" class="text-blue-700 font-semibold hover:underline">Return</a>
            <button type="submit" name="save_attendance" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Save</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
