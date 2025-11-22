<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - EMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold text-center mb-6">Đăng nhập hệ thống</h1>
        
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($_SESSION['login_error']); ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <!-- FORM ĐĂNG NHẬP -->
        <form action="/CSDL/public/index.php?path=/authenticate" method="POST" id="loginForm">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">
                    Tên đăng nhập
                </label>
                <input type="text" id="username" name="username" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required autofocus value="<?php echo htmlspecialchars($username ?? ''); ?>">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                    Mật khẩu
                </label>
                <input type="password" id="password" name="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                Đăng nhập
            </button>
        </form>
    </div>

    <!-- DEBUG: Thêm JavaScript để kiểm tra form -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            console.log('Form submitted!');
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            console.log('Username:', username);
            console.log('Password length:', password.length);
            
            // Kiểm tra required fields
            if (!username || !password) {
                alert('Vui lòng điền đầy đủ thông tin!');
                e.preventDefault();
            }
        });

        // Kiểm tra nút click
        document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
            console.log('Login button clicked!');
        });
    </script>
</body>
</html>