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

// Fetch questions related to this teacher and title
$stmt = $con->prepare("SELECT * FROM quiz_questions WHERE teacherid = ? AND title_name = ? ORDER BY question_number ASC");
$stmt->bind_param("ss", $teacherid, $title_name);
$stmt->execute();
$result_questions = $stmt->get_result();

$totalMarks = 0;
$totalQuestions = $result_questions->num_rows;
$questions = [];

while ($row = $result_questions->fetch_assoc()) {
    $questions[] = $row;
    $totalMarks += intval($row["marks"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: <?php echo htmlspecialchars($title_name); ?></title>
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
            padding: 15px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        header h2 { margin: 0; font-size: 20px; }
        .preview-badge {
            background: #f39c12;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .container {
            max-width: 850px;
            margin: 30px auto 60px;
            padding: 0 20px;
        }
        .info-card {
            background: white;
            padding: 20px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .q-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #eef2f5;
        }
        .q-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-weight: 700;
            color: #1e3c72;
        }
        .q-text {
            font-size: 16px;
            margin-bottom: 15px;
            color: #2c3e50;
            font-weight: 600;
        }
        .options-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .opt-item {
            padding: 10px 15px;
            border-radius: 6px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            font-size: 14px;
        }
        .opt-correct {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            font-weight: 700;
        }
    </style>
</head>
<body>
<header>
    <div style="display: flex; align-items: center; gap: 12px;">
        <h2><i class="fas fa-eye"></i> Examination Preview: <?php echo htmlspecialchars($title_name); ?></h2>
        <span class="preview-badge">Faculty Preview Mode</span>
    </div>
    <a href="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
        <i class="fas fa-arrow-left"></i> Exit Preview
    </a>
</header>

<div class="container">
    <div class="info-card">
        <div>
            <h3 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($title_name); ?></h3>
            <span style="color: #718096; font-size: 14px;">Total Questions: <strong><?php echo $totalQuestions; ?></strong> | Total Marks: <strong><?php echo $totalMarks; ?></strong></span>
        </div>
        <a href="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>" style="background: #27ae60; color: white; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; font-size: 14px;">
            <i class="fas fa-user-plus"></i> Assign to Students
        </a>
    </div>

    <?php if ($totalQuestions > 0): ?>
        <?php foreach ($questions as $q): ?>
            <div class="q-card">
                <div class="q-header">
                    <span>Question <?php echo $q['question_number']; ?></span>
                    <span style="background: #eef2f5; color: #4a5568; padding: 2px 10px; border-radius: 12px; font-size: 12px;"><?php echo $q['marks']; ?> Mark(s)</span>
                </div>
                <div class="q-text"><?php echo htmlspecialchars($q['question']); ?></div>
                <div class="options-list">
                    <div class="opt-item <?php echo ($q['correct_option'] === 'option1') ? 'opt-correct' : ''; ?>">
                        <strong>A.</strong> <?php echo htmlspecialchars($q['option1']); ?> <?php echo ($q['correct_option'] === 'option1') ? '&#10004; (Correct Answer)' : ''; ?>
                    </div>
                    <div class="opt-item <?php echo ($q['correct_option'] === 'option2') ? 'opt-correct' : ''; ?>">
                        <strong>B.</strong> <?php echo htmlspecialchars($q['option2']); ?> <?php echo ($q['correct_option'] === 'option2') ? '&#10004; (Correct Answer)' : ''; ?>
                    </div>
                    <div class="opt-item <?php echo ($q['correct_option'] === 'option3') ? 'opt-correct' : ''; ?>">
                        <strong>C.</strong> <?php echo htmlspecialchars($q['option3']); ?> <?php echo ($q['correct_option'] === 'option3') ? '&#10004; (Correct Answer)' : ''; ?>
                    </div>
                    <div class="opt-item <?php echo ($q['correct_option'] === 'option4') ? 'opt-correct' : ''; ?>">
                        <strong>D.</strong> <?php echo htmlspecialchars($q['option4']); ?> <?php echo ($q['correct_option'] === 'option4') ? '&#10004; (Correct Answer)' : ''; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; color: #718096; padding: 30px;">No questions added yet. <a href="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>">Add questions now</a>.</p>
    <?php endif; ?>
</div>
</body>
</html>
