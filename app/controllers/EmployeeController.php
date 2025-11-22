<?php

declare(strict_types=1);

include_once __DIR__ . '/../core/Controller.php';
include_once __DIR__ . '/../models/EmployeeModel.php'; 
include_once __DIR__ . '/../models/DepartmentModel.php';

class EmployeeController extends Controller
{
    private EmployeeModel $model;

    public function __construct($conn)
    {
        parent::__construct();
        $this->model = new EmployeeModel();
    }

    /**
     * Hiển thị danh sách employees với phân trang và filter
     */
    public function index(): void
    {
        try {
            // Check authentication
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to access employees page');
                $this->redirect('/login');
                return;
            }

            // Get parameters
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 15;
            $search = trim($_GET['search'] ?? '');
            $departmentId = (int)($_GET['department_id'] ?? 0);
            $status = trim($_GET['status'] ?? '');

            // Get data with filters
            $employees = $this->model->getAll($page, $perPage, $search, $departmentId, $status);
            $totalCount = $this->model->getTotalCount($search, $departmentId, $status);
            $stats = $this->model->getStats();

            // Load departments for filter dropdown
            $departmentModel = new DepartmentModel($this->conn);
            $departments = $departmentModel->getAllActive();

            // Calculate pagination
            $totalPages = ceil($totalCount / $perPage);

            $this->Renderview('employee/index', [
                'employees' => $employees,
                'departments' => $departments,
                'stats' => $stats,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalCount' => $totalCount,
                'search' => $search,
                'departmentId' => $departmentId,
                'status' => $status,
                'pageTitle' => 'Employee Management',
                'csrfToken' => $this->generateCsrfToken(),
                'availableStatuses' => $this->model->getAvailableStatuses()
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load employees data');
        }
    }

    /**
     * Hiển thị form thêm employee
     */
    public function create(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to add employee');
                $this->redirect('/login');
                return;
            }

            // Load departments and managers for dropdowns
            $departmentModel = new DepartmentModel($this->conn);
            $departments = $departmentModel->getAllActive();
            $managers = $this->model->getAllManagers();

            $this->Renderview('employee/create', [
                'pageTitle' => 'Add New Employee',
                'departments' => $departments,
                'managers' => $managers,
                'csrfToken' => $this->generateCsrfToken(),
                'availableStatuses' => $this->model->getAvailableStatuses(),
                'today' => date('Y-m-d')
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load employee form');
        }
    }

    /**
     * Xử lý thêm employee mới
     */
    public function store(): void
    {
        try {
            if (!$this->isLoggedIn() || !$this->isPostRequest()) {
                $this->setFlashMessage('error', 'Invalid request method');
                $this->redirect('/employees');
                return;
            }

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect('/employees/create');
                return;
            }

            $data = $this->getPostData();

            // Validate required fields
            $validation = $this->validateEmployeeData($data);
            if (!$validation['success']) {
                $this->setFlashMessage('error', $validation['message']);
                $this->redirect('/employees/create');
                return;
            }

            // Add employee
            $result = $this->model->add($data);

            if ($result) {
                $this->setFlashMessage('success', 'Employee added successfully');
                $this->redirect('/employees');
            } else {
                $this->setFlashMessage('error', 'Failed to add employee. Employee code or email may already exist.');
                $this->redirect('/employees/create');
            }

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to create employee');
        }
    }

