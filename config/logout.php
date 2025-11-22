<?php
session_start();
session_destroy();
header('Location: /CSDL/resource/views/auth/login.php');
exit;
