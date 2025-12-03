<?php
require_once "../app/core/Controller.php";

class SettingController extends Controller
{
    private $settingModel;

    public function __construct()
    {
        session_start();

        // Chặn truy cập nếu chưa login
        if (!isset($_SESSION['user'])) {
            header("Location: /EMS/public/index.php?url=auth/login");
            exit;
        }

        // Load model Setting
        $this->settingModel = $this->model("Setting");
    }

    // ===========================
    // 1. Xem cài đặt hệ thống
    // ===========================
    public function index()
    {
        $data['settings'] = $this->settingModel->getSettings();
        $this->view("setting/index", $data);
    }

    // ===========================
    // 2. Cập nhật cài đặt
    // ===========================
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ví dụ các setting cơ bản
            $site_name  = $_POST['site_name'] ?? '';
            $timezone   = $_POST['timezone'] ?? 'Asia/Ho_Chi_Minh';
            $currency   = $_POST['currency'] ?? 'VND';

            $this->settingModel->updateSettings([
                "site_name" => $site_name,
                "timezone"  => $timezone,
                "currency"  => $currency
            ]);

            header("Location: /EMS/public/index.php?url=setting/index");
            exit;
        }
    }
}
