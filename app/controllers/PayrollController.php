<?php
require_once "../app/core/Controller.php";

class PayrollController extends Controller
{
    private $payrollModel;
    private $employeeModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireLogin(); // logic login chung

        $this->payrollModel   = $this->model("Payroll");
        $this->employeeModel  = $this->model("Employee");
    }

    // ===========================
    // 1. Danh sách bảng lương
    // ===========================
    public function index()
    {
        $payrolls = $this->payrollModel->getAllPayrolls();
        $this->view("payroll/index", [
            'payrolls' => $payrolls,
            'base_url' => $this->config['base_url']
        ]);
    }

    // ===========================
    // 2. Tracking / Update Payroll
    // ===========================
    public function tracking($id)
    {
        $payroll = $this->payrollModel->getPayrollById((int)$id);
        if (!$payroll) {
            $this->flash('error', 'Payroll record not found.');
            $this->redirect($this->config['base_url'] . '/payroll');
        }

        $employees = $this->employeeModel->getAllEmployees();
        $this->view("payroll/tracking", [
            'payroll'   => $payroll,
            'employees' => $employees,
            'base_url'  => $this->config['base_url']
        ]);
    }

    // ===========================
    // 3. Cập nhật bảng lương
    // ===========================
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pay_day   = (int)$this->sanitize($_POST['pay_day']);
            $pay_month = (int)$this->sanitize($_POST['pay_month']);
            $pay_year  = (int)$this->sanitize($_POST['pay_year']);

            $payment_date = "$pay_year-$pay_month-$pay_day";

            $data = [
                'employee_id'   => $this->sanitize($_POST['employee_id']),
                'payment_date'  => $payment_date,
                'month'         => $pay_month,
                'year'          => $pay_year,
                'total_amount'  => $this->sanitize($_POST['total_amount']),
                'payment_status'=> $this->sanitize($_POST['payment_status'] ?? 'unpaid')
            ];

            $this->payrollModel->updatePayroll((int)$id, $data);
            $this->flash('success', 'Payroll updated successfully.');
            $this->redirect($this->config['base_url'] . '/payroll');
        }
    }

    // ===========================
    // 4. Xóa bảng lương
    // ===========================
    public function delete($id)
    {
        $this->payrollModel->deletePayroll((int)$id);
        $this->flash('success', 'Payroll deleted successfully.');
        $this->redirect($this->config['base_url'] . '/payroll');
    }
}
