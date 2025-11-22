<?php
session_start();
echo "<h1>Session Check</h1>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<pre>SESSION: " . print_r($_SESSION, true) . "</pre>";
echo '<p><a href="/CSDL/public/index.php?path=/login">Go to Login</a></p>';