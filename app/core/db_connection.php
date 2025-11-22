<?php
function get_db_connection(): ?mysqli {
    $host = 'localhost';
    $user = 'appuser';
    $pass = 'ted1234';
    $dbname = 'db_employee_infomation_manager';
    $port = 3306;

    $conn = new mysqli($host, $user, $pass, $dbname, $port);

    if ($conn->connect_error) {
        error_log("DB connection error: " . $conn->connect_error);
        return null;
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
