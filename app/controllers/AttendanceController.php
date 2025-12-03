<?php
require_once "../app/core/Controller.php";

class AttendanceController extends Controller
{
    private $attendanceModel;
    private $employeeModel;

    public function __construct()
    {
        parent::__construct();
        $this->attendanceModel = $this->model("Attendance");
        $this->employeeModel = $this->model("Employee");
    }

    // ===========================
    // Show attendance list
    // ===========================
    public function index()
    {
        $filter = ['search' => $_GET['search'] ?? ''];

        if (!empty($filter['search'])) {
            $attendances = $this->attendanceModel->searchAttendance($filter['search']);
        } else {
            $attendances = $this->attendanceModel->getAllAttendance();
        }

        $this->view("attendance/index", [
            'attendances' => $attendances,
            'filter' => $filter,
            'base_url' => $this->config['base_url']
        ]);
    }

    // ===========================
    // Show attendance tracking form
    // ===========================
    public function tracking()
    {
        $employees = $this->employeeModel->getAllEmployees();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
            $data = [
                'employee_id' => $_POST['employee_id'],
                'work_date' => $_POST['work_date'],
                'checkin_time' => $_POST['checkin_time'],
                'checkout_time' => $_POST['checkout_time'] ?? null,
                'status' => $_POST['status']
            ];

            $this->attendanceModel->insertAttendance($data);
            $_SESSION['flash_message'] = "Attendance saved successfully.";
            header("Location: {$this->config['base_url']}/attendance/index");
            exit;
        }

        $this->view("attendance/tracking", [
            'employees' => $employees,
            'base_url' => $this->config['base_url']
        ]);
    }
}
