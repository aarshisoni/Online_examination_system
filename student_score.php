<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";
$teacherid = $_SESSION['teacherid'];

// Fetch all quizzes conducted by this teacher for the filter dropdown
$title_stmt = $con->prepare("SELECT DISTINCT title_name FROM quiz_title WHERE teacherid = ? ORDER BY title_name ASC");
$title_stmt->bind_param("s", $teacherid);
$title_stmt->execute();
$titles_result = $title_stmt->get_result();

$selected_title = isset($_GET['title_name']) ? trim($_GET['title_name']) : '';

// Build query to fetch submitted scores
if (!empty($selected_title)) {
    $score_query = "SELECT ss.sid, ss.studentid, ss.title_name, ss.marks,
                           r.fname, r.branch, r.year, r.email,
                           (SELECT SUM(marks) FROM quiz_questions qq WHERE qq.teacherid = ss.teacherid AND qq.title_name = ss.title_name) AS max_marks
                    FROM submit_score ss
                    LEFT JOIN register r ON ss.studentid = r.studentid
                    WHERE ss.teacherid = ? AND ss.title_name = ?
                    ORDER BY ss.marks DESC";
    $stmt = $con->prepare($score_query);
    $stmt->bind_param("ss", $teacherid, $selected_title);
} else {
    $score_query = "SELECT ss.sid, ss.studentid, ss.title_name, ss.marks,
                           r.fname, r.branch, r.year, r.email,
                           (SELECT SUM(marks) FROM quiz_questions qq WHERE qq.teacherid = ss.teacherid AND qq.title_name = ss.title_name) AS max_marks
                    FROM submit_score ss
                    LEFT JOIN register r ON ss.studentid = r.studentid
                    WHERE ss.teacherid = ?
                    ORDER BY ss.sid DESC";
    $stmt = $con->prepare($score_query);
    $stmt->bind_param("s", $teacherid);
}

$stmt->execute();
$scores_result = $stmt->get_result();

// Calculate simple statistics
$total_subs = $scores_result->num_rows;
$sum_marks = 0;
$highest_marks = 0;
$scores_data = [];

while ($row = $scores_result->fetch_assoc()) {
    $scores_data[] = $row;
    $sum_marks += $row['marks'];
    if ($row['marks'] > $highest_marks) {
        $highest_marks = $row['marks'];
    }
}
$avg_marks = $total_subs > 0 ? round($sum_marks / $total_subs, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Examination Results</title>
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
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
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
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: 0.2s;
        }
        .nav-btn:hover {
            background: rgba(255,255,255,0.35);
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #edf2f7;
            flex-wrap: wrap;
            gap: 15px;
        }
        .filter-form {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .filter-form select {
            padding: 10px 16px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            color: #333;
        }
        .btn-filter {
            background: #1e3c72;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .stats-strip {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-badge {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px 22px;
            border-radius: 8px;
            flex: 1;
            text-align: center;
        }
        .stat-badge h4 {
            margin: 0;
            font-size: 24px;
            color: #2b6cb0;
        }
        .stat-badge span {
            font-size: 13px;
            color: #718096;
            font-weight: 600;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            text-align: left;
            padding: 14px 16px;
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
        .score-pill {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
        }
        .score-pass {
            background: #d4edda;
            color: #155724;
        }
        .score-fail {
            background: #f8d7da;
            color: #721c24;
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
    <h2><i class="fas fa-chart-line"></i> Student Examination Results</h2>
    <div class="header-nav">
        <a href="admin_dashboard.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="display_quize.php" class="nav-btn"><i class="fas fa-book-open"></i> Quizzes</a>
    </div>
</header>

<div class="container">
    <div class="filter-bar">
        <form method="get" action="student_score.php" class="filter-form">
            <label for="title_name" style="font-weight: 600;"><i class="fas fa-filter"></i> Select Exam / Subject:</label>
            <select name="title_name" id="title_name" onchange="this.form.submit()">
                <option value="">-- All Examinations --</option>
                <?php while ($t = $titles_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($t['title_name']); ?>" <?php echo ($selected_title === $t['title_name']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($t['title_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <!-- Stats strip -->
    <div class="stats-strip">
        <div class="stat-badge">
            <h4><?php echo $total_subs; ?></h4>
            <span>Total Submissions</span>
        </div>
        <div class="stat-badge">
            <h4><?php echo $highest_marks; ?></h4>
            <span>Highest Score</span>
        </div>
        <div class="stat-badge">
            <h4><?php echo $avg_marks; ?></h4>
            <span>Average Score</span>
        </div>
    </div>

    <?php if ($total_subs > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Branch & Year</th>
                    <th>Exam Title</th>
                    <th>Score / Total</th>
                    <th>Percentage</th>
                    <th>Status</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scores_data as $row): ?>
                    <?php 
                        $max = $row['max_marks'] > 0 ? $row['max_marks'] : $row['marks'];
                        $percent = $max > 0 ? round(($row['marks'] / $max) * 100, 1) : 0;
                        $isPass = $percent >= 40;
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['fname'] ? $row['fname'] : 'Student'); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['studentid']); ?></td>
                        <td><?php echo htmlspecialchars($row['branch'] . ' (' . $row['year'] . ')'); ?></td>
                        <td><?php echo htmlspecialchars($row['title_name']); ?></td>
                        <td><strong><?php echo $row['marks']; ?></strong> / <?php echo $max; ?></td>
                        <td><?php echo $percent; ?>%</td>
                        <td>
                            <span class="score-pill <?php echo $isPass ? 'score-pass' : 'score-fail'; ?>">
                                <?php echo $isPass ? 'Passed' : 'Needs Improvement'; ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="view_student_submission.php?studentid=<?php echo urlencode($row['studentid']); ?>&title_name=<?php echo urlencode($row['title_name']); ?>" 
                               class="nav-btn" 
                               style="background: #e0f2fe; color: #0369a1; padding: 6px 12px; font-size: 12px; border-radius: 6px; font-weight: 600;"
                               target="_blank">
                                <i class="fas fa-file-alt"></i> View Paper
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox" style="font-size: 40px; color: #cbd5e0; margin-bottom: 10px;"></i>
            <p>No student exam submissions found for the selected examination.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
