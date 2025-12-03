<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<link rel="stylesheet" href="/CSDL/public/assets/css/main.css">
<link rel="stylesheet" href="/CSDL/public/assets/css/responsive.css">
<script src="/CSDL/public/assets/js/main.js"></script>


<div class="container">
    <h1>Cài đặt hệ thống</h1>

    <div class="row g-3 mt-3">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Cài đặt chung</h5>
                    <p class="card-text">Thông tin cơ bản của hệ thống như tên công ty, email, múi giờ, định dạng ngày giờ.</p>
                    <a href="/setting/general" class="btn btn-primary">Cấu hình</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Cài đặt Email</h5>
                    <p class="card-text">Cấu hình SMTP, email gửi đi hệ thống, xác thực email, template email.</p>
                    <a href="/setting/email" class="btn btn-primary">Cấu hình</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Cài đặt Bảng lương</h5>
                    <p class="card-text">Thiết lập công thức tính lương, ngày trả lương, loại phụ cấp, khấu trừ.</p>
                    <a href="/setting/payroll" class="btn btn-primary">Cấu hình</a>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
