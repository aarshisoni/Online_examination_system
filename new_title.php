<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";
$teacherid = $_SESSION['teacherid'];

// Fetch teacher name
$t_stmt = $con->prepare("SELECT fname FROM admin_register WHERE teacherid = ?");
$t_stmt->bind_param("s", $teacherid);
$t_stmt->execute();
$t_res = $t_stmt->get_result()->fetch_assoc();
$teacherName = $t_res ? $t_res['fname'] : "Faculty";

$error_msg = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title_name = trim($_POST["title_name"]);
    
    if (!empty($new_title_name)) {
        $stmt = $con->prepare("SELECT id FROM quiz_title WHERE teacherid = ? AND title_name = ?");
        $stmt->bind_param("ss", $teacherid, $new_title_name);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error_msg = "An examination with this title already exists in your account!";
        } else {
            $stmt_ins = $con->prepare("INSERT INTO quiz_title (teacherid, title_name) VALUES (?, ?)");
            $stmt_ins->bind_param("ss", $teacherid, $new_title_name);

            if ($stmt_ins->execute()) {
                header("Location: quiz_questions.php?teacherid=" . urlencode($teacherid) . "&title_name=" . urlencode($new_title_name));
                exit();
            } else {
                $error_msg = "Error creating examination: " . $con->error;
            }
        }
    } else {
        $error_msg = "Please enter an examination title.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conduct Exam | Online Examination System</title>
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
            --card-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
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
            gap: 15px;
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

        .wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .form-card {
            background: var(--white);
            width: 100%;
            max-width: 520px;
            border-radius: 16px;
            padding: 40px 35px;
            box-shadow: var(--card-shadow);
            border: 1px solid #edf2f7;
        }

        .card-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: rgba(30, 60, 114, 0.08);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 15px;
        }

        .card-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 6px;
        }

        .card-header p {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #334155;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box i {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 16px;
        }

        .input-box input {
            width: 100%;
            padding: 13px 14px 13px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            color: var(--text-dark);
            outline: none;
            transition: var(--transition);
        }

        .input-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, #2a5298 100%);
            color: var(--white);
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(30, 60, 114, 0.25);
            transition: var(--transition);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(30, 60, 114, 0.35);
        }

        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .steps-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-top: 25px;
        }

        .steps-box h4 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .steps-box ol {
            padding-left: 20px;
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
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
            <a href="admin_dashboard.php" class="nav-btn">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="display_quize.php" class="nav-btn">
                <i class="fas fa-book-open"></i> All Quizzes
            </a>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="wrapper">
        <div class="form-card">
            <div class="card-header">
                <div class="header-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h2>Conduct New Examination</h2>
                <p>Define an exam title or subject to begin authoring questions</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <form action="new_title.php" method="POST">
                <div class="form-group">
                    <label for="title_name">Subject / Exam Title</label>
                    <div class="input-box">
                        <i class="fas fa-book-open"></i>
                        <input type="text" id="title_name" name="title_name" placeholder="e.g. Data Structures & Algorithms" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Create & Add Questions</span> <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="steps-box">
                <h4><i class="fas fa-lightbulb" style="color: #f59e0b;"></i> Next Steps in Examination Workflow:</h4>
                <ol>
                    <li>Create your Subject / Title above.</li>
                    <li>Add multiple-choice questions with marks & options.</li>
                    <li>Assign enrolled students to the test.</li>
                    <li>Review live student submissions in your gradebook.</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
