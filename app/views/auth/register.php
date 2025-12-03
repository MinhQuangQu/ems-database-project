<?php require_once __DIR__ . '/../layouts/auth_header.php'; ?>
<?php require_once __DIR__ . '/../partials/flash_message.php'; ?>

<body class="bg-gradient-to-r from-blue-100 to-blue-300 min-h-screen flex items-center justify-center">

  <div class="bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl rounded-2xl p-10 w-full max-w-2xl text-blue-800">

    <div class="text-center mb-8">
      <div class="flex items-center justify-center space-x-4">
        <img src="/public/assets/images/neu.png" class="w-14 h-14" />
        <h1 class="text-4xl font-bold tracking-wide">Create Account</h1>
      </div>
      <p class="mt-2 center text-blue-800/80">Register to access the EMS NEU.</p>
    </div>

    <form action="/auth/register" method="POST" class="space-y-6">

      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

      <div>
        <label class="block mb-1 font-medium">Username</label>
        <input type="text" name="username"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          required
          class="w-full px-4 py-3 rounded-lg bg-white/40 border border-white/30 
                 text-blue-800 placeholder-blue-500 focus:outline-none focus:border-blue-600"
          placeholder="Choose a username" />
      </div>

      <div>
        <label class="block mb-1 font-medium">Password</label>
        <div class="relative">
          <input type="password" id="password" name="password" required
            class="w-full px-4 py-3 pr-12 rounded-lg bg-white/40 border border-white/30 
                   text-blue-800 placeholder-blue-500 focus:outline-none focus:border-blue-600"
            placeholder="Create a password" />
          <button type="button" onclick="togglePassword('password','eye1')"
            class="absolute inset-y-0 right-3 flex items-center">
            <svg id="eye1" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-800"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
      </div>

      <div>
        <label class="block mb-1 font-medium">Confirm Password</label>
        <div class="relative">
          <input type="password" id="confirm_password" name="password_confirm" required
            class="w-full px-4 py-3 pr-12 rounded-lg bg-white/40 border border-white/30 
                   text-blue-800 placeholder-blue-500 focus:outline-none focus:border-blue-600"
            placeholder="Re-enter password" />
          <button type="button" onclick="togglePassword('confirm_password','eye2')"
            class="absolute inset-y-0 right-3 flex items-center">
            <svg id="eye2" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-800"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
      </div>

      <button type="submit"
        class="w-full py-3 bg-white text-blue-800 font-semibold rounded-lg shadow-lg transition hover:bg-blue-100">
        Register
      </button>
    </form>

    <p class="text-center mt-6 text-sm text-blue-800/80">
      Already have an account?
      <a href="/auth/login" class="underline font-medium hover:text-blue-600">Login</a>
    </p>

  </div>

  <script>
    function togglePassword(inputId, iconId) {
      const field = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      field.type = field.type === "password" ? "text" : "password";
    }
  </script>

</body>

<?php require_once __DIR__ . '/../layouts/auth_footer.php'; ?>
