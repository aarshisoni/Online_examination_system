<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";
$teacherid = $_SESSION['teacherid'];

// Fetch teacher details
$stmt = $con->prepare("SELECT * FROM admin_register WHERE teacherid = ?");
$stmt->bind_param("s", $teacherid);
$stmt->execute();
$adminData = $stmt->get_result()->fetch_assoc();
$adminName = $adminData ? $adminData['fname'] : "Faculty";
$adminBranch = $adminData ? $adminData['branch'] : "Department";

// Fetch Stats
// 1. Total Quizzes created by this teacher
$q_stmt = $con->prepare("SELECT COUNT(*) AS total FROM quiz_title WHERE teacherid = ?");
$q_stmt->bind_param("s", $teacherid);
$q_stmt->execute();
$total_quizzes = $q_stmt->get_result()->fetch_assoc()['total'];

// 2. Total Questions created by this teacher
$qn_stmt = $con->prepare("SELECT COUNT(*) AS total FROM quiz_questions WHERE teacherid = ?");
$qn_stmt->bind_param("s", $teacherid);
$qn_stmt->execute();
$total_questions = $qn_stmt->get_result()->fetch_assoc()['total'];

// 3. Total Students Assigned across exams
$st_stmt = $con->prepare("SELECT COUNT(DISTINCT studentid) AS total FROM selected_student WHERE teacherid = ?");
$st_stmt->bind_param("s", $teacherid);
$st_stmt->execute();
$total_assigned_students = $st_stmt->get_result()->fetch_assoc()['total'];

// 4. Total Exam Submissions
$sub_stmt = $con->prepare("SELECT COUNT(*) AS total FROM submit_score WHERE teacherid = ?");
$sub_stmt->bind_param("s", $teacherid);
$sub_stmt->execute();
$total_submissions = $sub_stmt->get_result()->fetch_assoc()['total'];

