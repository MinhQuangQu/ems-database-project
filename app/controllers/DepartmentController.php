<?php

declare(strict_types=1);

include_once __DIR__ . '/../core/Controller.php';
include_once __DIR__ . '/../models/DepartmentModel.php'; 
include_once __DIR__ . '/../models/EmployeeModel.php'; 

class DepartmentController extends Controller
{
    private $model;

    public function __construct($conn)
    {
        parent::__construct($conn); // Truyền connection cho parent
        $this->model = new DepartmentModel($conn);
    }

    /**
     * Hiển thị danh sách departments với phân trang và tìm kiếm
     */
    public function index(): void
    {
        try {
            // Check authentication - sử dụng requireAuth từ Controller
            $this->requireAuth();

            // Get parameters
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 15;
            $search = trim($_GET['search'] ?? '');

            // Get data with filters
            $departments = $this->model->getAll($page, $perPage, $search);
            $totalCount = $this->model->getTotalDepartment($search); // Sửa tên method
            $stats = $this->model->getStats();

            // Calculate pagination
            $totalPages = ceil($totalCount / $perPage);

            // Sử dụng renderView thay vì view
            echo $this->renderView('departments/index', [
                'departments' => $departments,
                'stats' => $stats,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalCount' => $totalCount,
                'search' => $search,
                'pageTitle' => 'Department Management',
                'csrfToken' => $this->generateCsrfToken(),
                'availableStatuses' => $this->model->getAvailableStatuses()
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load departments data');
        }
    }

    /**
     * Hiển thị form thêm department
     */
    public function create(): void
    {
        try {
            $this->requireAuth();

            // Load managers for dropdown
            $employeeModel = new EmployeeModel();
            $managers = $employeeModel->getAllManagers();

            echo $this->renderView('departments/create', [
                'pageTitle' => 'Add New Department',
                'managers' => $managers,
                'csrfToken' => $this->generateCsrfToken(),
                'availableStatuses' => $this->model->getAvailableStatuses()
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load department form');
        }
    }

    /**
     * Xử lý thêm department mới
     */
    public function store(): void
    {
        try {
            $this->requireAuth();
            $this->requirePost();

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect('/departments/create');
                return;
            }

            $data = $this->getPostData();

            // Validate required fields
            $validation = $this->validateDepartmentData($data);
            if (!$validation['success']) {
                $this->setFlashMessage('error', $validation['message']);
                $this->redirect('/departments/create');
                return;
            }

            // Add department
            $result = $this->model->add($data);

            if ($result) {
                $this->setFlashMessage('success', 'Department added successfully');
                $this->redirect('/departments');
            } else {
                $this->setFlashMessage('error', 'Failed to add department');
                $this->redirect('/departments/create');
            }

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to create department');
        }
    }

    /**
     * Hiển thị form chỉnh sửa department
     */
    public function edit(int $id): void
    {
        try {
            $this->requireAuth();

            $department = $this->model->getById($id);
            
            if (!$department) {
                $this->setFlashMessage('error', 'Department not found');
                $this->redirect('/departments');
                return;
            }

            // Load managers for dropdown
            $employeeModel = new EmployeeModel();
            $managers = $employeeModel->getAllManagers();

            echo $this->renderView('departments/edit', [
                'department' => $department,
                'managers' => $managers,
                'pageTitle' => 'Edit Department',
                'csrfToken' => $this->generateCsrfToken(),
                'availableStatuses' => $this->model->getAvailableStatuses()
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load department edit form');
        }
    }

    /**
     * Xử lý cập nhật department
     */
    public function update(int $id): void
    {
        try {
            $this->requireAuth();
            $this->requirePost();

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect("/departments/edit/{$id}");
                return;
            }

            // Check if department exists
            $existing = $this->model->getById($id);
            if (!$existing) {
                $this->setFlashMessage('error', 'Department not found');
                $this->redirect('/departments');
                return;
            }

            $data = $this->getPostData();

            // Validate required fields
            $validation = $this->validateDepartmentData($data);
            if (!$validation['success']) {
                $this->setFlashMessage('error', $validation['message']);
                $this->redirect("/departments/edit/{$id}");
                return;
            }

            // Update department
            $result = $this->model->update($id, $data);

            if ($result) {
                $this->setFlashMessage('success', 'Department updated successfully');
                $this->redirect('/departments');
            } else {
                $this->setFlashMessage('error', 'Failed to update department');
                $this->redirect("/departments/edit/{$id}");
            }

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to update department');
        }
    }

    /**
     * Xóa department (soft delete)
     */
    public function delete(int $id): void
    {
        try {
            $this->requireAuth();
            $this->requirePost();

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect('/departments');
                return;
            }

            // Check if department exists
            $existing = $this->model->getById($id);
            if (!$existing) {
                $this->setFlashMessage('error', 'Department not found');
                $this->redirect('/departments');
                return;
            }

            $result = $this->model->delete($id);

            if ($result) {
                $this->setFlashMessage('success', 'Department deleted successfully');
            } else {
                $this->setFlashMessage('error', 'Failed to delete department. Make sure no employees are assigned to this department.');
            }

            $this->redirect('/departments');

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to delete department');
        }
    }

    /**
     * Hiển thị chi tiết department với danh sách employees
     */
    public function show(int $id): void
    {
        try {
            $this->requireAuth();

            $department = $this->model->getById($id);
            
            if (!$department) {
                $this->setFlashMessage('error', 'Department not found');
                $this->redirect('/departments');
                return;
            }

            // Get employees in this department
            $employees = $this->model->getEmployees($id);

            echo $this->renderView('departments/show', [
                'department' => $department,
                'employees' => $employees,
                'pageTitle' => 'Department Details - ' . $department['department_name']
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load department details');
        }
    }

    /**
     * API: Lấy danh sách departments cho dropdown
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

            $departments = $this->model->getAllActive();

            $this->jsonResponse([
                'success' => true,
                'data' => $departments
            ]);

        } catch (Exception $e) {
            error_log("API Department list error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to fetch departments'
            ], 500);
        }
    }

    /**
     * API: Tìm kiếm departments
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

            $departments = $this->model->search($query);

            $this->jsonResponse([
                'success' => true,
                'data' => $departments
            ]);

        } catch (Exception $e) {
            error_log("API Department search error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to search departments'
            ], 500);
        }
    }

    /**
     * API: Lấy thống kê departments
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

            $this->jsonResponse([
                'success' => true,
                'data' => $stats
            ]);

        } catch (Exception $e) {
            error_log("API Department stats error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to fetch department statistics'
            ], 500);
        }
    }

    /**
     * Bulk action for departments
     */
    public function bulkAction(): void
    {
        try {
            $this->requireAuth();
            $this->requirePost();

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
                    'message' => 'No departments selected'
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
                            $errors[] = "Failed to delete department ID: {$id}";
                        }
                    }
                    break;

                case 'activate':
                    foreach ($ids as $id) {
                        if ($this->updateStatus((int)$id, 'active')) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to activate department ID: {$id}";
                        }
                    }
                    break;

                case 'deactivate':
                    foreach ($ids as $id) {
                        if ($this->updateStatus((int)$id, 'inactive')) {
                            $successCount++;
                        } else {
                            $errorCount++;
                            $errors[] = "Failed to deactivate department ID: {$id}";
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
                'message' => "Successfully processed {$successCount} departments, {$errorCount} failed",
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
     * Export departments to CSV
     */
    public function export(): void
    {
        try {
            $this->requireAuth();

            // Get all departments
            $departments = $this->model->getAll(1, 1000); // Large limit to get all

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=departments_' . date('Y-m-d') . '.csv');

            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($output, "\xEF\xBB\xBF");
            
            // CSV header
            fputcsv($output, [
                'Department Code',
                'Department Name',
                'Description',
                'Manager',
                'Employee Count',
                'Status',
                'Created Date'
            ]);

            // CSV data
            foreach ($departments as $department) {
                fputcsv($output, [
                    $department['department_code'] ?? '',
                    $department['department_name'] ?? '',
                    $department['description'] ?? '',
                    $department['manager_name'] ?? 'N/A',
                    $department['employee_count'] ?? 0,
                    ucfirst($department['status'] ?? 'active'),
                    $department['created_at'] ?? ''
                ]);
            }

            fclose($output);
            exit();

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to export departments data');
        }
    }

    /**
     * Validate department data
     */
    private function validateDepartmentData(array $data): array
    {
        $required = ['department_name', 'department_code'];
        
        foreach ($required as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                return [
                    'success' => false,
                    'message' => "Field '{$field}' is required"
                ];
            }
        }

        // Validate department name length
        if (strlen(trim($data['department_name'])) < 2) {
            return [
                'success' => false,
                'message' => 'Department name must be at least 2 characters long'
            ];
        }

        // Validate department code format
        if (!preg_match('/^[A-Z0-9_]{2,20}$/', $data['department_code'])) {
            return [
                'success' => false,
                'message' => 'Department code must be 2-20 characters, uppercase letters, numbers and underscores only'
            ];
        }

        // Validate status
        $validStatuses = ['active', 'inactive'];
        if (!empty($data['status']) && !in_array($data['status'], $validStatuses)) {
            return [
                'success' => false,
                'message' => 'Invalid department status'
            ];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }

    /**
     * Update department status
     */
    private function updateStatus(int $id, string $status): bool
    {
        try {
            $data = ['status' => $status];
            return $this->model->update($id, $data);
        } catch (Exception $e) {
            error_log("Update department status error: " . $e->getMessage());
            return false;
        }
    }
}