    /**
     * Hiển thị form chỉnh sửa employee
     */
    public function edit(int $id): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to edit employee');
                $this->redirect('/login');
                return;
            }

            $employee = $this->model->getById($id);
            
            if (!$employee) {
                $this->setFlashMessage('error', 'Employee not found');
                $this->redirect('/employees');
                return;
            }

            // Load departments and managers for dropdowns
            $departmentModel = new DepartmentModel($this->conn);
            $departments = $departmentModel->getAllActive();
            $managers = $this->model->getAllManagers();

            $this->Renderview('employee/edit', [
                'employee' => $employee,
                'departments' => $departments,
                'managers' => $managers,
                'pageTitle' => 'Edit Employee - ' . $employee['full_name'],
                'csrfToken' => $this->generateCsrfToken(),
                'availableStatuses' => $this->model->getAvailableStatuses()
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load employee edit form');
        }
    }

    /**
     * Xử lý cập nhật employee
     */
    public function update(int $id): void
    {
        try {
            if (!$this->isLoggedIn() || !$this->isPostRequest()) {
                $this->setFlashMessage('error', 'Invalid request method');
                $this->redirect('/employees');
                return;
            }

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect("/employees/edit/{$id}");
                return;
            }

            // Check if employee exists
            $existing = $this->model->getById($id);
            if (!$existing) {
                $this->setFlashMessage('error', 'Employee not found');
                $this->redirect('/employees');
                return;
            }

            $data = $this->getPostData();

            // Validate required fields
            $validation = $this->validateEmployeeData($data);
            if (!$validation['success']) {
                $this->setFlashMessage('error', $validation['message']);
                $this->redirect("/employees/edit/{$id}");
                return;
            }

            // Update employee
            $result = $this->model->update($id, $data);

            if ($result) {
                $this->setFlashMessage('success', 'Employee updated successfully');
                $this->redirect('/employees');
            } else {
                $this->setFlashMessage('error', 'Failed to update employee. Employee code or email may already exist.');
                $this->redirect("/employees/edit/{$id}");
            }

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to update employee');
        }
    }

    /**
     * Xóa employee (soft delete)
     */
    public function delete(int $id): void
    {
        try {
            if (!$this->isLoggedIn() || !$this->isPostRequest()) {
                $this->setFlashMessage('error', 'Invalid request method');
                $this->redirect('/employees');
                return;
            }

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect('/employees');
                return;
            }

            // Check if employee exists
            $existing = $this->model->getById($id);
            if (!$existing) {
                $this->setFlashMessage('error', 'Employee not found');
                $this->redirect('/employees');
                return;
            }

            $result = $this->model->delete($id);

            if ($result) {
                $this->setFlashMessage('success', 'Employee deleted successfully');
            } else {
                $this->setFlashMessage('error', 'Failed to delete employee. Make sure no attendance records exist for this employee.');
            }

            $this->redirect('/employees');

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to delete employee');
        }
    }

    /**
     * Hiển thị chi tiết employee
     */
    public function show(int $id): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view employee details');
                $this->redirect('/login');
                return;
            }

            $employee = $this->model->getById($id);
            
            if (!$employee) {
                $this->setFlashMessage('error', 'Employee not found');
                $this->redirect('/employees');
                return;
            }

            // Load attendance data for this employee
            $attendanceModel = new AttendanceModel($this->conn);

            $this->Renderview('employee/show', [
                'employee' => $employee,
                'pageTitle' => 'Employee Details - ' . $employee['full_name']
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load employee details');
        }
    }

    /**
     * API: Lấy danh sách employees cho dropdown
     */
    public function apiList(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
                return;
            }

            $departmentId = (int)($_GET['department_id'] ?? 0);
            $employees = $departmentId > 0 
                ? $this->model->getByDepartment($departmentId)
                : $this->model->getAllActive();

            $this->jsonResponse([
                'success' => true,
                'data' => $employees
            ]);

        } catch (Exception $e) {
            error_log("API Employee list error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to fetch employees'
            ], 500);
        }
    }

    /**
     * API: Tìm kiếm employees
     */
    public function apiSearch(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
                return;
            }

            $query = trim($_GET['q'] ?? '');
            
            if (empty($query)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Search query is required'
                ], 400);
                return;
            }

            $employees = $this->model->search($query);

            $this->jsonResponse([
                'success' => true,
                'data' => $employees
            ]);

        } catch (Exception $e) {
            error_log("API Employee search error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to search employees'
            ], 500);
        }
    }

    /**
     * API: Lấy thống kê employees
     */
    public function apiStats(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
                return;
            }

            $stats = $this->model->getStats();
            $departmentStats = $this->model->getStatsByDepartment();

            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'overview' => $stats,
                    'by_department' => $departmentStats
                ]
            ]);

        } catch (Exception $e) {
            error_log("API Employee stats error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to fetch employee statistics'
            ], 500);
        }
    }

    /**
     * Bulk action for employees
     */
    public function bulkAction(): void
    {
        try {
            if (!$this->isLoggedIn() || !$this->isPostRequest()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid request'
                ], 400);
                return;
            }

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Invalid security token'
                ], 400);
                return;
            }

            $data = $this->getPostData();
            $action = $data['action'] ?? '';
            $ids = $data['ids'] ?? [];

            if (empty($ids) || !is_array($ids)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'No employees selected'
                ], 400);
                return;
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            switch ($action) {
                case 'delete':
                    foreach ($ids as $id) {
                        if ($this->model->delete((int)$id)) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to delete employee ID: {$id}";
                        }
                    }
                    break;

                case 'activate':
                    foreach ($ids as $id) {
                        if ($this->updateStatus((int)$id, 'active')) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to activate employee ID: {$id}";
                        }
                    }
                    break;

                case 'deactivate':
                    foreach ($ids as $id) {
                        if ($this->updateStatus((int)$id, 'inactive')) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to deactivate employee ID: {$id}";
                        }
                    }
                    break;

                case 'transfer_department':
                    $newDepartmentId = (int)($data['department_id'] ?? 0);
                    if ($newDepartmentId <= 0) {
                        $this->jsonResponse([
                            'success' => false,
                            'message' => 'Invalid department ID'
                        ], 400);
                        return;
                    }

                    foreach ($ids as $id) {
                        if ($this->transferDepartment((int)$id, $newDepartmentId)) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to transfer employee ID: {$id}";
                        }
                    }
                    break;

                default:
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Invalid action'
                    ], 400);
                    return;
            }

            $this->jsonResponse([
                'success' => true,
                'message' => "Successfully processed {$successCount} employees, {$errorCount} failed",
                'processed' => $successCount,
                'failed' => $errorCount,
                'errors' => $errors
            ]);

        } catch (Exception $e) {
            error_log("Bulk action error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to process bulk action'
            ], 500);
        }
    }

    /**
     * Export employees to CSV
     */
    public function export(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to export data');
                $this->redirect('/login');
                return;
            }

            // Get filter parameters
            $search = trim($_GET['search'] ?? '');
            $departmentId = (int)($_GET['department_id'] ?? 0);
            $status = trim($_GET['status'] ?? '');

            // Get all employees with filters
            $employees = $this->model->getAll(1, 10000, $search, $departmentId, $status);

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=employees_' . date('Y-m-d') . '.csv');

            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($output, "\xEF\xBB\xBF");
            
            // CSV header
            fputcsv($output, [
                'Employee Code',
                'Full Name',
                'Email',
                'Phone',
                'Position',
                'Department',
                'Manager',
                'Salary',
                'Date of Birth',
                'Date of Joining',
                'Status',
                'Attendance Count'
            ]);

            // CSV data
            foreach ($employees as $employee) {
                fputcsv($output, [
                    $employee['employee_code'],
                    $employee['full_name'],
                    $employee['email'],
                    $employee['phone'] ?? 'N/A',
                    $employee['position'],
                    $employee['department_name'] ?? 'N/A',
                    $employee['manager_name'] ?? 'N/A',
                    $employee['salary'] ? number_format($employee['salary'], 2) : 'N/A',
                    $employee['date_of_birth'] ?? 'N/A',
                    $employee['date_of_joining'] ?? 'N/A',
                    ucfirst($employee['status']),
                    $employee['attendance_count']
                ]);
            }

            fclose($output);
            exit();

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to export employees data');
        }
    }

    /**
     * Validate employee data
     */
    private function validateEmployeeData(array $data): array
    {
        $required = ['employee_code', 'full_name', 'email', 'position'];
        
        foreach ($required as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                return [
                    'success' => false,
                    'message' => "Field '{$field}' is required"
                ];
            }
        }

        // Validate employee code format
        if (!preg_match('/^[A-Z0-9]{3,20}$/', $data['employee_code'])) {
            return [
                'success' => false,
                'message' => 'Employee code must be 3-20 characters, uppercase letters and numbers only'
            ];
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email format'
            ];
        }

        // Validate full name length
        if (strlen(trim($data['full_name'])) < 2) {
            return [
                'success' => false,
                'message' => 'Full name must be at least 2 characters long'
            ];
        }

        // Validate phone format if provided
        if (!empty($data['phone']) && !preg_match('/^[\+]?[0-9\s\-\(\)]{10,15}$/', $data['phone'])) {
            return [
                'success' => false,
                'message' => 'Invalid phone number format'
            ];
        }

        // Validate salary if provided
        if (!empty($data['salary']) && (!is_numeric($data['salary']) || $data['salary'] < 0)) {
            return [
                'success' => false,
                'message' => 'Salary must be a positive number'
            ];
        }

        // Validate date of birth if provided
        if (!empty($data['date_of_birth']) && !strtotime($data['date_of_birth'])) {
            return [
                'success' => false,
                'message' => 'Invalid date of birth format'
            ];
        }

        // Validate date of joining if provided
        if (!empty($data['date_of_joining']) && !strtotime($data['date_of_joining'])) {
            return [
                'success' => false,
                'message' => 'Invalid date of joining format'
            ];
        }

        // Validate status
        $validStatuses = ['active', 'inactive', 'on_leave', 'terminated'];
        if (!empty($data['status']) && !in_array($data['status'], $validStatuses)) {
            return [
                'success' => false,
                'message' => 'Invalid employee status'
            ];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }

    /**
     * Update employee status
     */
    private function updateStatus(int $id, string $status): bool
    {
        try {
            $data = ['status' => $status];
            return $this->model->update($id, $data);
        } catch (Exception $e) {
            error_log("Update employee status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Transfer employee to different department
     */
    private function transferDepartment(int $id, int $departmentId): bool
    {
        try {
            $data = ['department_id' => $departmentId];
            return $this->model->update($id, $data);
        } catch (Exception $e) {
            error_log("Transfer employee department error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate employee report
     */
    public function report(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view reports');
                $this->redirect('/login');
                return;
            }

            $type = $_GET['type'] ?? 'overview';
            $departmentId = (int)($_GET['department_id'] ?? 0);

            $reportData = [];

            switch ($type) {
                case 'department':
                    $reportData = $this->model->getStatsByDepartment();
                    break;
                
                case 'salary':
                    $employees = $this->model->getAll(1, 1000, '', $departmentId, 'active');
                    $reportData = $employees;
                    break;
                
                case 'overview':
                default:
                    $reportData = $this->model->getStats();
                    break;
            }

            $this->Renderview('employee/report', [
                'reportData' => $reportData,
                'reportType' => $type,
                'departmentId' => $departmentId,
                'pageTitle' => 'Employee Reports'
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to generate employee report');
        }
    }
}