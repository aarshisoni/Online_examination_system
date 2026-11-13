<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include 'connect.php';
$teacherid = $_SESSION['teacherid'];
$alert_msg = "";

// Handle title deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_title'])) {
    $title_to_delete = trim($_POST['delete_title']);
    
    // Delete from quiz_title
    $stmt1 = $con->prepare("DELETE FROM quiz_title WHERE teacherid = ? AND title_name = ?");
    $stmt1->bind_param("ss", $teacherid, $title_to_delete);
    $stmt1->execute();

    // Cascade delete questions
    $stmt2 = $con->prepare("DELETE FROM quiz_questions WHERE teacherid = ? AND title_name = ?");
    $stmt2->bind_param("ss", $teacherid, $title_to_delete);
    $stmt2->execute();

    // Cascade delete assigned students
    $stmt3 = $con->prepare("DELETE FROM selected_student WHERE teacherid = ? AND title_name = ?");
    $stmt3->bind_param("ss", $teacherid, $title_to_delete);
    $stmt3->execute();

    // Cascade delete submitted scores
    $stmt4 = $con->prepare("DELETE FROM submit_score WHERE teacherid = ? AND title_name = ?");
    $stmt4->bind_param("ss", $teacherid, $title_to_delete);
    $stmt4->execute();

    $alert_msg = "Quiz '$title_to_delete' and all associated data deleted successfully.";
}

// Fetch quizzes with question count and assigned student count
$query = "SELECT qt.title_name,
                 (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.teacherid = qt.teacherid AND qq.title_name = qt.title_name) AS question_count,
                 (SELECT COUNT(*) FROM selected_student ss WHERE ss.teacherid = qt.teacherid AND ss.title_name = qt.title_name) AS student_count,
                 (SELECT COUNT(*) FROM submit_score sc WHERE sc.teacherid = qt.teacherid AND sc.title_name = qt.title_name) AS submission_count
          FROM quiz_title qt
          WHERE qt.teacherid = ?
          ORDER BY qt.id DESC";

$stmt = $con->prepare($query);
$stmt->bind_param("s", $teacherid);
$stmt->execute();
$result_titles = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Examinations & Quizzes</title>
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
            transition: all 0.2s;
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
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .top-bar h3 {
            margin: 0;
            font-size: 20px;
            color: #2c3e50;
        }
        .btn-create {
            background: #27ae60;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-create:hover {
            background: #219150;
        }
        .alert-box {
            padding: 12px 20px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            text-align: left;
            padding: 14px 16px;
            border-bottom: 1px solid #eef2f5;
            font-size: 14px;
        }
        th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        tr:hover {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-blue { background: #e3f2fd; color: #1565c0; }
        .badge-green { background: #e8f5e9; color: #2e7d32; }
        .badge-purple { background: #f3e5f5; color: #7b1fa2; }
        .actions-cell {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .action-btn {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-questions { background: #3498db; color: white; }
        .btn-questions:hover { background: #2980b9; }
        .btn-assign { background: #f39c12; color: white; }
        .btn-assign:hover { background: #d68910; }
        .btn-results { background: #8e44ad; color: white; }
        .btn-results:hover { background: #71368a; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-delete:hover { background: #c0392b; }
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #7f8c8d;
        }
        .empty-state i {
            font-size: 48px;
            color: #bdc3c7;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-graduation-cap"></i> Examination Portal - Faculty Panel</h2>
    <div class="header-nav">
        <a href="admin_dashboard.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="admin_logout.php" class="nav-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</header>

<div class="container">
    <div class="top-bar">
        <h3><i class="fas fa-book-open"></i> My Courses & Scheduled Examinations</h3>
        <a href="new_title.php" class="btn-create"><i class="fas fa-plus"></i> Create New Exam / Subject</a>
    </div>

    <?php if (!empty($alert_msg)): ?>
        <div class="alert-box"><?php echo htmlspecialchars($alert_msg); ?></div>
    <?php endif; ?>

    <?php if ($result_titles && $result_titles->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th width="25%">Subject / Exam Title</th>
                    <th width="12%">Questions</th>
                    <th width="15%">Assigned Students</th>
                    <th width="13%">Submissions</th>
                    <th width="35%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_titles->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($row['title_name']); ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-blue">
                                <i class="fas fa-question-circle"></i> <?php echo $row['question_count']; ?> Question(s)
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-green">
                                <i class="fas fa-users"></i> <?php echo $row['student_count']; ?> Student(s)
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-purple">
                                <i class="fas fa-clipboard-check"></i> <?php echo $row['submission_count']; ?> Done
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($row['title_name']); ?>" class="action-btn btn-questions" title="Manage Questions">
                                    <i class="fas fa-edit"></i> Questions
                                </a>
                                <a href="student_list_to_select.php?title_name=<?php echo urlencode($row['title_name']); ?>" class="action-btn btn-assign" title="Assign Students">
                                    <i class="fas fa-user-plus"></i> Assign
                                </a>
                                <a href="student_score.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($row['title_name']); ?>" class="action-btn btn-results" title="View Results">
                                    <i class="fas fa-chart-bar"></i> Results
                                </a>
                                <form method="post" style="margin: 0;" onsubmit="return confirm('Are you sure you want to permanently delete this exam and all its questions, student assignments, and scores?');">
                                    <input type="hidden" name="delete_title" value="<?php echo htmlspecialchars($row['title_name']); ?>">
                                    <button type="submit" class="action-btn btn-delete" title="Delete Exam">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>You have not created any examinations or quizzes yet.</p>
            <a href="new_title.php" class="btn-create" style="display: inline-block; margin-top: 10px;">
                <i class="fas fa-plus"></i> Create Your First Exam
            </a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
