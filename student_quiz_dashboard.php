<?php
session_start();

if (!isset($_SESSION['studentid'])) {
    header("Location: student_login.php");
    exit();
}

include "connect.php";
$studentid = $_SESSION['studentid'];

// Fetch all assigned quizzes for this student
$query = "SELECT ss.title_name, ss.teacherid, ar.fname AS teacher_name, ar.branch AS teacher_branch,
                 (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.teacherid = ss.teacherid AND qq.title_name = ss.title_name) AS question_count,
                 (SELECT SUM(marks) FROM quiz_questions qq WHERE qq.teacherid = ss.teacherid AND qq.title_name = ss.title_name) AS total_marks,
                 sc.marks AS scored_marks,
                 sc.sid AS submission_id
          FROM selected_student ss
          LEFT JOIN admin_register ar ON ss.teacherid = ar.teacherid
          LEFT JOIN submit_score sc ON sc.studentid = ss.studentid AND sc.teacherid = ss.teacherid AND sc.title_name = ss.title_name
          WHERE ss.studentid = ?
          ORDER BY sc.sid ASC, ss.sid DESC";

$stmt = $con->prepare($query);
$stmt->bind_param("s", $studentid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Examinations Portal</title>
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
        .header-nav {
            display: flex;
            gap: 12px;
        }
        .nav-btn {
            color: white;
            text-decoration: none;
            background: rgba(0,0,0,0.15);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: 0.2s;
        }
        .nav-btn:hover {
            background: rgba(0,0,0,0.25);
        }
        .container {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 20px;
        }
        .intro-text {
            margin-bottom: 25px;
        }
        .intro-text h3 {
            font-size: 22px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .intro-text p {
            color: #7f8c8d;
            font-size: 15px;
        }
        .exam-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }
        .exam-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #eef2f5;
            padding: 25px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .exam-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .exam-title {
            font-size: 19px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .exam-details {
            margin: 15px 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 14px;
            color: #636e72;
        }
        .exam-details div {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-attempt {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            text-decoration: none;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(17, 153, 142, 0.3);
            transition: 0.2s;
        }
        .btn-attempt:hover {
            opacity: 0.95;
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.4);
        }
        .completed-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            color: #27ae60;
            font-size: 14px;
        }
        .empty-state {
            background: white;
            padding: 50px;
            border-radius: 12px;
            text-align: center;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-laptop-code"></i> Live Examinations</h2>
    <div class="header-nav">
        <a href="student_dashboard.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="student_score_dashboard.php" class="nav-btn"><i class="fas fa-trophy"></i> My Results</a>
        <a href="student_logout.php" class="nav-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</header>

<div class="container">
    <div class="intro-text">
        <h3>Available Tests & Exams</h3>
        <p>Select any examination assigned by your faculty to begin. Please read all instructions before starting.</p>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="exam-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php $isSubmitted = ($row['submission_id'] !== null); ?>
                <div class="exam-card">
                    <div>
                        <div class="exam-title"><?php echo htmlspecialchars($row['title_name']); ?></div>
                        <div class="exam-details">
                            <div><i class="fas fa-user-tie"></i> Faculty: <strong><?php echo htmlspecialchars($row['teacher_name'] ? $row['teacher_name'] : $row['teacherid']); ?></strong></div>
                            <div><i class="fas fa-question-circle"></i> Questions: <strong><?php echo $row['question_count']; ?></strong></div>
                            <div><i class="fas fa-award"></i> Total Marks: <strong><?php echo $row['total_marks'] ? $row['total_marks'] : '0'; ?></strong></div>
                        </div>
                    </div>

                    <?php if ($isSubmitted): ?>
                        <div class="completed-box">
                            <i class="fas fa-check-circle"></i> Exam Completed (Score: <?php echo $row['scored_marks']; ?>/<?php echo $row['total_marks']; ?>)
                        </div>
                    <?php else: ?>
                        <a href="student_start.php?title_name=<?php echo urlencode($row['title_name']); ?>&teacherid=<?php echo urlencode($row['teacherid']); ?>" class="btn-attempt">
                            <i class="fas fa-play"></i> Start Examination
                        </a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-folder-open" style="font-size: 48px; color: #cbd5e0; margin-bottom: 15px;"></i>
            <h3>No Live Exams Found</h3>
            <p>You currently do not have any pending exams assigned to your account.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
