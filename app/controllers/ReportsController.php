<?php
require_once "../app/core/Controller.php";

class ReportsController extends Controller
{
    private $reportModel;

    public function __construct()
    {
        parent::__construct();
        $this->reportModel = $this->model("Report");
    }

    // ===========================
    // Index report – show 1 employee by search
    // ===========================
    public function index()
    {
        $search = $_GET['search'] ?? null;
        $employeeId = $_GET['employee_id'] ?? null;
        $filter = ['search' => $search];

        $employee = [];
        $attendanceStats = $stats = [
            'Present' => 0,
            'Absent' => 0,
            'On Leave' => 0,
        ];
        $payroll = [];

        if ($employeeId) {
            // Lấy theo ID
            $employee = $this->reportModel->getEmployeeById((int)$employeeId);
        } elseif ($search) {
            // Lấy employee đầu tiên matching tên search
            $employee = $this->reportModel->getEmployeeByName($search);
            if ($employee) $employeeId = $employee['employee_id'];
        }

        if ($employeeId) {
            $attendanceStats = $this->reportModel->getAttendanceByEmployee((int)$employeeId);
            $payroll = $this->reportModel->getPayrollByEmployee((int)$employeeId);
        }

        $this->view("report/index", [
            'employee' => $employee,
            'attendanceStats' => $attendanceStats,
            'payroll' => $payroll,
            'filter' => $filter,
            'base_url' => $this->config['base_url']
        ]);
    }

}
