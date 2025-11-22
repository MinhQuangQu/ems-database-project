<?php
include "parts/header.php";
include "parts/sidebar.php";
?>

<div class="flex-1 ml-64 flex flex-col min-h-screen p-6">
    <h1 class="text-2xl font-bold mb-6">Employee List</h1>

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="employeeTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Full Name</th>
                    <th class="px-6 py-3">Gender</th>
                    <th class="px-6 py-3">DOB</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php include "parts/footer.php"; ?>

