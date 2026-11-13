<?php
session_start();

if (!isset($_SESSION['studentid'])) {
    header("Location: student_login.php");
    exit();
}

include "connect.php";
$studentid = $_SESSION['studentid'];

// Fetch all exam submissions for this student
$query = "SELECT ss.sid, ss.title_name, ss.teacherid, ss.marks,
                 ar.fname AS teacher_name, ar.branch AS teacher_branch,
                 (SELECT SUM(marks) FROM quiz_questions qq WHERE qq.teacherid = ss.teacherid AND qq.title_name = ss.title_name) AS max_marks,
                 (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.teacherid = ss.teacherid AND qq.title_name = ss.title_name) AS total_questions
          FROM submit_score ss
          LEFT JOIN admin_register ar ON ss.teacherid = ar.teacherid
          WHERE ss.studentid = ?
          ORDER BY ss.sid DESC";

$stmt = $con->prepare($query);
$stmt->bind_param("s", $studentid);
$stmt->execute();
$result = $stmt->get_result();

$total_completed = $result->num_rows;
$sum_percentage = 0;
$scores = [];

while ($row = $result->fetch_assoc()) {
    $max = ($row['max_marks'] > 0) ? $row['max_marks'] : $row['marks'];
    $pct = ($max > 0) ? round(($row['marks'] / $max) * 100, 1) : 0;
    $sum_percentage += $pct;
    $row['percentage'] = $pct;
    $row['max_marks_calc'] = $max;
    $scores[] = $row;
}

$avg_percentage = ($total_completed > 0) ? round($sum_percentage / $total_completed, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Examination Scorecard & History</title>
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
            margin: 35px auto 60px;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #eef2f5;
            display: flex;
            align-items: center;
            gap: 18px;
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
        .icon-green { background: #e8f5e9; color: #27ae60; }
        .icon-blue { background: #e3f2fd; color: #1976d2; }
        .stat-info h3 {
            margin: 0 0 3px;
            font-size: 24px;
            color: #2c3e50;
        }
        .stat-info span {
            color: #7f8c8d;
            font-size: 13px;
            font-weight: 600;
        }
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 25px 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .table-card h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 19px;
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
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
        }
        .badge-pass { background: #d4edda; color: #155724; }
        .badge-fail { background: #f8d7da; color: #721c24; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-trophy"></i> My Scorecard & Exam History</h2>
    <div class="header-nav">
        <a href="student_dashboard.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="student_quiz_dashboard.php" class="nav-btn"><i class="fas fa-play"></i> Live Exams</a>
        <a href="student_logout.php" class="nav-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</header>

<div class="container">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3><?php echo $total_completed; ?></h3>
                <span>Exams Completed</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <h3><?php echo $avg_percentage; ?>%</h3>
                <span>Average Performance</span>
            </div>
        </div>
    </div>

    <div class="table-card">
        <h3><i class="fas fa-history"></i> Completed Examination Records</h3>
        <?php if (!empty($scores)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Subject / Exam Title</th>
                        <th>Faculty Name</th>
                        <th>Score Obtained</th>
                        <th>Total Marks</th>
                        <th>Percentage</th>
                        <th>Result Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($scores as $s): ?>
                        <?php $isPass = ($s['percentage'] >= 40); ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($s['title_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($s['teacher_name'] ? $s['teacher_name'] : $s['teacherid']); ?></td>
                            <td><strong style="font-size: 15px; color: #2c3e50;"><?php echo $s['marks']; ?></strong></td>
                            <td><?php echo $s['max_marks_calc']; ?></td>
                            <td><strong><?php echo $s['percentage']; ?>%</strong></td>
                            <td>
                                <span class="badge <?php echo $isPass ? 'badge-pass' : 'badge-fail'; ?>">
                                    <?php echo $isPass ? 'Passed' : 'Needs Improvement'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-clipboard" style="font-size: 40px; color: #cbd5e0; margin-bottom: 10px;"></i>
                <p>You have not completed any examinations yet. Check <a href="student_quiz_dashboard.php">Live Exams</a> to attempt tests.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
