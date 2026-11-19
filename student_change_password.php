<?php
session_start();

if (!isset($_SESSION['studentid'])) {
    header("Location: student_login.php");
    exit();
}

include "connect.php";
$studentid = $_SESSION['studentid'];

$alert_msg = "";
$alert_type = "";

// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = isset($_POST['current_password']) ? trim($_POST['current_password']) : '';
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $alert_msg = "Please fill in all password fields.";
        $alert_type = "danger";
    } elseif (strlen($new_password) < 6) {
        $alert_msg = "New password must be at least 6 characters long.";
        $alert_type = "danger";
    } elseif ($new_password !== $confirm_password) {
        $alert_msg = "New password and confirmation password do not match.";
        $alert_type = "danger";
    } else {
        // Fetch current password from database
        $stmt = $con->prepare("SELECT password FROM register WHERE studentid = ?");
        $stmt->bind_param("s", $studentid);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            if ($row['password'] !== $current_password) {
                $alert_msg = "Current password is incorrect.";
                $alert_type = "danger";
            } elseif ($current_password === $new_password) {
                $alert_msg = "New password cannot be the same as your current password.";
                $alert_type = "warning";
            } else {
                // Update password
                $update_stmt = $con->prepare("UPDATE register SET password = ?, cpassword = ? WHERE studentid = ?");
                $update_stmt->bind_param("sss", $new_password, $new_password, $studentid);

                if ($update_stmt->execute()) {
                    $alert_msg = "Password has been successfully changed!";
                    $alert_type = "success";
                } else {
                    $alert_msg = "Error updating password: " . $con->error;
                    $alert_type = "danger";
                }
            }
        } else {
            $alert_msg = "Student record not found.";
            $alert_type = "danger";
        }
    }
}

// Fetch student name
$student_stmt = $con->prepare("SELECT fname FROM register WHERE studentid = ?");
$student_stmt->bind_param("s", $studentid);
$student_stmt->execute();
$s_data = $student_stmt->get_result()->fetch_assoc();
$student_name = $s_data ? $s_data['fname'] : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Student Portal | ExamPortal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #11998e;
            --primary-dark: #0e8076;
            --secondary: #1e3c72;
            --accent: #38ef7d;
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

        /* Glassmorphic Navigation */
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
            color: var(--secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo span {
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .btn {
            padding: 0.55rem 1.15rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-outline {
            color: var(--primary);
            border: 1.5px solid var(--primary);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #38ef7d 100%);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.25);
            width: 100%;
            justify-content: center;
            padding: 0.85rem;
            font-size: 1rem;
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(17, 153, 142, 0.35);
        }

        .container {
            max-width: 580px;
            width: 92%;
            margin: 3rem auto;
            flex: 1;
        }

        .card {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: rgba(17, 153, 142, 0.1);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
        }

        .card-header h2 {
            font-size: 1.6rem;
            color: var(--secondary);
            margin-bottom: 0.5rem;
        }

        .card-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }

        .form-group {
            margin-bottom: 1.4rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 0.75rem 2.8rem 0.75rem 2.8rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: var(--transition);
        }

        .input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.15);
        }

        .input-icon-left {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1rem;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .password-requirements {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.85rem 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .password-requirements ul {
            list-style: none;
            margin-top: 0.35rem;
            padding-left: 0;
        }

        .password-requirements li {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }

        .password-requirements li i {
            color: var(--primary);
            font-size: 0.75rem;
        }

        .quick-links {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        .quick-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .quick-links a:hover {
            color: var(--secondary);
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

    <!-- Glassmorphic Top Navbar -->
    <header class="navbar">
        <a href="student_dashboard.php" class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>ExamPortal</span>
        </a>
        <div class="nav-links">
            <a href="student_profile.php" class="btn btn-outline">
                <i class="fas fa-user-circle"></i> Profile
            </a>
            <a href="student_dashboard.php" class="btn btn-outline">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Change Student Password</h2>
                <p>Logged in as: <strong><?php echo htmlspecialchars($student_name); ?></strong> (<?php echo htmlspecialchars($studentid); ?>)</p>
            </div>

            <?php if (!empty($alert_msg)): ?>
                <div class="alert alert-<?php echo $alert_type; ?>">
                    <i class="fas <?php echo $alert_type === 'success' ? 'fa-check-circle' : ($alert_type === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle'); ?>"></i>
                    <span><?php echo htmlspecialchars($alert_msg); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="student_change_password.php">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon-left"></i>
                        <input type="password" id="current_password" name="current_password" required placeholder="Enter current password">
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('current_password', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-group">
                        <i class="fas fa-shield-alt input-icon-left"></i>
                        <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="Enter new password (min. 6 characters)">
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="input-group">
                        <i class="fas fa-check-double input-icon-left"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Confirm new password">
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password', this)"></i>
                    </div>
                </div>

                <div class="password-requirements">
                    <strong>Password Guidelines:</strong>
                    <ul>
                        <li><i class="fas fa-circle"></i> Minimum 6 characters in length</li>
                        <li><i class="fas fa-circle"></i> Must differ from your existing password</li>
                        <li><i class="fas fa-circle"></i> Never share your exam portal password with anyone</li>
                    </ul>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Password
                </button>

                <div class="quick-links">
                    <a href="student_profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
                    <a href="student_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Online Examination System. All rights reserved.</p>
    </footer>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
