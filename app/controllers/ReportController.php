<?php
// app/controllers/ReportController.php

declare(strict_types=1);

include_once __DIR__ . '/../core/Controller.php';
include_once __DIR__ . '/../models/Report.php';
include_once __DIR__ . '/../models/Department.php';

class ReportController extends Controller
{
    private ReportModel $model;

    public function __construct($conn)
    {
        parent::__construct();
        $this->model = new ReportModel($conn);
    }

    /**
     * Hiển thị trang chủ báo cáo với danh sách các loại báo cáo
     */
    public function index(): void
    {
        try {
            // Check authentication
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to access reports');
                $this->redirect('/login');
                return;
            }

            $this->Renderview('report/index', [
                'pageTitle' => 'Reports Dashboard',
                'availableReports' => $this->model->getAvailableReportTypes(),
                'csrfToken' => $this->generateCsrfToken()
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load reports dashboard');
        }
    }

    /**
     * Báo cáo lương hàng tháng
     */
    public function salary(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view salary reports');
                $this->redirect('/login');
                return;
            }

            // Get filter parameters
            $yearMonth = $_GET['month'] ?? date('Y-m');
            $departmentId = (int)($_GET['department_id'] ?? 0);

            // Get data
            $monthlySalary = $this->model->getMonthlySalaryView($yearMonth);
            $departmentPayroll = $this->model->getDepartmentPayroll($yearMonth);
            $avgSalaryByDept = $this->model->getAvgSalaryByDepartment($departmentId);
            $salaryDistribution = $this->model->getSalaryDistributionReport();

            // Load departments for filter
            $departmentModel = new DepartmentModel($this->conn);
            $departments = $departmentModel->getAllActive();

            $this->Renderview('report/salary', [
                'monthlySalary' => $monthlySalary,
                'departmentPayroll' => $departmentPayroll,
                'avgSalaryByDept' => $avgSalaryByDept,
                'salaryDistribution' => $salaryDistribution,
                'departments' => $departments,
                'selectedMonth' => $yearMonth,
                'selectedDepartment' => $departmentId,
                'pageTitle' => 'Salary Reports'
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load salary reports');
        }
    }

    /**
     * Báo cáo attendance
     */
    public function attendance(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view attendance reports');
                $this->redirect('/login');
                return;
            }

            // Get filter parameters
            $yearMonth = $_GET['month'] ?? date('Y-m');
            $departmentId = (int)($_GET['department_id'] ?? 0);

            // Get data
            $attendanceReport = $this->model->getRecentActive($yearMonth, $departmentId);
            $departmentPerformance = $this->model->getDepartmentPerformanceReport($yearMonth);

            // Load departments for filter
            $departmentModel = new DepartmentModel($this->conn);
            $departments = $departmentModel->getAllActive();

            $this->Renderview('report/attendance', [
                'attendanceReport' => $attendanceReport,
                'departmentPerformance' => $departmentPerformance,
                'departments' => $departments,
                'selectedMonth' => $yearMonth,
                'selectedDepartment' => $departmentId,
                'pageTitle' => 'Attendance Reports'
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load attendance reports');
        }
    }

    /**
     * Báo cáo overtime
     */
    public function overtime(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view overtime reports');
                $this->redirect('/login');
                return;
            }

            // Get filter parameters
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-t');
            $departmentId = (int)($_GET['department_id'] ?? 0);

            // Get data
            $overtimeReport = $this->model->getOvertimeReport($startDate, $endDate, $departmentId);

            // Load departments for filter
            $departmentModel = new DepartmentModel($this->conn);
            $departments = $departmentModel->getAllActive();

            $this->Renderview('report/overtime', [
                'overtimeReport' => $overtimeReport,
                'departments' => $departments,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'selectedDepartment' => $departmentId,
                'pageTitle' => 'Overtime Reports'
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load overtime reports');
        }
    }

    /**
     * Báo cáo project assignments
     */
    public function project(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view project reports');
                $this->redirect('/login');
                return;
            }

            // Get filter parameters
            $projectId = (int)($_GET['project_id'] ?? 0);

            // Get data
            $projectReport = $this->model->getEmployeePerProject($projectId);

            $this->Renderview('report/project', [
                'projectReport' => $projectReport,
                'selectedProject' => $projectId,
                'pageTitle' => 'Project Assignment Reports'
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load project reports');
        }
    }

    /**
     * Báo cáo turnover rate
     */
    public function turnover(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view turnover reports');
                $this->redirect('/login');
                return;
            }

            // Get filter parameters
            $startDate = $_GET['start_date'] ?? date('Y-m-01', strtotime('-12 months'));
            $endDate = $_GET['end_date'] ?? date('Y-m-t');

            // Get data
            $turnoverReport = $this->model->getTurnoverRateReport($startDate, $endDate);

            $this->Renderview('report/turnover', [
                'turnoverReport' => $turnoverReport,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'pageTitle' => 'Turnover Rate Reports'
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load turnover reports');
        }
    }

    /**
     * Báo cáo department performance
     */
    public function department(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view department reports');
                $this->redirect('/login');
                return;
            }

            // Get filter parameters
            $yearMonth = $_GET['month'] ?? date('Y-m');

            // Get data
            $departmentPerformance = $this->model->getDepartmentPerformanceReport($yearMonth);
            $avgSalaryByDept = $this->model->getAvgSalaryByDepartment();

            $this->Renderview('report/department', [
                'departmentPerformance' => $departmentPerformance,
                'avgSalaryByDept' => $avgSalaryByDept,
                'selectedMonth' => $yearMonth,
                'pageTitle' => 'Department Performance Reports'
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load department reports');
        }
    }

    /**
     * Export báo cáo sang CSV
     */
    public function export(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to export reports');
                $this->redirect('/login');
                return;
            }

            $reportType = $_GET['type'] ?? '';
            $format = $_GET['format'] ?? 'csv';

            if (empty($reportType)) {
                $this->setFlashMessage('error', 'Report type is required');
                $this->redirect('/reports');
                return;
            }

            $data = [];
            $filename = '';
            $headers = [];

            switch ($reportType) {
                case 'salary':
                    $yearMonth = $_GET['month'] ?? date('Y-m');
                    $data = $this->model->getMonthlySalaryView($yearMonth);
                    $filename = "salary_report_{$yearMonth}";
                    $headers = ['Year Month', 'Department', 'Total Employees', 'Total Salary', 'Average Salary'];
                    break;

                case 'attendance':
                    $yearMonth = $_GET['month'] ?? date('Y-m');
                    $departmentId = (int)($_GET['department_id'] ?? 0);
                    $data = $this->model->getRecentActive($yearMonth, $departmentId);
                    $filename = "attendance_report_{$yearMonth}";
                    $headers = ['Employee Code', 'Full Name', 'Department', 'Total Days', 'Present Days', 'Absent Days', 'Late Days', 'Attendance Rate'];
                    break;

                case 'overtime':
                    $startDate = $_GET['start_date'] ?? date('Y-m-01');
                    $endDate = $_GET['end_date'] ?? date('Y-m-t');
                    $departmentId = (int)($_GET['department_id'] ?? 0);
                    $data = $this->model->getOvertimeReport($startDate, $endDate, $departmentId);
                    $filename = "overtime_report_{$startDate}_to_{$endDate}";
                    $headers = ['Employee Code', 'Full Name', 'Department', 'Total Overtime Hours', 'Overtime Days', 'Average Overtime per Day'];
                    break;

                case 'turnover':
                    $startDate = $_GET['start_date'] ?? date('Y-m-01', strtotime('-12 months'));
                    $endDate = $_GET['end_date'] ?? date('Y-m-t');
                    $data = $this->model->getTurnoverRateReport($startDate, $endDate);
                    $filename = "turnover_report_{$startDate}_to_{$endDate}";
                    $headers = ['Month', 'Employees Joined', 'Employees Left', 'Turnover Rate'];
                    break;

                default:
                    $this->setFlashMessage('error', 'Invalid report type');
                    $this->redirect('/reports');
                    return;
            }

            if ($format === 'csv') {
                $this->exportToCSV($data, $filename, $headers);
            } else {
                $this->setFlashMessage('error', 'Unsupported export format');
                $this->redirect('/reports');
            }

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to export report');
        }
    }

    /**
     * API: Lấy dữ liệu báo cáo dạng JSON
     */
    public function apiData(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
                return;
            }

            $reportType = $_GET['type'] ?? '';
            $yearMonth = $_GET['month'] ?? date('Y-m');
            $departmentId = (int)($_GET['department_id'] ?? 0);
            $startDate = $_GET['start_date'] ?? '';
            $endDate = $_GET['end_date'] ?? '';

            if (empty($reportType)) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Report type is required'
                ], 400);
                return;
            }

            $data = [];

            switch ($reportType) {
                case 'salary_distribution':
                    $data = $this->model->getSalaryDistributionReport();
                    break;

                case 'department_performance':
                    $data = $this->model->getDepartmentPerformanceReport($yearMonth);
                    break;

                case 'attendance_summary':
                    $data = $this->model->getRecentActive($yearMonth, $departmentId);
                    break;

                case 'overtime_summary':
                    if (empty($startDate) || empty($endDate)) {
                        $startDate = date('Y-m-01');
                        $endDate = date('Y-m-t');
                    }
                    $data = $this->model->getOvertimeReport($startDate, $endDate, $departmentId);
                    break;

                default:
                    $this->jsonResponse([
                        'success' => false,
                        'message' => 'Invalid report type'
                    ], 400);
                    return;
            }

            $this->jsonResponse([
                'success' => true,
                'data' => $data,
                'reportType' => $reportType
            ]);

        } catch (Exception $e) {
            error_log("API Report data error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to fetch report data'
            ], 500);
        }
    }

    /**
     * Export dữ liệu sang CSV
     */
    private function exportToCSV(array $data, string $filename, array $headers): void
    {
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');

        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fputs($output, "\xEF\xBB\xBF");
        
        // CSV header
        fputcsv($output, $headers);

        // CSV data
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($headers as $header) {
                // Map header to actual data field
                $field = $this->mapHeaderToField($header);
                $csvRow[] = $row[$field] ?? '';
            }
            fputcsv($output, $csvRow);
        }

        fclose($output);
        exit();
    }

    /**
     * Map header names to actual data fields
     */
    private function mapHeaderToField(string $header): string
    {
        $mapping = [
            'Year Month' => 'year_month',
            'Department' => 'department_name',
            'Total Employees' => 'total_employees',
            'Total Salary' => 'total_salary',
            'Average Salary' => 'avg_salary',
            'Employee Code' => 'employee_code',
            'Full Name' => 'full_name',
            'Total Days' => 'total_days',
            'Present Days' => 'present_days',
            'Absent Days' => 'absent_days',
            'Late Days' => 'late_days',
            'Attendance Rate' => 'attendance_rate',
            'Total Overtime Hours' => 'total_overtime_hours',
            'Overtime Days' => 'overtime_days',
            'Average Overtime per Day' => 'avg_overtime_per_day',
            'Month' => 'month',
            'Total Bonus' => 'total_bonus',
            'Bonus Count' => 'bonus_count',
            'Average Bonus' => 'avg_bonus',
            'Employees Joined' => 'employees_joined',
            'Employees Left' => 'employees_left',
            'Turnover Rate' => 'turnover_rate'
        ];

        return $mapping[$header] ?? strtolower(str_replace(' ', '_', $header));
    }

    /**
     * Hiển thị dashboard với các báo cáo tổng quan
     */
    public function dashboard(): void
    {
        try {
            if (!$this->isLoggedIn()) {
                $this->setFlashMessage('error', 'Please login to view dashboard');
                $this->redirect('/login');
                return;
            }

            // Get current month data
            $currentMonth = date('Y-m');
            $attendanceSummary = $this->model->getRecentActive($currentMonth);
            $salarySummary = $this->model->getMonthlySalaryView($currentMonth);
            $departmentPerformance = $this->model->getDepartmentPerformanceReport($currentMonth);
            $turnoverRate = $this->model->getTurnoverRateReport(
                date('Y-m-01', strtotime('-6 months')),
                date('Y-m-t')
            );

            $this->Renderview('report/dashboard', [
                'attendanceSummary' => $attendanceSummary,
                'salarySummary' => $salarySummary,
                'departmentPerformance' => $departmentPerformance,
                'turnoverRate' => $turnoverRate,
                'currentMonth' => $currentMonth,
                'pageTitle' => 'Reports Dashboard'
            ]);

        } catch (Exception $e) {
            $this->handleException($e, 'Failed to load reports dashboard');
        }
    }
}