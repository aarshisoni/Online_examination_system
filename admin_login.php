<?php
include("connect.php");
session_start();
$error_msg = "";

if(isset($_POST["submit"])){
    $teacherid = trim($_POST["teacherid"]);
    $password = $_POST["password"];

    $stmt = $con->prepare("SELECT * FROM admin_register WHERE teacherid = ? AND password = ?");
    $stmt->bind_param("ss", $teacherid, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows == 1){
        $_SESSION['teacherid'] = $teacherid;
        header("Location: admin_dashboard.php?teacherid=" . urlencode($teacherid));
        exit();
    } else {
        $error_msg = "Invalid Teacher ID or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Login | Online Examination System</title>
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

        .nav-back {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .nav-back:hover {
            color: var(--primary);
        }

        /* Login Container */
        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-card {
            background: var(--white);
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            padding: 35px 30px;
            box-shadow: var(--card-shadow);
            border: 1px solid #edf2f7;
        }

        /* Role Switcher Tabs */
        .role-tabs {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .tab-btn {
            flex: 1;
            text-align: center;
            padding: 9px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .tab-btn.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(30, 60, 114, 0.08);
            color: var(--primary);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .auth-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            font-size: 14px;
            color: #334155;
        }

        .input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box i.input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 15px;
        }

        .input-box input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            color: var(--text-dark);
            transition: var(--transition);
            outline: none;
        }

        .input-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            cursor: pointer;
            color: #94a3b8;
            font-size: 16px;
        }

        .toggle-password:hover {
            color: var(--text-dark);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, #2a5298 100%);
            color: var(--white);
            border: none;
            width: 100%;
            padding: 13px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
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

        .auth-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            font-size: 14px;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .demo-hint {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <nav class="navbar">
        <a href="MainPage.html" class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>ExamPortal</span>
        </a>
        <a href="MainPage.html" class="nav-back">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </nav>

    <!-- Form Area -->
    <div class="auth-wrapper">
        <div class="auth-card">
            
            <!-- Role Tabs -->
            <div class="role-tabs">
                <a href="admin_login.php" class="tab-btn active"><i class="fas fa-chalkboard-teacher"></i> Faculty</a>
                <a href="student_login.php" class="tab-btn"><i class="fas fa-user-graduate"></i> Student</a>
            </div>

            <div class="auth-header">
                <div class="role-badge">
                    <i class="fas fa-shield-alt"></i> Faculty Portal
                </div>
                <h2>Instructor Login</h2>
                <p>Sign in to conduct exams and evaluate students</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <form action="admin_login.php" method="POST">
                <div class="form-group">
                    <label for="teacherid">Teacher ID / Username</label>
                    <div class="input-box">
                        <i class="fas fa-id-badge input-icon"></i>
                        <input type="text" id="teacherid" name="teacherid" placeholder="Enter your Teacher ID" required autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-box">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Sign In to Dashboard
                </button>
            </form>

            <div class="demo-hint">
                <i class="fas fa-info-circle"></i> Demo Credentials: ID: <strong>T101</strong> | Password: <strong>password123</strong>
            </div>

            <div class="auth-footer">
                Don't have a faculty account? <a href="adm_register.php">Register now</a>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
