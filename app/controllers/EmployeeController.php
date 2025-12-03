<?php
class EmployeeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireLogin(); // bảo vệ tất cả các route
    }

    // ===== LIST EMPLOYEES =====
    public function index()
    {
        $employeeModel = $this->model("Employee");
        $employees = $employeeModel->all();

        $this->view("employee/index", [
            'employees'   => $employees,
            'filter'      => $_GET ?? [],
            'currentPage' => $_GET['page'] ?? 1,
            'perPage'     => 10,
            'totalPages'  => ceil(count($employees) / 10)
        ]);
    }

    // ===== CREATE EMPLOYEE FORM =====
    public function create()
    {
        $departmentModel = $this->model("Department");
        $departments = $departmentModel->getAllDepartments();

        $this->view("employee/create", [
            'departments' => $departments,
            'formAction'  => $this->config['base_url'] . "/employee/store"
        ]);
    }

    // ===== STORE NEW EMPLOYEE =====
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData(); // sanitize inputs

            $employeeModel = $this->model("Employee");
            $employeeModel->add(
                $data['full_name'] ?? '',
                $data['email'] ?? '',
                $data['phone_number'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['gender'] ?? null,
                $data['department_id'] ?? null
            );

            $this->redirect($this->config['base_url'] . "/employee");
        }
    }

    // ===== EDIT EMPLOYEE FORM =====
    public function edit($id)
    {
        $employeeModel = $this->model("Employee");
        $employee = $employeeModel->getById($id);

        $departmentModel = $this->model("Department");
        $departments = $departmentModel->getAllDepartments();

        $this->view("employee/edit", [
            'employee'    => $employee,
            'departments' => $departments,
            'formAction'  => $this->config['base_url'] . "/employee/update/$id"
        ]);
    }

    // ===== UPDATE EMPLOYEE =====
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->getPostData();

            $employeeModel = $this->model("Employee");
            $employeeModel->update(
                $id,
                $data['full_name'] ?? '',
                $data['email'] ?? '',
                $data['phone_number'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['gender'] ?? null,
                $data['department_id'] ?? null
            );

            $this->redirect($this->config['base_url'] . "/employee");
        }
    }

    // ===== DELETE EMPLOYEE =====
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeModel = $this->model("Employee");
            $employeeModel->delete($id);

            $this->redirect($this->config['base_url'] . "/employee");
        }
    }
}
