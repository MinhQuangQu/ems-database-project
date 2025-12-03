<?php
class DashboardController extends Controller
{
    public function index()
    {
        $this->requireLogin(); // bảo vệ route

        $dashboardModel = $this->model("DashboardModel"); // model lấy dữ liệu

        $dashboardData = [
            'totalEmployees'    => $dashboardModel->getTotalEmployees(),
            'totalDepartments'  => $dashboardModel->getTotalDepartments(),
            'totalPayroll'      => $dashboardModel->getTotalPayroll(),
            'recentEmployees'   => $dashboardModel->getRecentEmployees(5),
        ];

        $this->view("dashboard/index", ['dashboardData' => $dashboardData]);
    }
}

