<?php
@include "connect.php";

if(isset($_POST["submit"])){
    $fname = trim($_POST['fname']);
    $studentid = trim($_POST["studentid"]);
    $email = trim($_POST["email"]);
    $branch = $_POST["Branch"];
    $year = $_POST["year"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    // Check if passwords match
    if ($password !== $cpassword){
        echo '<script>alert("Passwords do not match!"); window.location.href="student_register.php";</script>';
        exit();
    }

    // Check if student ID or email already exists
    $stmt = $con->prepare("SELECT studentid FROM register WHERE studentid = ? OR email = ?");
    $stmt->bind_param("ss", $studentid, $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo '<script>alert("Student ID or Email already registered!"); window.location.href="student_register.php";</script>';
        exit();
    }

    // Insert new student
    $stmt = $con->prepare("INSERT INTO register (fname, studentid, email, branch, year, dob, gender, phone, password, cpassword) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $fname, $studentid, $email, $branch, $year, $dob, $gender, $phone, $password, $cpassword);
    if ($stmt->execute()) {
        echo '<script>alert("Registration successful! Please login."); window.location.href="student_login.php";</script>';
        exit();
    } else {
        echo '<script>alert("Registration failed: ' . addslashes($con->error) . '"); window.location.href="student_register.php";</script>';
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration | Online Examination System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #1e3c72;
            --accent: #11998e;
            --accent-dark: #0e8076;
            --accent-light: #38ef7d;
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
            color: var(--accent);
        }

        /* Registration Form Area */
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
            max-width: 680px;
            border-radius: 16px;
            padding: 35px 35px;
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
            color: var(--accent-dark);
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
            background: rgba(17, 153, 142, 0.1);
            color: var(--accent);
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
            color: var(--text-dark);
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .full-span {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 13px;
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
            font-size: 14px;
        }

        .input-box input, .input-box select {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: var(--text-dark);
            transition: var(--transition);
            outline: none;
            background: #ffffff;
        }

        .input-box input:focus, .input-box select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.12);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            cursor: pointer;
            color: #94a3b8;
            font-size: 15px;
        }

        .toggle-password:hover {
            color: var(--text-dark);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
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
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
            transition: var(--transition);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(17, 153, 142, 0.4);
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
            font-size: 14px;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--accent);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .full-span {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="MainPage.html" class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>ExamPortal</span>
        </a>
        <a href="MainPage.html" class="nav-back">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </nav>

    <!-- Registration Area -->
    <div class="auth-wrapper">
        <div class="auth-card">
            
            <!-- Role Tabs -->
            <div class="role-tabs">
                <a href="adm_register.php" class="tab-btn"><i class="fas fa-chalkboard-teacher"></i> Faculty</a>
                <a href="student_register.php" class="tab-btn active"><i class="fas fa-user-graduate"></i> Student</a>
            </div>

            <div class="auth-header">
                <div class="role-badge">
                    <i class="fas fa-user-graduate"></i> Student Portal
                </div>
                <h2>Create Student Account</h2>
                <p>Register to attempt active exams and track your academic scores</p>
            </div>

            <form action="student_register.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fname">Full Name</label>
                        <div class="input-box">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="fname" name="fname" placeholder="Aarav Patel" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="studentid">Student ID / Roll No.</label>
                        <div class="input-box">
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text" id="studentid" name="studentid" placeholder="e.g. S102" required>
                        </div>
                    </div>

                    <div class="form-group full-span">
                        <label for="email">Email Address</label>
                        <div class="input-box">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" placeholder="student@university.edu" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="Branch">Branch / Department</label>
                        <div class="input-box">
                            <i class="fas fa-graduation-cap input-icon"></i>
                            <select id="Branch" name="Branch" required>
                                <option value="">Select Department</option>
                                <option value="CSE">Computer Science (CSE)</option>
                                <option value="Electrical">Electrical Engineering</option>
                                <option value="Civil">Civil Engineering</option>
                                <option value="Mechanical">Mechanical Engineering</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="year">Academic Year</label>
                        <div class="input-box">
                            <i class="fas fa-calendar-check input-icon"></i>
                            <select id="year" name="year" required>
                                <option value="">Select Year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <div class="input-box">
                            <i class="fas fa-calendar-alt input-icon"></i>
                            <input type="date" id="dob" name="dob" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <div class="input-box">
                            <i class="fas fa-venus-mars input-icon"></i>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full-span">
                        <label for="phone">Contact Phone</label>
                        <div class="input-box">
                            <i class="fas fa-phone input-icon"></i>
                            <input type="text" id="phone" name="phone" placeholder="10-digit number" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-box">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" minlength="4" placeholder="At least 4 characters" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePass('password', this)"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cpassword">Confirm Password</label>
                        <div class="input-box">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="cpassword" name="cpassword" minlength="4" placeholder="Re-enter password" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePass('cpassword', this)"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Complete Student Registration
                </button>
            </form>

            <div class="auth-footer">
                Already registered as a student? <a href="student_login.php">Sign In</a>
            </div>
        </div>
    </div>

    <script>
        function togglePass(inputId, icon) {
            const input = document.getElementById(inputId);
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>
</html>
