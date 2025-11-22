<?php
session_start();
session_destroy();
echo "<h1>Session Cleared</h1>";
echo '<p><a href="/CSDL/public/index.php?path=/login">Go to Login</a></p>';