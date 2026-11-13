<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";

if (isset($_GET['teacherid'])) {
    $target_teacherid = trim($_GET['teacherid']);
    
    // Prevent self-deletion if current user
    if ($target_teacherid === $_SESSION['teacherid']) {
        echo "<script>alert('You cannot delete your own active faculty account!'); window.location.href='admin_list.php';</script>";
        exit();
    }

    $stmt = $con->prepare("DELETE FROM admin_register WHERE teacherid = ?");
    $stmt->bind_param("s", $target_teacherid);
    if ($stmt->execute()) {
        echo "<script>alert('Faculty record deleted successfully.'); window.location.href='admin_list.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to delete faculty record: " . addslashes($con->error) . "'); window.location.href='admin_list.php';</script>";
        exit();
    }
} else {
    header("Location: admin_list.php");
    exit();
}
?>