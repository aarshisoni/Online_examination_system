<?php
// Forward to standardized student delete script
$id = isset($_GET['studentid']) ? $_GET['studentid'] : (isset($_GET['$studentid']) ? $_GET['$studentid'] : '');
header("Location: studentList_delete.php" . ($id ? "?studentid=" . urlencode($id) : ""));
exit();
