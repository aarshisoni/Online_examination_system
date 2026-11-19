<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include 'connect.php';

$teacherid = $_SESSION['teacherid'];
$title_name = isset($_GET['title_name']) ? trim($_GET['title_name']) : '';

if (empty($title_name)) {
    header("Location: display_quize.php");
    exit();
}

$alert_msg = "";
$alert_type = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_question'])) {
        $delete_question_id = intval($_POST['delete_question']);
        $stmt_del = $con->prepare("DELETE FROM quiz_questions WHERE id = ? AND teacherid = ? AND title_name = ?");
        $stmt_del->bind_param("iss", $delete_question_id, $teacherid, $title_name);
        if ($stmt_del->execute()) {
            $alert_msg = "Question deleted successfully.";
            $alert_type = "success";
        } else {
            $alert_msg = "Error deleting question: " . $con->error;
            $alert_type = "danger";
        }
    } elseif (isset($_POST['add_question'])) {
        $question_number = intval($_POST['question_number']);
        $question = trim($_POST['question']);
        $option1 = trim($_POST['option1']);
        $option2 = trim($_POST['option2']);
        $option3 = trim($_POST['option3']);
        $option4 = trim($_POST['option4']);
        $correct_option = trim($_POST['correct_option']);
        $marks = intval($_POST['marks']);

        $stmt_ins = $con->prepare("INSERT INTO quiz_questions (teacherid, title_name, question_number, question, option1, option2, option3, option4, correct_option, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_ins->bind_param("ssissssssi", $teacherid, $title_name, $question_number, $question, $option1, $option2, $option3, $option4, $correct_option, $marks);

        if ($stmt_ins->execute()) {
            $alert_msg = "Question added successfully!";
            $alert_type = "success";
        } else {
            $alert_msg = "Error adding question: " . $con->error;
            $alert_type = "danger";
        }
    }
}

// Fetch all questions for this quiz
$stmt_fetch = $con->prepare("SELECT * FROM quiz_questions WHERE teacherid = ? AND title_name = ? ORDER BY question_number ASC");
$stmt_fetch->bind_param("ss", $teacherid, $title_name);
$stmt_fetch->execute();
$result_questions = $stmt_fetch->get_result();

