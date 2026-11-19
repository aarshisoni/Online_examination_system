<?php
// Forward to standardized student update script
$id = isset($_GET['studentid']) ? $_GET['studentid'] : '';
header("Location: studentList_update.php" . ($id ? "?studentid=" . urlencode($id) : ""));
exit();
