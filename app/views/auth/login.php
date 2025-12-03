<?php
$config = require __DIR__ . '/../../config/config.php';
$base_url = $config['base_url'] ?? '';
require_once __DIR__ . '/../layouts/auth_header.php';
require_once __DIR__ . '/../partials/flash_message.php';
?>

<body class="bg-gradient-to-r from-blue-100 to-blue-300 min-h-screen flex items-center justify-center">

<div class="bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl rounded-2xl p-10 w-full max-w-2xl text-blue-800">

    <div class="text-center mb-8">
        <div class="flex items-center justify-center space-x-4">
            <img src="<?= $base_url ?>/assets/images/neu.png" class="w-14 h-14" />
            <h1 class="text-4xl font-bold tracking-wide">NEU Employee System</h1>
        </div>
        <p class="mt-2 text-blue-800/80">Welcome back! Please login to continue.</p>
    </div>

    <form action="<?= $base_url ?>/auth/login" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

        <div>
            <label class="block mb-1 font-medium">Username</label>
            <input type="text" name="username" required
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   class="w-full px-4 py-3 rounded-lg bg-white/40 border border-white/30 text-blue-800 
                          placeholder-blue-500 focus:outline-none focus:border-blue-600"
                   placeholder="Enter your username" />
        </div>

        <div>
            <label class="block mb-1 font-medium">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-4 py-3 rounded-lg bg-white/40 border border-white/30 
                          text-blue-800 placeholder-blue-500 focus:outline-none focus:border-blue-600"
                   placeholder="Enter your password" />
        </div>

        <button type="submit"
                class="w-full py-3 bg-white text-blue-800 font-semibold rounded-lg shadow-lg transition hover:bg-blue-100">
            Login
        </button>
    </form>

    <p class="text-center mt-6 text-sm text-blue-800/80">
        Don't have an account?
        <a href="<?= $base_url ?>/auth/register" class="underline font-medium hover:text-blue-600">Register</a>
    </p>

</div>

</body>
<?php require_once __DIR__ . '/../layouts/auth_footer.php'; ?>
