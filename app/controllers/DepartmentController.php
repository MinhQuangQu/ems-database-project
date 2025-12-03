<?php
require_once "../app/core/Controller.php";

class DepartmentController extends Controller
{
    private $departmentModel;

    public function __construct()
    {
        parent::__construct(); 
        $this->requireLogin(); // dùng chung logic login

        $this->departmentModel = $this->model("Department");
    }

    public function index()
    {
        $search = $_GET['search'] ?? '';

        if ($search !== '') {
            // trả về danh sách nhân viên thuộc department tìm được
            $employees = $this->departmentModel->searchDepartmentEmployees($search);
            $data = ['employees' => $employees, 'filter' => ['search' => $search]];
            $this->view("department/index", $data, 'employees'); // truyền mode 'employees'
        } else {
            // danh sách department bình thường
            $departments = $this->departmentModel->getAllDepartments();
            $data = ['departments' => $departments, 'filter' => ['search' => '']];
            $this->view("department/index", $data, 'departments'); // truyền mode 'departments'
        }
    }




    public function create()
    {
        $this->view("department/create");
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->sanitize($_POST['name']);
            $this->departmentModel->insertDepartment($name);

            $this->redirect($this->config['base_url'] . '/department');
        }
    }

    public function edit($id)
    {
        $this->requireLogin();

        $department = $this->departmentModel->getDepartmentById((int)$id);

        if (!$department) {
            $this->flash('error', 'Phòng ban không tồn tại.');
            $this->redirect('/department');
        }

        $this->view('department/edit', ['department' => $department]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->sanitize($_POST['name']);
            $this->departmentModel->updateDepartment((int)$id, $name);

            $this->redirect($this->config['base_url'] . '/department');
        }
    }

    public function delete($id)
    {
        $this->departmentModel->deleteDepartment((int)$id);
        $this->redirect($this->config['base_url'] . '/department');
    }
}
