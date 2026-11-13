<?php
session_start();

// Check if teacher is logged in
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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_question'])) {
        $delete_question_id = intval($_POST['delete_question']);
        $stmt_del = $con->prepare("DELETE FROM quiz_questions WHERE id = ? AND teacherid = ? AND title_name = ?");
        $stmt_del->bind_param("iss", $delete_question_id, $teacherid, $title_name);
        if ($stmt_del->execute()) {
            $alert_msg = "Question deleted successfully.";
        } else {
            $alert_msg = "Error deleting question: " . $con->error;
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
        } else {
            $alert_msg = "Error adding question: " . $con->error;
        }
    }
}

// Fetch all questions for this quiz
$stmt_fetch = $con->prepare("SELECT * FROM quiz_questions WHERE teacherid = ? AND title_name = ? ORDER BY question_number ASC");
$stmt_fetch->bind_param("ss", $teacherid, $title_name);
$stmt_fetch->execute();
$result_questions = $stmt_fetch->get_result();

// Determine next suggested question number
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
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(235, 210, 210);
            margin: 0;
            padding: 0;
        }
        header {
            background-color: rgb(184, 149, 149);
            color: white;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header h2 {
            margin: 0;
            font-size: 24px;
        }
        .header-nav a {
            color: white;
            text-decoration: none;
            background: #345e70;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 15px;
        }
        .header-nav a:hover {
            background: #233e4a;
        }
        .main-wrapper {
            display: flex;
            padding: 30px 40px;
            gap: 30px;
            align-items: flex-start;
        }
        .sidebar-actions {
            width: 240px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .action-link {
            display: block;
            background-color: #037808;
            color: white;
            padding: 12px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            transition: 0.2s;
        }
        .action-link:hover {
            background-color: #229027;
        }
        .action-link.secondary {
            background-color: #008CBA;
        }
        .action-link.secondary:hover {
            background-color: #005f6b;
        }
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .card {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .full-width {
            grid-column: span 2;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
            font-size: 14px;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 15px;
        }
        .btn-submit {
            background-color: #037808;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: #229027;
        }
        .question-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .question-item {
            background-color: #fdfdfd;
            border: 1px solid #e2e2e2;
            padding: 20px;
            border-radius: 6px;
        }
        .q-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .q-title {
            font-size: 17px;
            font-weight: bold;
            color: #222;
        }
        .q-marks {
            background: #eef2f5;
            color: #333;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }
        .q-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 12px 0;
        }
        .opt {
            padding: 8px 12px;
            background: #f5f5f5;
            border-radius: 4px;
            font-size: 14px;
        }
        .opt.correct {
            background: #d4edda;
            color: #155724;
            font-weight: bold;
            border: 1px solid #c3e6cb;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-delete:hover {
            background-color: #bd2130;
        }
        .alert-box {
            padding: 12px 20px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 6px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-clipboard-question"></i> Quiz: <?php echo htmlspecialchars($title_name); ?></h2>
    <div class="header-nav">
        <a href="admin_dashboard.php"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>
</header>

<div class="main-wrapper">
    <div class="sidebar-actions">
        <a href="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>&teacherid=<?php echo urlencode($teacherid); ?>" class="action-link">
            <i class="fas fa-user-plus"></i> Assign Students
        </a>
        <a href="selected_student_list.php?title_name=<?php echo urlencode($title_name); ?>&teacherid=<?php echo urlencode($teacherid); ?>" class="action-link secondary">
            <i class="fas fa-users"></i> Assigned Students
        </a>
        <a href="generate_quiz.php?title_name=<?php echo urlencode($title_name); ?>&teacherid=<?php echo urlencode($teacherid); ?>" class="action-link" target="_blank">
            <i class="fas fa-eye"></i> Preview Quiz
        </a>
        <a href="display_quize.php" class="action-link secondary">
            <i class="fas fa-list"></i> All Quizzes
        </a>
    </div>

    <div class="content-area">
        <?php if (!empty($alert_msg)): ?>
            <div class="alert-box"><?php echo htmlspecialchars($alert_msg); ?></div>
        <?php endif; ?>

        <!-- Add Question Card -->
        <div class="card">
            <h3><i class="fas fa-plus-circle"></i> Add New Question</h3>
            <form method="post" action="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>">
                <input type="hidden" name="add_question" value="1">
                <div class="form-grid">
                    <div>
                        <label for="question_number">Question Number:</label>
                        <input type="number" id="question_number" name="question_number" value="<?php echo $next_q_num; ?>" required>
                    </div>
                    <div>
                        <label for="marks">Marks:</label>
                        <input type="number" id="marks" name="marks" value="1" min="1" required>
                    </div>
                    <div class="full-width">
                        <label for="question">Question Text:</label>
                        <input type="text" id="question" name="question" placeholder="Enter question description" required>
                    </div>
                    <div>
                        <label for="option1">Option 1:</label>
                        <input type="text" id="option1" name="option1" placeholder="First option" required>
                    </div>
                    <div>
                        <label for="option2">Option 2:</label>
                        <input type="text" id="option2" name="option2" placeholder="Second option" required>
                    </div>
                    <div>
                        <label for="option3">Option 3:</label>
                        <input type="text" id="option3" name="option3" placeholder="Third option" required>
                    </div>
                    <div>
                        <label for="option4">Option 4:</label>
                        <input type="text" id="option4" name="option4" placeholder="Fourth option" required>
                    </div>
                    <div class="full-width">
                        <label for="correct_option">Correct Option:</label>
                        <select id="correct_option" name="correct_option" required>
                            <option value="">Select the correct option</option>
                            <option value="option1">Option 1</option>
                            <option value="option2">Option 2</option>
                            <option value="option3">Option 3</option>
                            <option value="option4">Option 4</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Add Question</button>
            </form>
        </div>

        <!-- Existing Questions Card -->
        <div class="card">
            <h3><i class="fas fa-list-check"></i> Questions in this Quiz (<?php echo $result_questions->num_rows; ?>)</h3>
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
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                <input type="hidden" name="delete_question" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn-delete"><i class="fas fa-trash"></i> Delete Question</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p style="color: #666; font-style: italic;">No questions added yet. Use the form above to add questions to this quiz.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