$next_q_num = $result_questions->num_rows + 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions - <?php echo htmlspecialchars($title_name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #1e3c72;
            --primary-dark: #14284d;
            --accent: #11998e;
            --text-dark: #2c3e50;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #e6f0fa 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--text-dark);
        }

        /* Navbar */
        .navbar {
            height: 75px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--primary);
            font-size: 22px;
            font-weight: 800;
        }

        .logo i {
            font-size: 26px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-btn {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .nav-btn:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .main-wrapper {
            display: flex;
            padding: 35px 7%;
            gap: 30px;
            align-items: flex-start;
        }

        /* Sidebar Navigation Actions */
        .sidebar-actions {
            width: 250px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action-card {
            background: var(--white);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            border: 1px solid #edf2f7;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-card h4 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .action-link {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #f8fafc;
            color: var(--text-dark);
            padding: 12px 16px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }

        .action-link:hover {
            background-color: var(--primary);
            color: var(--white);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .action-link.highlight {
            background: linear-gradient(135deg, var(--accent) 0%, #38ef7d 100%);
            color: var(--white);
            border: none;
        }

        .action-link.highlight:hover {
            opacity: 0.95;
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
        }

        /* Content Area */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .card {
            background-color: var(--white);
            padding: 30px;
            border-radius: 14px;
            box-shadow: var(--card-shadow);
            border: 1px solid #edf2f7;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-header h3 {
            margin: 0;
            font-size: 20px;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #334155;
            font-size: 13px;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: var(--transition);
            background: #ffffff;
        }

        input[type="text"]:focus, input[type="number"]:focus, select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, #2a5298 100%);
            color: var(--white);
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(30, 60, 114, 0.25);
            transition: var(--transition);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(30, 60, 114, 0.35);
        }

        .question-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .question-item {
            background-color: #ffffff;
            border: 1.5px solid #edf2f7;
            padding: 22px;
            border-radius: 10px;
            transition: var(--transition);
        }

        .question-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        }

        .q-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .q-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
        }

        .q-marks {
            background: #eef2f5;
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .q-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 14px 0;
        }

        .opt {
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: 6px;
            font-size: 14px;
            border: 1px solid #f1f5f9;
        }

        .opt.correct {
            background: #d4edda;
            color: #155724;
            font-weight: 700;
            border-color: #c3e6cb;
        }

        .btn-delete {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 7px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .btn-delete:hover {
            background-color: #dc2626;
            color: #ffffff;
        }

        .alert-box {
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .alert-box.success { background-color: #d4edda; color: #155724; }
        .alert-box.danger { background-color: #f8d7da; color: #721c24; }

        @media (max-width: 900px) {
            .main-wrapper { flex-direction: column; }
            .sidebar-actions { width: 100%; }
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar">
        <a href="admin_dashboard.php" class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>ExamPortal</span>
        </a>
        <div class="nav-actions">
            <span style="font-size: 14px; color: var(--text-muted); font-weight: 600; margin-right: 10px;">
                Exam: <strong style="color: var(--primary);"><?php echo htmlspecialchars($title_name); ?></strong>
            </span>
            <a href="admin_dashboard.php" class="nav-btn">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="display_quize.php" class="nav-btn">
                <i class="fas fa-book-open"></i> All Quizzes
            </a>
        </div>
    </nav>

    <div class="main-wrapper">
        <!-- Sidebar Actions -->
        <div class="sidebar-actions">
            <div class="action-card">
                <h4>Quiz Workflows</h4>
                <a href="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>&teacherid=<?php echo urlencode($teacherid); ?>" class="action-link highlight">
                    <i class="fas fa-user-plus"></i> Assign Students
                </a>
                <a href="selected_student_list.php?title_name=<?php echo urlencode($title_name); ?>&teacherid=<?php echo urlencode($teacherid); ?>" class="action-link">
                    <i class="fas fa-users"></i> Assigned List
                </a>
                <a href="generate_quiz.php?title_name=<?php echo urlencode($title_name); ?>&teacherid=<?php echo urlencode($teacherid); ?>" class="action-link" target="_blank">
                    <i class="fas fa-eye"></i> Preview Exam
                </a>
                <a href="student_score.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>" class="action-link">
                    <i class="fas fa-chart-bar"></i> View Results
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <?php if (!empty($alert_msg)): ?>
                <div class="alert-box <?php echo $alert_type; ?>"><?php echo htmlspecialchars($alert_msg); ?></div>
            <?php endif; ?>

            <!-- Add Question Card -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-plus-circle" style="color: var(--accent);"></i> Add New Question</h3>
                    <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Question #<?php echo $next_q_num; ?></span>
                </div>

                <form method="post" action="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>">
                    <input type="hidden" name="add_question" value="1">
                    <div class="form-grid">
                        <div>
                            <label for="question_number">Question Number</label>
                            <input type="number" id="question_number" name="question_number" value="<?php echo $next_q_num; ?>" required>
                        </div>
                        <div>
                            <label for="marks">Marks Weightage</label>
                            <input type="number" id="marks" name="marks" value="2" min="1" required>
                        </div>
                        <div class="full-width">
                            <label for="question">Question Text</label>
                            <input type="text" id="question" name="question" placeholder="Enter question description..." required>
                        </div>
                        <div>
                            <label for="option1">Option A</label>
                            <input type="text" id="option1" name="option1" placeholder="First option" required>
                        </div>
                        <div>
                            <label for="option2">Option B</label>
                            <input type="text" id="option2" name="option2" placeholder="Second option" required>
                        </div>
                        <div>
                            <label for="option3">Option C</label>
                            <input type="text" id="option3" name="option3" placeholder="Third option" required>
                        </div>
                        <div>
                            <label for="option4">Option D</label>
                            <input type="text" id="option4" name="option4" placeholder="Fourth option" required>
                        </div>
                        <div class="full-width">
                            <label for="correct_option">Select the Correct Option</label>
                            <select id="correct_option" name="correct_option" required>
                                <option value="">-- Choose Correct Answer --</option>
                                <option value="option1">Option A</option>
                                <option value="option2">Option B</option>
                                <option value="option3">Option C</option>
                                <option value="option4">Option D</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check"></i> Save Question
                    </button>
                </form>
            </div>

            <!-- Existing Questions Card -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list-check" style="color: var(--primary);"></i> Current Question Bank</h3>
                    <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Total: <?php echo $result_questions->num_rows; ?> Questions</span>
                </div>

                <?php if ($result_questions->num_rows > 0): ?>
                    <ul class="question-list">
                        <?php while ($row = $result_questions->fetch_assoc()): ?>
                            <li class="question-item">
                                <div class="q-header">
                                    <span class="q-title">Q<?php echo $row['question_number']; ?>: <?php echo htmlspecialchars($row['question']); ?></span>
                                    <span class="q-marks"><?php echo $row['marks']; ?> Mark(s)</span>
                                </div>
                                <div class="q-options">
                                    <div class="opt <?php echo ($row['correct_option'] === 'option1') ? 'correct' : ''; ?>">A: <?php echo htmlspecialchars($row['option1']); ?></div>
                                    <div class="opt <?php echo ($row['correct_option'] === 'option2') ? 'correct' : ''; ?>">B: <?php echo htmlspecialchars($row['option2']); ?></div>
                                    <div class="opt <?php echo ($row['correct_option'] === 'option3') ? 'correct' : ''; ?>">C: <?php echo htmlspecialchars($row['option3']); ?></div>
                                    <div class="opt <?php echo ($row['correct_option'] === 'option4') ? 'correct' : ''; ?>">D: <?php echo htmlspecialchars($row['option4']); ?></div>
                                </div>
                                <form method="post" onsubmit="return confirm('Delete this question?');">
                                    <input type="hidden" name="delete_question" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn-delete"><i class="fas fa-trash"></i> Delete Question</button>
                                </form>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: #64748b; text-align: center; padding: 30px; font-style: italic;">No questions added yet. Use the form above to add your first question.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
