<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/EmployeeModel.php';  // Đường dẫn đúng
require_once __DIR__ . '/../models/DepartmentModel.php';
require_once __DIR__ . '/../models/AttendanceModel.php';
require_once __DIR__ . '/../models/ReportModel.php';

class DashboardController extends Controller
{
    protected $employeeModel;
    protected $departmentModel;
    protected $attendanceModel;
    protected $reportModel;

    public function __construct($conn)
    {
        parent::__construct($conn);
        $this->initializeModels();
        $this->requireAuth();
    }

    /**
     * Khởi tạo models
     */
    private function initializeModels(): void
    {
        try {
            // Employee Model
            if (file_exists(__DIR__ . '/../../models/EmployeeModel.php')) {
                require_once __DIR__ . '/../../models/EmployeeModel.php';
                $this->employeeModel = new EmployeeModel();
            }
            
            // Department Model
            if (file_exists(__DIR__ . '/../../models/DepartmentModel.php')) {
                require_once __DIR__ . '/../../models/DepartmentModel.php';
                $this->departmentModel = new DepartmentModel($this->conn);
            }
            
            // Attendance Model
            if (file_exists(__DIR__ . '/../../models/AttendanceModel.php')) {
                require_once __DIR__ . '/../../models/AttendanceModel.php';
                $this->attendanceModel = new AttendanceModel($this->conn);
            }
            
            // Report Model
            if (file_exists(__DIR__ . '/../../models/ReportModel.php')) {
                require_once __DIR__ . '/../../models/ReportModel.php';
                $this->reportModel = new ReportModel($this->conn);
            }
            
        } catch (Exception $e) {
            error_log("Initialize models error: " . $e->getMessage());
        }
    }

    /**
     * Hiển thị dashboard chính
     */
    public function index(): string
    {
        require_once __DIR__ . "/../models/EmployeeModel.php";
        $employeeModel = new EmployeeModel();

        $data['totalEmployees'] = $employeeModel->getTotalEmployees();
        $data['employees'] = $employeeModel->getAll();

        $this->Renderview("dashboard/index", $data);

        if (!$this->isLoggedIn()) {
        header("Location: /CSDL/public/index.php?path=/login");
        exit;
        }
        try {
            $stats = $this->getDashboardStats();
            $recentActivities = $this->getRecentActivities();
            $attendanceData = $this->getTodayAttendance();
            $currentUser = $this->getCurrentUser();

            $data = [
                'adminName' => $currentUser['name'] ?? 'Guest',
                'adminId' => $currentUser['admin_id'] ?? 'N/A',
                'stats' => $stats,
                'recentActivities' => $recentActivities,
                'attendanceData' => $attendanceData,
                'currentDate' => date('F j, Y')
            ];

            return $this->renderView('dashboard/index', $data);

        } catch (Exception $e) {
            return $this->renderView('error', [
                'message' => 'Could not load dashboard data'
            ]);
        }
    }

    /**
     * Lấy dữ liệu thống kê dashboard
     */
    private function getDashboardStats(): array
    {
        try {
            return [
                'total_employees' => $this->employeeModel ? $this->employeeModel->getTotalEmployees() : 0,
                'present_today' => $this->attendanceModel ? $this->attendanceModel->getPresentTodayCount() : 0,
                'total_departments' => $this->departmentModel ? $this->departmentModel->getTotalDepartments() : 0,
                'active_employees' => $this->employeeModel ? $this->employeeModel->getActiveEmployeesCount() : 0,
                'on_leave_today' => $this->attendanceModel ? $this->attendanceModel->getOnLeaveTodayCount() : 0,
                'new_this_month' => $this->employeeModel ? $this->employeeModel->getNewEmployeesThisMonth() : 0
            ];
        } catch (Exception $e) {
            error_log("getDashboardStats error: " . $e->getMessage());
            return [
                'total_employees' => 0,
                'present_today' => 0,
                'total_departments' => 0,
                'active_employees' => 0,
                'on_leave_today' => 0,
                'new_this_month' => 0
            ];
        }
    }

    /**
     * Lấy hoạt động gần đây
     */
    private function getRecentActivities(): array
    {
        try {
            if ($this->reportModel && method_exists($this->reportModel, 'getRecentActivities')) {
                return $this->reportModel->getRecentActivities(5);
            }
            
            // Fallback data
            return [
                ['action' => 'System initialized', 'time' => date('H:i:s'), 'user' => 'System']
            ];
            
        } catch (Exception $e) {
            error_log("getRecentActivities error: " . $e->getMessage());
            return [
                ['action' => 'Error loading activities', 'time' => date('H:i:s'), 'user' => 'System']
            ];
        }
    }

    /**
     * Lấy dữ liệu điểm danh hôm nay
     */
    private function getTodayAttendance(): array
    {
        try {
            if ($this->attendanceModel && method_exists($this->attendanceModel, 'getTodaySummary')) {
                return $this->attendanceModel->getTodaySummary();
            }
            
            // Fallback data
            return [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'on_leave' => 0
            ];
            
        } catch (Exception $e) {
            error_log("getTodayAttendance error: " . $e->getMessage());
            return [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'on_leave' => 0
            ];
        }
    }

    /**
     * API: dữ liệu dashboard cho AJAX
     */
    public function apiDashboardData(): void
    {
        try {
            $stats = $this->getDashboardStats();
            $attendanceData = $this->getTodayAttendance();
            $recentActivities = $this->getRecentActivities();

            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'attendance' => $attendanceData,
                    'recentActivities' => $recentActivities,
                    'lastUpdated' => date('Y-m-d H:i:s')
                ]
            ]);

        } catch (Exception $e) {
            error_log("API Dashboard error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'error' => 'Could not load dashboard data'
            ], 500);
        }
    }

    /**
     * Fallback nếu models không tồn tại
     */
    private function createFallbackModels(): void
    {
        if (!$this->employeeModel) {
            $this->employeeModel = new class() {
                public function getTotalEmployees(): int { return 0; }
                public function getActiveEmployeesCount(): int { return 0; }
                public function getNewEmployeesThisMonth(): int { return 0; }
            };
        }

        if (!$this->departmentModel) {
            $this->departmentModel = new class() {
                public function getTotalDepartments(): int { return 0; }
            };
        }

        if (!$this->attendanceModel) {
            $this->attendanceModel = new class() {
                public function getPresentTodayCount(): int { return 0; }
                public function getOnLeaveTodayCount(): int { return 0; }
                public function getTodaySummary(): array { 
                    return ['present' => 0, 'absent' => 0, 'late' => 0, 'on_leave' => 0];
                }
            };
        }

        if (!$this->reportModel) {
            $this->reportModel = new class() {
                public function getRecentActivities(int $limit = 5): array { 
                    return [['action' => 'System initialized', 'time' => date('H:i:s'), 'user' => 'System']];
                }
            };
        }
    }
}