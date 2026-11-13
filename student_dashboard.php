<?php
session_start();

if (!isset($_SESSION['studentid'])) {
    header("Location: student_login.php");
    exit();
}

include "connect.php";
$studentid = $_SESSION['studentid'];

// Fetch student info
$stmt = $con->prepare("SELECT * FROM register WHERE studentid = ?");
$stmt->bind_param("s", $studentid);
$stmt->execute();
$studentData = $stmt->get_result()->fetch_assoc();
$studentName = $studentData ? $studentData['fname'] : "Student";
$studentBranch = $studentData ? $studentData['branch'] : "Department";
$studentYear = $studentData ? $studentData['year'] : "Year";

// Fetch Assigned Quizzes with Teacher Name, Question Count, and Submission Status
$query_quizzes = "SELECT ss.title_name, ss.teacherid, ar.fname AS teacher_name,
                         (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.teacherid = ss.teacherid AND qq.title_name = ss.title_name) AS question_count,
                         (SELECT SUM(marks) FROM quiz_questions qq WHERE qq.teacherid = ss.teacherid AND qq.title_name = ss.title_name) AS total_marks,
                         sc.marks AS scored_marks,
                         sc.sid AS submission_id
                  FROM selected_student ss
                  LEFT JOIN admin_register ar ON ss.teacherid = ar.teacherid
                  LEFT JOIN submit_score sc ON sc.studentid = ss.studentid AND sc.teacherid = ss.teacherid AND sc.title_name = ss.title_name
                  WHERE ss.studentid = ?
                  ORDER BY sc.sid ASC, ss.sid DESC";

$stmt_q = $con->prepare($query_quizzes);
$stmt_q->bind_param("s", $studentid);
$stmt_q->execute();
$quizzes_result = $stmt_q->get_result();

$total_assigned = 0;
$total_completed = 0;
$pending_exams = 0;
$quiz_list = [];

while ($row = $quizzes_result->fetch_assoc()) {
    $quiz_list[] = $row;
    $total_assigned++;
    if ($row['submission_id'] !== null) {
        $total_completed++;
    } else {
        $pending_exams++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - <?php echo htmlspecialchars($studentName); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f0f2f5;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 20px;
            font-weight: 700;
        }
        .header-brand i {
            font-size: 24px;
        }
        .user-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0,0,0,0.15);
            padding: 8px 16px;
            border-radius: 30px;
        }
        .layout {
            display: flex;
            flex: 1;
        }
        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid #e1e4e8;
            padding: 25px 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .sidebar-heading {
            padding: 10px 25px 5px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8c9ba5;
            font-weight: 700;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 25px;
            color: #4a5568;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }
        .nav-item:hover, .nav-item.active {
            background-color: #f0fff4;
            color: #11998e;
            border-left-color: #11998e;
        }
        .nav-item i {
            font-size: 17px;
            width: 20px;
            color: #718096;
        }
        .nav-item:hover i, .nav-item.active i {
            color: #11998e;
        }
        .logout-item {
            margin-top: auto;
            color: #e53e3e;
        }
        .logout-item:hover {
            background-color: #fff5f5;
            color: #c53030;
            border-left-color: #e53e3e;
        }
        .main-content {
            flex: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }
        .welcome-card {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.25);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome-text h1 {
            font-size: 26px;
            margin-bottom: 6px;
        }
        .welcome-text p {
            font-size: 15px;
            opacity: 0.95;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #eef2f5;
            display: flex;
            align-items: center;
            gap: 18px;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        }
        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .icon-blue { background: #e3f2fd; color: #1976d2; }
        .icon-green { background: #e8f5e9; color: #388e3c; }
        .icon-orange { background: #fff3e0; color: #f57c00; }
        .stat-info h3 {
            font-size: 26px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 3px;
        }
        .stat-info span {
            font-size: 13px;
            color: #718096;
            font-weight: 500;
        }
        .section-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #eef2f5;
            margin-bottom: 30px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #edf2f7;
        }
        .section-header h2 {
            font-size: 18px;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
        }
        th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr:hover {
            background-color: #f8fafc;
        }
        .btn-start {
            background: #27ae60;
            color: white;
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
        }
        .btn-start:hover {
            background: #219150;
        }
        .badge-done {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #718096;
        }
    </style>
</head>
<body>

<header>
    <div class="header-brand">
        <i class="fas fa-graduation-cap"></i>
        <span>Student Examination Portal</span>
    </div>
    <div class="user-pill">
        <i class="fas fa-user-graduate"></i>
        <span><strong><?php echo htmlspecialchars($studentName); ?></strong> (ID: <?php echo htmlspecialchars($studentid); ?>)</span>
    </div>
</header>

<div class="layout">
    <!-- Sidebar -->
    <div class="sidebar">
        <span class="sidebar-heading">Navigation</span>
        <a href="student_dashboard.php" class="nav-item active"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="student_quiz_dashboard.php" class="nav-item"><i class="fas fa-clipboard-list"></i> Live Examinations</a>
        <a href="student_score_dashboard.php" class="nav-item"><i class="fas fa-trophy"></i> My Scorecard & History</a>
        <a href="student_profile.php" class="nav-item"><i class="fas fa-user-circle"></i> My Profile</a>

        <a href="student_logout.php" class="nav-item logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Welcome Banner -->
        <div class="welcome-card">
            <div class="welcome-text">
                <h1>Welcome, <?php echo htmlspecialchars($studentName); ?>!</h1>
                <p>Student ID: <strong><?php echo htmlspecialchars($studentid); ?></strong> | Branch: <strong><?php echo htmlspecialchars($studentBranch); ?></strong> | Year: <strong><?php echo htmlspecialchars($studentYear); ?></strong></p>
            </div>
            <div>
                <a href="student_quiz_dashboard.php" style="background: white; color: #11998e; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-play-circle"></i> View Active Exams
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class="fas fa-book-open"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_assigned; ?></h3>
                    <span>Total Exams Assigned</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-orange"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <h3><?php echo $pending_exams; ?></h3>
                    <span>Pending to Attempt</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_completed; ?></h3>
                    <span>Exams Completed</span>
                </div>
            </div>
        </div>

        <!-- Assigned Examinations Table -->
        <div class="section-card">
            <div class="section-header">
                <h2><i class="fas fa-laptop-code"></i> My Scheduled Examinations</h2>
                <a href="student_score_dashboard.php">View Past Results &#8594;</a>
            </div>

            <?php if (!empty($quiz_list)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject / Exam Title</th>
                            <th>Faculty / Examiner</th>
                            <th>Questions</th>
                            <th>Total Marks</th>
                            <th>Status / Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quiz_list as $q): ?>
                            <?php $isSubmitted = ($q['submission_id'] !== null); ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($q['title_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($q['teacher_name'] ? $q['teacher_name'] : $q['teacherid']); ?></td>
                                <td><?php echo $q['question_count']; ?> Questions</td>
                                <td><?php echo $q['total_marks'] ? $q['total_marks'] : '0'; ?> Marks</td>
                                <td>
                                    <?php if ($isSubmitted): ?>
                                        <span class="badge-done">
                                            <i class="fas fa-check"></i> Scored: <?php echo $q['scored_marks']; ?>/<?php echo $q['total_marks']; ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="student_start.php?title_name=<?php echo urlencode($q['title_name']); ?>&teacherid=<?php echo urlencode($q['teacherid']); ?>" class="btn-start">
                                            <i class="fas fa-pencil-alt"></i> Attempt Exam
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-info-circle" style="font-size: 40px; color: #cbd5e0; margin-bottom: 12px;"></i>
                    <p>No exams have been assigned to you by faculty yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