// Fetch Recent Quizzes
$recent_stmt = $con->prepare("SELECT qt.title_name,
                                (SELECT COUNT(*) FROM quiz_questions WHERE teacherid = qt.teacherid AND title_name = qt.title_name) AS q_count,
                                (SELECT COUNT(*) FROM selected_student WHERE teacherid = qt.teacherid AND title_name = qt.title_name) AS s_count,
                                (SELECT COUNT(*) FROM submit_score WHERE teacherid = qt.teacherid AND title_name = qt.title_name) AS sub_count
                             FROM quiz_title qt WHERE qt.teacherid = ? ORDER BY qt.id DESC LIMIT 5");
$recent_stmt->bind_param("s", $teacherid);
$recent_stmt->execute();
$recent_quizzes = $recent_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - <?php echo htmlspecialchars($adminName); ?></title>
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
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
            background: rgba(255,255,255,0.15);
            padding: 8px 16px;
            border-radius: 30px;
        }
        .user-pill i {
            font-size: 18px;
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
            background-color: #f7fafc;
            color: #1e3c72;
            border-left-color: #1e3c72;
        }
        .nav-item i {
            font-size: 17px;
            width: 20px;
            color: #718096;
        }
        .nav-item:hover i, .nav-item.active i {
            color: #1e3c72;
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
        .logout-item i {
            color: #e53e3e;
        }
        .main-content {
            flex: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }
        .welcome-card {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(30, 60, 114, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome-text h1 {
            font-size: 26px;
            margin-bottom: 8px;
        }
        .welcome-text p {
            opacity: 0.9;
            font-size: 15px;
        }
        .btn-quick-create {
            background: #27ae60;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: 0.2s;
        }
        .btn-quick-create:hover {
            background: #219150;
            transform: translateY(-2px);
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
        .icon-purple { background: #f3e5f5; color: #7b1fa2; }
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
        .section-header a {
            color: #1e3c72;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 16px;
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
        .btn-sm {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            margin-right: 5px;
            display: inline-block;
        }
        .btn-view { background: #e2e8f0; color: #4a5568; }
        .btn-view:hover { background: #cbd5e0; }
        .btn-manage { background: #ebf8ff; color: #2b6cb0; }
        .btn-manage:hover { background: #bee3f8; }
    </style>
</head>
<body>

<header>
    <div class="header-brand">
        <i class="fas fa-graduation-cap"></i>
        <span>Examination Portal</span>
    </div>
    <div class="user-pill">
        <i class="fas fa-user-tie"></i>
        <span><strong><?php echo htmlspecialchars($adminName); ?></strong> (<?php echo htmlspecialchars($adminBranch); ?>)</span>
    </div>
</header>

<div class="layout">
    <!-- Sidebar -->
    <div class="sidebar">
        <span class="sidebar-heading">Main Navigation</span>
        <a href="admin_dashboard.php" class="nav-item active"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="new_title.php" class="nav-item"><i class="fas fa-plus-circle"></i> Conduct Exam / Subject</a>
        <a href="display_quize.php" class="nav-item"><i class="fas fa-book-open"></i> My Quizzes & Courses</a>
        <a href="student_score.php" class="nav-item"><i class="fas fa-trophy"></i> Student Results & Scores</a>

        <span class="sidebar-heading" style="margin-top: 15px;">Management</span>
        <a href="student_list.php" class="nav-item"><i class="fas fa-user-graduate"></i> Manage Students</a>
        <a href="admin_list.php" class="nav-item"><i class="fas fa-chalkboard-teacher"></i> Faculty Directory</a>
        <a href="admin_profile.php" class="nav-item"><i class="fas fa-user-cog"></i> My Profile</a>

        <a href="admin_logout.php" class="nav-item logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Welcome Banner -->
        <div class="welcome-card">
            <div class="welcome-text">
                <h1>Welcome back, Prof. <?php echo htmlspecialchars($adminName); ?>!</h1>
                <p>Teacher ID: <strong><?php echo htmlspecialchars($teacherid); ?></strong> | Department: <strong><?php echo htmlspecialchars($adminBranch); ?></strong></p>
            </div>
            <a href="new_title.php" class="btn-quick-create"><i class="fas fa-plus"></i> Conduct New Exam</a>
        </div>

        <!-- Stats Overview Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class="fas fa-file-signature"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_quizzes; ?></h3>
                    <span>Total Exams Created</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-orange"><i class="fas fa-clipboard-question"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_questions; ?></h3>
                    <span>Total Questions</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fas fa-user-check"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_assigned_students; ?></h3>
                    <span>Assigned Students</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-purple"><i class="fas fa-chart-pie"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_submissions; ?></h3>
                    <span>Exam Submissions</span>
                </div>
            </div>
        </div>

        <!-- Recent Examinations Table -->
        <div class="section-card">
            <div class="section-header">
                <h2><i class="fas fa-clock"></i> Recently Created Examinations</h2>
                <a href="display_quize.php">View All Exams &#8594;</a>
            </div>

            <?php if ($recent_quizzes && $recent_quizzes->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject / Exam Title</th>
                            <th>Questions</th>
                            <th>Assigned</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($q = $recent_quizzes->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($q['title_name']); ?></strong></td>
                                <td><?php echo $q['q_count']; ?> Questions</td>
                                <td><?php echo $q['s_count']; ?> Students</td>
                                <td><span style="color: #27ae60; font-weight: bold;"><?php echo $q['sub_count']; ?> Done</span></td>
                                <td>
                                    <a href="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($q['title_name']); ?>" class="btn-sm btn-manage"><i class="fas fa-edit"></i> Edit Questions</a>
                                    <a href="student_score.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($q['title_name']); ?>" class="btn-sm btn-view"><i class="fas fa-chart-bar"></i> Results</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #718096; text-align: center; padding: 25px 0;">No exams created yet. Click "Conduct New Exam" to create your first examination.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
