<?php
require_once "../app/core/Controller.php";

class ProjectController extends Controller
{
    private $projectModel;

    public function __construct() {
        parent::__construct();
        //$this->requireLogin();
        $this->projectModel = $this->model("Project");
    }

    // =======================
    // Index + pagination + search
    // =======================
    public function index() {
        $search = $_GET['search'] ?? '';
        $perPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $perPage;

        $projects = $this->projectModel->getAll($search, $perPage, $offset);
        $totalProjects = $this->projectModel->countAll($search);
        $totalPages = ceil($totalProjects / $perPage);

        $this->view("project/index", [
            'projects' => $projects,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'filter' => ['search' => $search],
            'base_url' => $this->config['base_url']
        ]);
    }

    // =======================
    // Thêm dự án
    // =======================
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_name = $_POST['project_name'] ?? '';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? null;
            $budget = $_POST['budget'] ?? 0;
            $status = $_POST['status'] ?? 'active';
            $department_id = $_POST['department_id'] ?? null;

            $this->projectModel->add($project_name, $start_date, $end_date, $budget, $status, $department_id);
            $this->flash('success', 'Project added successfully.');
            $this->redirect($this->config['base_url'] . '/project');
        }

        $departments = $this->projectModel->getDepartments();

        $this->view("project/create", [
            'departments' => $departments,
            'base_url' => $this->config['base_url']
        ]);
    }

    // =======================
    // Sửa dự án
    // =======================
    public function edit($id) {
        $project = $this->projectModel->getById($id);
        if (!$project) {
            $this->flash('error', 'Project not found.');
            $this->redirect($this->config['base_url'] . '/project');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_name = $_POST['project_name'] ?? '';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? null;
            $budget = $_POST['budget'] ?? 0;
            $status = $_POST['status'] ?? 'active';
            $department_id = $_POST['department_id'] ?? null;

            $this->projectModel->update($id, $project_name, $start_date, $end_date, $budget, $status, $department_id);
            $this->flash('success', 'Project updated successfully.');
            $this->redirect($this->config['base_url'] . '/project');
        }

        $departments = $this->projectModel->getDepartments();

        $this->view("project/edit", [
            'departments' => $departments,
            'project' => $project,
            'base_url' => $this->config['base_url']
        ]);
    }

    // =======================
    // Xóa dự án
    // =======================
    public function delete($id) {
        $this->projectModel->delete($id);
        $this->flash('success', 'Project deleted successfully.');
        $this->redirect($this->config['base_url'] . '/project');
    }
}
