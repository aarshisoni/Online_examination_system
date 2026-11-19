<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";
$teacherid = $_SESSION['teacherid'];

$student_id = isset($_GET['studentid']) ? trim($_GET['studentid']) : '';
$title_name = isset($_GET['title_name']) ? trim($_GET['title_name']) : '';

if (empty($student_id) || empty($title_name)) {
    header("Location: student_score.php");
    exit();
}

// Fetch Student details
$stmt_stu = $con->prepare("SELECT * FROM register WHERE studentid = ?");
$stmt_stu->bind_param("s", $student_id);
$stmt_stu->execute();
$student_data = $stmt_stu->get_result()->fetch_assoc();

// Fetch Submission score
$stmt_sc = $con->prepare("SELECT * FROM submit_score WHERE studentid = ? AND teacherid = ? AND title_name = ?");
$stmt_sc->bind_param("sss", $student_id, $teacherid, $title_name);
$stmt_sc->execute();
$score_data = $stmt_sc->get_result()->fetch_assoc();

// Fetch all questions
$stmt_q = $con->prepare("SELECT * FROM quiz_questions WHERE teacherid = ? AND title_name = ? ORDER BY question_number ASC");
$stmt_q->bind_param("ss", $teacherid, $title_name);
$stmt_q->execute();
$questions_res = $stmt_q->get_result();

// Fetch saved student answers
$stmt_ans = $con->prepare("SELECT * FROM student_answers WHERE studentid = ? AND teacherid = ? AND title_name = ?");
$stmt_ans->bind_param("sss", $student_id, $teacherid, $title_name);
$stmt_ans->execute();
$ans_res = $stmt_ans->get_result();

$saved_answers = [];
while ($a = $ans_res->fetch_assoc()) {
    $saved_answers[$a['question_id']] = $a;
}

$total_max = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paper Review - <?php echo htmlspecialchars($student_id); ?> | <?php echo htmlspecialchars($title_name); ?></title>
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
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-outline {
            color: var(--primary);
            border: 1.5px solid var(--primary);
            background: transparent;
            padding: 0.55rem 1.15rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
        }

        .container {
            max-width: 950px;
            width: 92%;
            margin: 2.5rem auto;
            flex: 1;
        }

        .meta-card {
            background: var(--white);
            border-radius: 14px;
            padding: 1.8rem 2rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            border: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .student-info h2 {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .student-info p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .score-pill {
            background: linear-gradient(135deg, var(--accent) 0%, #38ef7d 100%);
            color: white;
            padding: 14px 24px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.25);
        }

        .score-pill h3 {
            font-size: 1.8rem;
            margin: 0;
            line-height: 1;
        }

        .score-pill span {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }

        .q-card {
            background: var(--white);
            border-radius: 12px;
            padding: 1.6rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid #edf2f7;
        }

        .q-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .badge-type {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .badge-written {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .badge-mcq {
            background: #e0f2fe;
            color: #0369a1;
        }

        .q-text {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .ans-block {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 0.92rem;
        }

        .student-ans {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .model-ans {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #14532d;
        }

        footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 1.25rem 5%;
            text-align: center;
            font-size: 0.85rem;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header class="navbar">
        <a href="admin_dashboard.php" class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>ExamPortal</span>
        </a>
        <a href="student_score.php?title_name=<?php echo urlencode($title_name); ?>" class="btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Gradebook
        </a>
    </header>

    <div class="container">
        <div class="meta-card">
            <div class="student-info">
                <h2><?php echo htmlspecialchars($student_data ? $student_data['fname'] : 'Student Submission'); ?></h2>
                <p>
                    Student ID: <strong><?php echo htmlspecialchars($student_id); ?></strong> | 
                    Branch: <strong><?php echo htmlspecialchars($student_data['branch'] ?? 'N/A'); ?></strong> | 
                    Year: <strong><?php echo htmlspecialchars($student_data['year'] ?? 'N/A'); ?></strong>
                </p>
                <p style="margin-top: 4px;">
                    Examination: <strong><?php echo htmlspecialchars($title_name); ?></strong>
                </p>
            </div>
            <div class="score-pill">
                <h3><?php echo $score_data ? $score_data['marks'] : '0'; ?></h3>
                <span>Score Obtained</span>
            </div>
        </div>

        <h3 style="margin-bottom: 1.2rem; color: var(--primary); display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-signature"></i> Submitted Answer Script
        </h3>

        <?php if ($questions_res->num_rows > 0): ?>
            <?php while ($q = $questions_res->fetch_assoc()): ?>
                <?php 
                    $total_max += $q['marks'];
                    $isWritten = (isset($q['question_type']) && $q['question_type'] === 'written');
                    $ansRecord = isset($saved_answers[$q['id']]) ? $saved_answers[$q['id']] : null;
                    $submittedText = $ansRecord ? $ansRecord['student_answer'] : 'No answer submitted';
                    $marksAwarded = $ansRecord ? $ansRecord['marks_awarded'] : 0;
                ?>
                <div class="q-card">
                    <div class="q-top">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-weight: 700; color: var(--primary);">Question <?php echo $q['question_number']; ?></span>
                            <span class="badge-type <?php echo $isWritten ? 'badge-written' : 'badge-mcq'; ?>">
                                <i class="fas <?php echo $isWritten ? 'fa-pen-nib' : 'fa-list-ul'; ?>"></i> <?php echo $isWritten ? 'Written / Descriptive' : 'MCQ'; ?>
                            </span>
                        </div>
                        <span style="font-size: 0.88rem; font-weight: 700; color: #0284c7;">
                            Weight: <?php echo $q['marks']; ?> Mark(s)
                        </span>
                    </div>

                    <div class="q-text">
                        <?php echo htmlspecialchars($q['question']); ?>
                    </div>

                    <div class="ans-block student-ans">
                        <strong style="color: #475569; display: block; margin-bottom: 4px;">
                            <i class="fas fa-user-edit"></i> Student's Submitted Answer:
                        </strong>
                        <div><?php echo nl2br(htmlspecialchars($submittedText)); ?></div>
                    </div>

                    <div class="ans-block model-ans">
                        <strong style="color: #166534; display: block; margin-bottom: 4px;">
                            <i class="fas fa-key"></i> Model Answer / Instructor Key:
                        </strong>
                        <div>
                            <?php 
                                if ($isWritten) {
                                    echo nl2br(htmlspecialchars($q['correct_option']));
                                } else {
                                    $corrKey = $q['correct_option'];
                                    echo htmlspecialchars(isset($q[$corrKey]) ? $q[$corrKey] : $corrKey);
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; color: var(--text-muted); padding: 40px;">No questions found for this exam.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Online Examination System. All rights reserved.</p>
    </footer>

</body>
</html>
