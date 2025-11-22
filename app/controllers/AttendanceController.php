<?php

declare(strict_types=1);

include_once __DIR__ . '/../core/Controller.php';
include_once __DIR__ . '/../models/Attendance.php';

class AttendanceController extends Controller
{
    private AttendanceModel $model;

    public function __construct($conn)
    {
        parent::__construct();
        $this->model = new AttendanceModel($conn);
    }

    /**
     * Hiển thị danh sách attendance với phân trang và tìm kiếm
     */
    public function index(): void
    {
        try {
            // Check authentication
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to access attendance page');
                $this->redirect('/login');
                return;
            }

            // Get parameters
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 15;
            $search = trim($_GET['search'] ?? '');
            $date = trim($_GET['date'] ?? '');
            $status = trim($_GET['status'] ?? '');

            // Get data with filters
            $attendanceList = $this->model->getAll($page, $perPage, $search, $date, $status);
            $totalCount = $this->model->getTotalCount($search, $date, $status);
            $stats = $this->model->getStatsByStatus();

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load attendance data');
        }
    }

    /**
     * Hiển thị form thêm attendance
     */
    public function create(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to add attendance');
                $this->redirect('/login');
                return;
            }
        }
    }

    /**
     * Xử lý thêm attendance mới
     */
    public function store(): void
    {
        try {
            if (!$this->isLoggedIn() || !$this->isPostRequest()) {
                $this->setFlashMessage('error', 'Invalid request method');
                $this->redirect('/attendance');
                return;
            }

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect('/attendance/create');
                return;
            }

            $data = $this->getPostData();

            // Validate required fields
            $validation = $this->validateAttendanceData($data);
            if (!$validation['success']) {
                $this->setFlashMessage('error', $validation['message']);
                $this->redirect('/attendance/create');
                return;
            }

            // Add attendance
            $result = $this->model->add(
                (int)$data['employee_id'],
                $data['work_date'],
                $data['check_in'] ?? null,
                $data['check_out'] ?? null,
                $data['status']
            );

            if ($result) {
                $this->setFlashMessage('success', 'Attendance record added successfully');
                $this->redirect('/attendance');
            } else {
                $this->setFlashMessage('error', 'Failed to add attendance record');
                $this->redirect('/attendance/create');
            }

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to create attendance record');
        }
    }

    /**
     * Xử lý cập nhật attendance
     */
    public function update(int $id): void
    {
        try {
            if (!$this->isLoggedIn() || !$this->isPostRequest()) {
                $this->setFlashMessage('error', 'Invalid request method');
                $this->redirect('/attendance');
                return;
            }

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect("/attendance/edit/{$id}");
                return;
            }

            // Check if record exists
            $existing = $this->model->getById($id);
            if (!$existing) {
                $this->setFlashMessage('error', 'Attendance record not found');
                $this->redirect('/attendance');
                return;
            }

            $data = $this->getPostData();

            // Validate required fields
            $validation = $this->validateAttendanceData($data);
            if (!$validation['success']) {
                $this->setFlashMessage('error', $validation['message']);
                $this->redirect("/attendance/edit/{$id}");
                return;
            }

            // Update attendance
            $result = $this->model->update(
                $id,
                (int)$data['employee_id'],
                $data['work_date'],
                $data['check_in'] ?? null,
                $data['check_out'] ?? null,
                $data['status']
            );

            if ($result) {
                $this->setFlashMessage('success', 'Attendance record updated successfully');
                $this->redirect('/attendance');
            } else {
                $this->setFlashMessage('error', 'Failed to update attendance record');
                $this->redirect("/attendance/edit/{$id}");
            }

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to update attendance record');
        }
    }

    /**
     * Xóa attendance (soft delete)
     */
    public function delete(int $id): void
    {
        try {
            if (!$this->isLoggedIn() || !$this->isPostRequest()) {
                $this->setFlashMessage('error', 'Invalid request method');
                $this->redirect('/attendance');
                return;
            }

            // Validate CSRF token
            if (!$this->validateCsrfToken()) {
                $this->setFlashMessage('error', 'Invalid security token');
                $this->redirect('/attendance');
                return;
            }

            // Check if record exists
            $existing = $this->model->getById($id);
            if (!$existing) {
                $this->setFlashMessage('error', 'Attendance record not found');
                $this->redirect('/attendance');
                return;
            }

            $result = $this->model->delete($id);

            if ($result) {
                $this->setFlashMessage('success', 'Attendance record deleted successfully');
            } else {
                $this->setFlashMessage('error', 'Failed to delete attendance record');
            }

            $this->redirect('/attendance');

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to delete attendance record');
        }
    }

    public function export(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to export data');
                $this->redirect('/login');
                return;
            }

            $month = $_GET['month'] ?? date('Y-m');
            $startDate = $month . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));

            // Get all attendance for the month
            $attendanceData = $this->model->getByDateRange($startDate, $endDate);

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=attendance_' . $month . '.csv');

            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($output, "\xEF\xBB\xBF");
            
            // CSV header
            fputcsv($output, [
                'Employee ID',
                'Employee Name',
                'Work Date',
                'Check In',
                'Check Out',
                'Status',
                'Working Hours'
            ]);

            // CSV data
            foreach ($attendanceData as $row) {
                $workingHours = $this->model->calculateWorkingHours($row['check_in'], $row['check_out']);
                
                fputcsv($output, [
                    $row['employee_code'],
                    $row['full_name'],
                    $row['work_date'],
                    $row['check_in'] ?? 'N/A',
                    $row['check_out'] ?? 'N/A',
                    $row['status'],
                    $workingHours
                ]);
            }

            fclose($output);
            exit();

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to export attendance data');
        }
    }

    /**
     * Hiển thị thống kê attendance
     */
    public function reports(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view reports');
                $this->redirect('/login');
                return;
            }

            $month = $_GET['month'] ?? date('Y-m');
            $departmentId = (int)($_GET['department_id'] ?? 0);

            $monthlyStats = $this->model->getMonthlyStats($month, $departmentId);
            $statusStats = $this->model->getStatsByStatus();

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load attendance reports');
        }
    }

    /**
     * Validate attendance data
     */
    private function validateAttendanceData(array $data): array
    {
        $required = ['employee_id', 'work_date', 'status'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return [
                    'success' => false,
                    'message' => "Field '{$field}' is required"
                ];
            }
        }

        // Validate employee_id
        if (!is_numeric($data['employee_id']) || $data['employee_id'] <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid employee ID'
            ];
        }

        // Validate date format
        if (!strtotime($data['work_date'])) {
            return [
                'success' => false,
                'message' => 'Invalid work date format'
            ];
        }

        // Validate work date not in future
        if (strtotime($data['work_date']) > time()) {
            return [
                'success' => false,
                'message' => 'Work date cannot be in the future'
            ];
        }

        // Validate time formats
        if (!empty($data['check_in']) && !$this->isValidTime($data['check_in'])) {
            return [
                'success' => false,
                'message' => 'Invalid check-in time format (HH:MM)'
            ];
        }

        if (!empty($data['check_out']) && !$this->isValidTime($data['check_out'])) {
            return [
                'success' => false,
                'message' => 'Invalid check-out time format (HH:MM)'
            ];
        }

        // Validate check-out after check-in
        if (!empty($data['check_in']) && !empty($data['check_out'])) {
            $checkInTime = strtotime($data['check_in']);
            $checkOutTime = strtotime($data['check_out']);
            
            if ($checkOutTime <= $checkInTime) {
                return [
                    'success' => false,
                    'message' => 'Check-out time must be after check-in time'
                ];
            }
        }

        // Validate status
        $validStatuses = ['present', 'absent', 'late', 'half_day', 'holiday'];
        if (!in_array($data['status'], $validStatuses)) {
            return [
                'success' => false,
                'message' => 'Invalid attendance status'
            ];
        }

        return ['success' => true, 'message' => 'Validation passed'];
    }

    /**
     * Check if time is valid (HH:MM format)
     */
    private function isValidTime(string $time): bool
    {
        return (bool)preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
    }

    /**
     * Bulk attendance action
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
                    'message' => 'No records selected'
                ], 400);
                return;
            }

            $successCount = 0;
            $errorCount = 0;

            switch ($action) {
                case 'delete':
                    foreach ($ids as $id) {
                        if ($this->model->delete((int)$id)) {
                            $successCount++;
                        } else {
                            $errorCount++;
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
                'message' => "Successfully processed {$successCount} records, {$errorCount} failed",
                'processed' => $successCount,
                'failed' => $errorCount
            ]);

        } catch (Exception $e) {
            error_log("Bulk action error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to process bulk action'
            ], 500);
        }
    }
}