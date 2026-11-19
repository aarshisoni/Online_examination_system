<?php
session_start();

if (!isset($_SESSION['studentid'])) {
    header("Location: student_login.php");
    exit();
}

include "connect.php";
$studentid = $_SESSION['studentid'];

$titleName = isset($_GET['title_name']) ? trim($_GET['title_name']) : '';
$teacherid = isset($_GET['teacherid']) ? trim($_GET['teacherid']) : '';

if (empty($titleName) || empty($teacherid)) {
    header("Location: student_quiz_dashboard.php");
    exit();
}

// Check if already submitted
$check = $con->prepare("SELECT sid FROM submit_score WHERE studentid = ? AND teacherid = ? AND title_name = ?");
$check->bind_param("sss", $studentid, $teacherid, $titleName);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo "<script>alert('You have already submitted this exam.'); window.location.href='student_quiz_dashboard.php';</script>";
    exit();
}

// Fetch questions
$sql_fetch = "SELECT * FROM quiz_questions WHERE teacherid = ? AND title_name = ? ORDER BY question_number ASC";
$stmt = $con->prepare($sql_fetch);
$stmt->bind_param("ss", $teacherid, $titleName);
$stmt->execute();
$result_questions = $stmt->get_result();

if ($result_questions->num_rows == 0) {
    echo "<script>alert('No questions available for this exam.'); window.location.href='student_quiz_dashboard.php';</script>";
    exit();
}

$totalQuestions = $result_questions->num_rows;
$totalMarks = 0;
$questions_data = [];

while ($row = $result_questions->fetch_assoc()) {
    $questions_data[] = $row;
    $totalMarks += intval($row["marks"]);
}

// Fetch student name
$st_stmt = $con->prepare("SELECT fname FROM register WHERE studentid = ?");
$st_stmt->bind_param("s", $studentid);
$st_stmt->execute();
$st_row = $st_stmt->get_result()->fetch_assoc();
$studentName = $st_row ? $st_row['fname'] : "Student";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Exam: <?php echo htmlspecialchars($titleName); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .exam-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 15px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .exam-title-box h2 {
            margin: 0;
            font-size: 20px;
        }
        .exam-title-box span {
            font-size: 13px;
            opacity: 0.9;
        }
        .exam-meta-pills {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .pill {
            background: rgba(0,0,0,0.2);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .container {
            max-width: 900px;
            margin: 30px auto 60px;
            padding: 0 20px;
        }
        .question-card {
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            border: 1px solid #eef2f5;
            margin-bottom: 25px;
        }
        .q-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f3f5;
        }
        .q-number {
            font-size: 16px;
            font-weight: 700;
            color: #11998e;
        }
        .q-marks-tag {
            background: #eef2f5;
            color: #495057;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .q-text {
            font-size: 17px;
            font-weight: 600;
            color: #2c3e50;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .options-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .option-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 15px;
            background: #ffffff;
        }
        .option-label:hover {
            border-color: #11998e;
            background-color: #f0fff4;
        }
        .option-label input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #11998e;
            cursor: pointer;
        }
        .written-box {
            margin-top: 10px;
        }
        .written-textarea {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            line-height: 1.6;
            outline: none;
            transition: all 0.2s;
            background: #ffffff;
            resize: vertical;
            box-sizing: border-box;
        }
        .written-textarea:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.15);
        }
        .submit-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
        .btn-submit-exam {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border: none;
            padding: 14px 35px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
            transition: 0.2s;
        }
        .btn-submit-exam:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(17, 153, 142, 0.4);
        }
    </style>
</head>
<body>

<div class="exam-header">
    <div class="exam-title-box">
        <h2><i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($titleName); ?></h2>
        <span>Examinee: <strong><?php echo htmlspecialchars($studentName); ?></strong> (<?php echo htmlspecialchars($studentid); ?>)</span>
    </div>
    <div class="exam-meta-pills">
        <div class="pill"><i class="fas fa-list-ol"></i> <?php echo $totalQuestions; ?> Questions</div>
        <div class="pill"><i class="fas fa-trophy"></i> Total: <?php echo $totalMarks; ?> Marks</div>
    </div>
</div>

<div class="container">
    <form id="examForm" method="post" action="student_quiz_result.php?title_name=<?php echo urlencode($titleName); ?>&teacherid=<?php echo urlencode($teacherid); ?>" onsubmit="return confirm('Are you sure you want to finish and submit your exam? You cannot modify your answers afterwards.');">
        <input type="hidden" name="title_name" value="<?php echo htmlspecialchars($titleName); ?>">
        <input type="hidden" name="teacherid" value="<?php echo htmlspecialchars($teacherid); ?>">

        <?php $q_counter = 1; ?>
        <?php foreach ($questions_data as $q): ?>
            <?php $isWritten = (isset($q['question_type']) && $q['question_type'] === 'written'); ?>
            <div class="question-card">
                <div class="q-top">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="q-number">Question <?php echo $q_counter; ?> of <?php echo $totalQuestions; ?></span>
                        <span style="background: <?php echo $isWritten ? '#f3e8ff; color: #7e22ce' : '#e0f2fe; color: #0369a1'; ?>; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                            <?php echo $isWritten ? 'Written / Descriptive' : 'MCQ'; ?>
                        </span>
                    </div>
                    <span class="q-marks-tag"><?php echo $q['marks']; ?> Mark(s)</span>
                </div>
                <div class="q-text">
                    <?php echo htmlspecialchars($q['question']); ?>
                </div>

                <?php if ($isWritten): ?>
                    <div class="written-box">
                        <label style="display: block; font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 8px;">
                            <i class="fas fa-pencil-alt" style="color: #11998e;"></i> Type your written answer below:
                        </label>
                        <textarea name="question_<?php echo $q['id']; ?>" class="written-textarea" rows="5" placeholder="Write your complete answer or explanation here..."></textarea>
                    </div>
                <?php else: ?>
                    <div class="options-group">
                        <label class="option-label">
                            <input type="radio" name="question_<?php echo $q['id']; ?>" value="option1">
                            <span><strong>A.</strong> <?php echo htmlspecialchars($q['option1']); ?></span>
                        </label>
                        <label class="option-label">
                            <input type="radio" name="question_<?php echo $q['id']; ?>" value="option2">
                            <span><strong>B.</strong> <?php echo htmlspecialchars($q['option2']); ?></span>
                        </label>
                        <label class="option-label">
                            <input type="radio" name="question_<?php echo $q['id']; ?>" value="option3">
                            <span><strong>C.</strong> <?php echo htmlspecialchars($q['option3']); ?></span>
                        </label>
                        <label class="option-label">
                            <input type="radio" name="question_<?php echo $q['id']; ?>" value="option4">
                            <span><strong>D.</strong> <?php echo htmlspecialchars($q['option4']); ?></span>
                        </label>
                    </div>
                <?php endif; ?>
            </div>
            <?php $q_counter++; ?>
        <?php endforeach; ?>

        <div class="submit-section">
            <span style="color: #636e72; font-size: 14px;"><i class="fas fa-check-double"></i> Review all answers before submitting.</span>
            <button type="submit" class="btn-submit-exam"><i class="fas fa-paper-plane"></i> Submit Examination</button>
        </div>
    </form>
</div>

</body>
</html>
