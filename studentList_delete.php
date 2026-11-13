<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";

if (isset($_GET['studentid'])) {
    $studentid = trim($_GET['studentid']);

    // Delete student and cascade from selected_student and submit_score
    $stmt1 = $con->prepare("DELETE FROM register WHERE studentid = ?");
    $stmt1->bind_param("s", $studentid);
    $stmt1->execute();

    $stmt2 = $con->prepare("DELETE FROM selected_student WHERE studentid = ?");
    $stmt2->bind_param("s", $studentid);
    $stmt2->execute();

    $stmt3 = $con->prepare("DELETE FROM submit_score WHERE studentid = ?");
    $stmt3->bind_param("s", $studentid);
    $stmt3->execute();

    echo "<script>alert('Student record and related exam records deleted successfully.'); window.location.href='student_list.php';</script>";
    exit();
} else {
    header("Location: student_list.php");
    exit();
}
?>