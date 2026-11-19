<?php
// Forward to authenticated student_quiz_result.php
session_start();
$title_name = isset($_REQUEST['title_name']) ? $_REQUEST['title_name'] : '';
$teacherid = isset($_REQUEST['teacherid']) ? $_REQUEST['teacherid'] : '';
header("Location: student_quiz_result.php?title_name=" . urlencode($title_name) . "&teacherid=" . urlencode($teacherid));
exit();
