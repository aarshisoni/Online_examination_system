<?php
session_start();

if (!isset($_SESSION['studentid'])) {
    header("Location: student_login.php");
    exit();
}

include "connect.php";
$studentid = $_SESSION['studentid'];

$title_name = isset($_GET['title_name']) ? trim($_GET['title_name']) : '';
$teacherid = isset($_GET['teacherid']) ? trim($_GET['teacherid']) : '';

if (empty($title_name) || empty($teacherid)) {
    header("Location: student_quiz_dashboard.php");
    exit();
}

// Check if student has already submitted this exam
$check = $con->prepare("SELECT sid, marks FROM submit_score WHERE studentid = ? AND teacherid = ? AND title_name = ?");
$check->bind_param("sss", $studentid, $teacherid, $title_name);
$check->execute();
$check_res = $check->get_result();
if ($check_res->num_rows > 0) {
    echo "<script>alert('You have already attempted and submitted this exam!'); window.location.href='student_quiz_dashboard.php';</script>";
    exit();
}

// Fetch total questions and marks
$q_stmt = $con->prepare("SELECT COUNT(*) AS total_q, SUM(marks) AS total_m FROM quiz_questions WHERE teacherid = ? AND title_name = ?");
$q_stmt->bind_param("ss", $teacherid, $title_name);
$q_stmt->execute();
$q_data = $q_stmt->get_result()->fetch_assoc();
$totalQuestions = $q_data['total_q'] ? $q_data['total_q'] : 0;
$totalMarks = $q_data['total_m'] ? $q_data['total_m'] : 0;

if ($totalQuestions == 0) {
    echo "<script>alert('This examination does not have any questions yet. Please contact your instructor.'); window.location.href='student_quiz_dashboard.php';</script>";
    exit();
}

// Fetch teacher name
$t_stmt = $con->prepare("SELECT fname FROM admin_register WHERE teacherid = ?");
$t_stmt->bind_param("s", $teacherid);
$t_stmt->execute();
$t_res = $t_stmt->get_result()->fetch_assoc();
$teacherName = $t_res ? $t_res['fname'] : $teacherid;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructions - <?php echo htmlspecialchars($title_name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header { 
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 18px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 35px 45px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .exam-banner {
            border-bottom: 2px solid #eef2f5;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .exam-banner h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 12px;
        }
        .exam-meta {
            display: flex;
            gap: 25px;
            color: #555;
            font-size: 15px;
            flex-wrap: wrap;
        }
        .exam-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .instructions-list {
            background: #f8fafc;
            border-left: 4px solid #11998e;
            padding: 20px 25px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        .instructions-list h3 {
            margin-top: 0;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .instructions-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .instructions-list li {
            margin-bottom: 12px;
            line-height: 1.6;
            color: #4a5568;
        }
        .action-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
        .btn-cancel {
            color: #e53e3e;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
        }
        .btn-start {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
            transition: 0.2s;
        }
        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(17, 153, 142, 0.4);
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-file-signature"></i> Examination Instructions</h2>
    <a href="student_quiz_dashboard.php" style="color: white; text-decoration: none; background: rgba(0,0,0,0.15); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
        <i class="fas fa-arrow-left"></i> Back to Portal
    </a>
</header>

<div class="container">
    <div class="exam-banner">
        <h1><?php echo htmlspecialchars($title_name); ?></h1>
        <div class="exam-meta">
            <span><i class="fas fa-user-tie"></i> Faculty: <strong><?php echo htmlspecialchars($teacherName); ?></strong></span>
            <span><i class="fas fa-question-circle"></i> Questions: <strong><?php echo $totalQuestions; ?></strong></span>
            <span><i class="fas fa-award"></i> Total Marks: <strong><?php echo $totalMarks; ?></strong></span>
        </div>
    </div>

    <div class="instructions-list">
        <h3><i class="fas fa-info-circle"></i> Important Exam Guidelines</h3>
        <ul>
            <li>Please read each question carefully before selecting your answer.</li>
            <li>Each question contains multiple choices with only <strong>one</strong> correct answer.</li>
            <li>There is no negative marking unless stated otherwise by the faculty.</li>
            <li>Do not refresh the page or navigate away during the live exam.</li>
            <li>Once you submit your examination, your answers are final and your scorecard will be generated immediately.</li>
            <li>Re-attempting an examination after submission is not allowed.</li>
        </ul>
    </div>

    <div class="action-area">
        <a href="student_quiz_dashboard.php" class="btn-cancel"><i class="fas fa-times"></i> Cancel & Return</a>
        <a href="student_generate_quiz.php?title_name=<?php echo urlencode($title_name); ?>&teacherid=<?php echo urlencode($teacherid); ?>" class="btn-start">
            <span>I Understand, Begin Test</span> <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
</body>
</html>
