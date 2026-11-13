<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include 'connect.php';

$teacherid = $_SESSION['teacherid'];
$studentId = isset($_REQUEST['studentid']) ? trim($_REQUEST['studentid']) : '';
$titleName = isset($_REQUEST['title_name']) ? trim($_REQUEST['title_name']) : '';

if (!empty($studentId) && !empty($titleName)) {
    $stmt = $con->prepare("DELETE FROM selected_student WHERE studentid = ? AND teacherid = ? AND title_name = ?");
    $stmt->bind_param("sss", $studentId, $teacherid, $titleName);
    $stmt->execute();
}

header("Location: selected_student_list.php?teacherid=" . urlencode($teacherid) . "&title_name=" . urlencode($titleName));
exit();
?>
