<?php
// Database connection configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'database1';

// Enable mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $con = new mysqli($host, $user, $pass, $db);
    $con->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage() . "<br>Please ensure MySQL is running in XAMPP and database 'database1' is imported.");
}
?>