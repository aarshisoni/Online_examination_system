<?php
session_start();

if (!isset($_SESSION['studentid'])) {
    header("Location: student_login.php");
    exit();
}

include "connect.php";
$studentid = $_SESSION['studentid'];

$title_name = isset($_REQUEST['title_name']) ? trim($_REQUEST['title_name']) : '';
$teacherid = isset($_REQUEST['teacherid']) ? trim($_REQUEST['teacherid']) : '';

if (empty($title_name) || empty($teacherid)) {
    header("Location: student_quiz_dashboard.php");
    exit();
}

// Fetch questions
$sql_fetch = "SELECT * FROM quiz_questions WHERE teacherid = ? AND title_name = ? ORDER BY question_number ASC";
$stmt = $con->prepare($sql_fetch);
$stmt->bind_param("ss", $teacherid, $title_name);
$stmt->execute();
$result_questions = $stmt->get_result();

$total_questions = $result_questions->num_rows;
$total_marks = 0;
$obtained_marks = 0;
$correct_count = 0;
$breakdown = [];

while ($row = $result_questions->fetch_assoc()) {
    $q_id = $row['id'];
    $q_marks = intval($row['marks']);
    $total_marks += $q_marks;

    $selected_key = 'question_' . $q_id;
    $selected_val = isset($_POST[$selected_key]) ? trim($_POST[$selected_key]) : '';
    $correct_val = trim($row['correct_option']);

    $is_correct = (!empty($selected_val) && $selected_val === $correct_val);
    if ($is_correct) {
        $obtained_marks += $q_marks;
        $correct_count++;
    }

    $breakdown[] = [
        'number' => $row['question_number'],
        'question' => $row['question'],
        'selected_opt' => $selected_val,
        'correct_opt' => $correct_val,
        'selected_text' => !empty($selected_val) && isset($row[$selected_val]) ? $row[$selected_val] : 'No answer selected',
        'correct_text' => isset($row[$correct_val]) ? $row[$correct_val] : '',
        'is_correct' => $is_correct,
        'marks' => $q_marks
    ];
}

// Check if already in submit_score
$check = $con->prepare("SELECT sid, marks FROM submit_score WHERE studentid = ? AND teacherid = ? AND title_name = ?");
$check->bind_param("sss", $studentid, $teacherid, $title_name);
$check->execute();
$check_res = $check->get_result();

if ($check_res->num_rows == 0) {
    // Insert new score
    $ins = $con->prepare("INSERT INTO submit_score (studentid, teacherid, title_name, marks) VALUES (?, ?, ?, ?)");
    $ins->bind_param("sssi", $studentid, $teacherid, $title_name, $obtained_marks);
    $ins->execute();
} else {
    // Already submitted earlier, show recorded marks
    $existing = $check_res->fetch_assoc();
    $obtained_marks = $existing['marks'];
}

$percentage = ($total_marks > 0) ? round(($obtained_marks / $total_marks) * 100, 1) : 0;
$passed = ($percentage >= 40);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Result - <?php echo htmlspecialchars($title_name); ?></title>
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
            max-width: 850px;
            margin: 35px auto 60px;
            padding: 0 20px;
        }
        .scorecard {
            background: white;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
            margin-bottom: 30px;
        }
        .score-circle {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: <?php echo $passed ? 'linear-gradient(135deg, #11998e, #38ef7d)' : 'linear-gradient(135deg, #eb3349, #f45c43)'; ?>;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .score-circle h1 {
            margin: 0;
            font-size: 34px;
            font-weight: 800;
        }
        .score-circle span {
            font-size: 13px;
            opacity: 0.9;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 15px;
            background: <?php echo $passed ? '#d4edda' : '#f8d7da'; ?>;
            color: <?php echo $passed ? '#155724' : '#721c24'; ?>;
        }
        .scorecard h2 {
            margin: 0 0 10px;
            color: #2c3e50;
        }
        .scorecard p {
            color: #636e72;
            margin-bottom: 25px;
            font-size: 15px;
        }
        .actions-bar {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .btn-act {
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .btn-primary {
            background: #11998e;
            color: white;
        }
        .btn-primary:hover {
            background: #0e8076;
        }
        .btn-secondary {
            background: #edf2f7;
            color: #4a5568;
        }
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        .review-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        .review-card h3 {
            margin-top: 0;
            margin-bottom: 25px;
            color: #2c3e50;
            border-bottom: 2px solid #edf2f7;
            padding-bottom: 12px;
        }
        .review-item {
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 18px;
            border: 1px solid #eef2f5;
        }
        .item-correct {
            border-left: 5px solid #27ae60;
            background: #fbfdfc;
        }
        .item-incorrect {
            border-left: 5px solid #e74c3c;
            background: #fdfcfc;
        }
        .review-q-header {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            margin-bottom: 8px;
            color: #2d3748;
        }
        .review-ans {
            font-size: 14px;
            margin: 6px 0;
        }
        .ans-your {
            color: #4a5568;
        }
        .ans-correct {
            color: #27ae60;
            font-weight: 600;
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-award"></i> Examination Scorecard</h2>
    <a href="student_dashboard.php" style="color: white; text-decoration: none; background: rgba(0,0,0,0.15); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
        <i class="fas fa-home"></i> Dashboard
    </a>
</header>

<div class="container">
    <div class="scorecard">
        <div class="score-circle">
            <h1><?php echo $obtained_marks; ?></h1>
            <span>out of <?php echo $total_marks; ?></span>
        </div>
        <div class="status-badge">
            <i class="fas <?php echo $passed ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo $passed ? 'Passed Examination' : 'Needs Improvement'; ?> (<?php echo $percentage; ?>%)
        </div>
        <h2><?php echo htmlspecialchars($title_name); ?></h2>
        <p>You answered <strong><?php echo $correct_count; ?></strong> out of <strong><?php echo $total_questions; ?></strong> questions correctly.</p>
        
        <div class="actions-bar">
            <a href="student_dashboard.php" class="btn-act btn-primary"><i class="fas fa-home"></i> Back to Dashboard</a>
            <a href="student_score_dashboard.php" class="btn-act btn-secondary"><i class="fas fa-list"></i> View All Results</a>
        </div>
    </div>

    <!-- Question by Question Review -->
    <div class="review-card">
        <h3><i class="fas fa-tasks"></i> Question Review & Explanations</h3>
        <?php foreach ($breakdown as $item): ?>
            <div class="review-item <?php echo $item['is_correct'] ? 'item-correct' : 'item-incorrect'; ?>">
                <div class="review-q-header">
                    <span>Question <?php echo $item['number']; ?>: <?php echo htmlspecialchars($item['question']); ?></span>
                    <span style="font-size: 13px; color: <?php echo $item['is_correct'] ? '#27ae60' : '#e74c3c'; ?>;">
                        <?php echo $item['is_correct'] ? "+{$item['marks']} Mark(s)" : "0 Marks"; ?>
                    </span>
                </div>
                <div class="review-ans ans-your">
                    <strong>Your Answer:</strong> 
                    <span style="color: <?php echo $item['is_correct'] ? '#27ae60' : '#e74c3c'; ?>;">
                        <?php echo htmlspecialchars($item['selected_text']); ?>
                    </span>
                </div>
                <?php if (!$item['is_correct']): ?>
                    <div class="review-ans ans-correct">
                        <strong>Correct Answer:</strong> <?php echo htmlspecialchars($item['correct_text']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
