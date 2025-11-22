const employeeTable = document.getElementById("employeeTable").querySelector("tbody");
const employeeForm = document.getElementById("employeeForm");
let editingId = null;
const API_URL = "employee_api.php";

async function loadEmployees() {
    try {
        const res = await fetch(API_URL);
        const employees = await res.json();
        employeeTable.innerHTML = "";
        employees.forEach(emp => {
            const row = document.createElement("tr");
            row.classList.add("hover:bg-gray-50");
            row.innerHTML = `
                <td class="px-6 py-4">${emp.id}</td>
                <td class="px-6 py-4">${emp.full_name}</td>
                <td class="px-6 py-4">${emp.gender}</td>
                <td class="px-6 py-4">${emp.dob}</td>
                <td class="px-6 py-4">${emp.phone}</td>
                <td class="px-6 py-4">${emp.email}</td>
                <td class="px-6 py-4 space-x-2">
                    <button class="bg-green-500 text-white px-3 py-1 rounded" onclick="editEmployee(${emp.id})">Edit</button>
                    <button class="bg-red-500 text-white px-3 py-1 rounded" onclick="deleteEmployee(${emp.id})">Delete</button>
                </td>
            `;
            employeeTable.appendChild(row);
        });
    } catch (err) { console.error(err); }
}

employeeForm.addEventListener("submit", async e => {
    e.preventDefault();
    const payload = {
        full_name: document.getElementById("fullName").value,
        gender: document.getElementById("gender").value,
        dob: document.getElementById("dob").value,
        phone: document.getElementById("phone").value,
        email: document.getElementById("email").value
    };
    try {
        if (editingId) {
            await fetch(`${API_URL}?id=${editingId}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
            editingId = null;
        } else {
            await fetch(API_URL, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
        }
        employeeForm.reset();
        loadEmployees();
    } catch (err) { console.error(err); }
});

async function deleteEmployee(id) {
    if(confirm("Are you sure?")) {
        await fetch(`${API_URL}?id=${id}`, { method: "DELETE" });
        loadEmployees();
    }
}

async function editEmployee(id) {
    const res = await fetch(`${API_URL}?id=${id}`);
    const emp = await res.json();
    document.getElementById("fullName").value = emp.full_name;
    document.getElementById("gender").value = emp.gender;
    document.getElementById("dob").value = emp.dob;
    document.getElementById("phone").value = emp.phone;
    document.getElementById("email").value = emp.email;
    editingId = id;
}

document.addEventListener("DOMContentLoaded", loadEmployees);
