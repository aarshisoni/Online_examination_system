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

$alertMessage = '';
$alertType = 'info';

// Handle form submission to assign students
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_students'])) {
    if (isset($_POST['selected_students']) && is_array($_POST['selected_students']) && !empty($_POST['selected_students'])) {
        $added_count = 0;
        foreach ($_POST['selected_students'] as $studentid) {
            $studentid = trim($studentid);
            // Check if already assigned
            $stmt_check = $con->prepare("SELECT sid FROM selected_student WHERE teacherid = ? AND title_name = ? AND studentid = ?");
            $stmt_check->bind_param("sss", $teacherid, $title_name, $studentid);
            $stmt_check->execute();
            if ($stmt_check->get_result()->num_rows == 0) {
                $stmt_ins = $con->prepare("INSERT INTO selected_student (teacherid, title_name, studentid) VALUES (?, ?, ?)");
                $stmt_ins->bind_param("sss", $teacherid, $title_name, $studentid);
                if ($stmt_ins->execute()) {
                    $added_count++;
                }
            }
        }
        $alertMessage = "$added_count student(s) successfully assigned to this exam!";
        $alertType = 'success';
    } else {
        $alertMessage = "Please select at least one student.";
        $alertType = 'danger';
    }
}

// Fetch already assigned students for this quiz
$assigned_set = array();
$stmt_assigned = $con->prepare("SELECT studentid FROM selected_student WHERE teacherid = ? AND title_name = ?");
$stmt_assigned->bind_param("ss", $teacherid, $title_name);
$stmt_assigned->execute();
$res_assigned = $stmt_assigned->get_result();
while ($r = $res_assigned->fetch_assoc()) {
    $assigned_set[$r['studentid']] = true;
}

// Optional Branch Filter
$branch_filter = isset($_GET['branch_filter']) ? trim($_GET['branch_filter']) : '';
$q_students = "SELECT * FROM register";
if (!empty($branch_filter)) {
    $q_students .= " WHERE branch = '" . $con->real_escape_string($branch_filter) . "'";
}
$q_students .= " ORDER BY fname ASC";
$students_result = $con->query($q_students);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Students - <?php echo htmlspecialchars($title_name); ?></title>
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

        .container {
            max-width: 1200px;
            margin: 35px auto 60px;
            padding: 0 20px;
            width: 100%;
        }

        .card {
            background: var(--white);
            border-radius: 14px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            border: 1px solid #edf2f7;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-header h2 {
            margin: 0;
            font-size: 22px;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group select {
            padding: 8px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }

        .btn-assign {
            background: linear-gradient(135deg, var(--accent) 0%, #38ef7d 100%);
            color: var(--white);
            padding: 11px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
            transition: var(--transition);
        }

        .btn-assign:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(17, 153, 142, 0.4);
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
        }

        th {
            background-color: #f7fafc;
            color: #4a5568;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .badge-assigned {
            background: #d4edda;
            color: #155724;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-pending {
            background: #f1f5f9;
            color: var(--text-muted);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
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
            <a href="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>" class="nav-btn">
                <i class="fas fa-arrow-left"></i> Questions Bank
            </a>
            <a href="selected_student_list.php?title_name=<?php echo urlencode($title_name); ?>&teacherid=<?php echo urlencode($teacherid); ?>" class="nav-btn">
                <i class="fas fa-users"></i> View Assigned
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-user-plus" style="color: var(--accent);"></i> Assign Students to: <?php echo htmlspecialchars($title_name); ?></h2>
                <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">
                    Currently Assigned: <strong><?php echo count($assigned_set); ?> Students</strong>
                </span>
            </div>

            <?php if (!empty($alertMessage)): ?>
                <div class="alert-box <?php echo $alertType; ?>">
                    <?php echo htmlspecialchars($alertMessage); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>">
                <div class="toolbar">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 700; cursor: pointer; font-size: 14px;">
                            <input type="checkbox" id="selectAll" onclick="toggleAll(this)" style="width: 17px; height: 17px; accent-color: var(--accent);"> Select All Unassigned
                        </label>
                    </div>

                    <div>
                        <button type="submit" name="add_students" class="btn-assign">
                            <i class="fas fa-check-circle"></i> Assign Selected Students
                        </button>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th width="5%">Select</th>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Email Address</th>
                            <th>Department</th>
                            <th>Academic Year</th>
                            <th>Phone</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($students_result && $students_result->num_rows > 0): ?>
                            <?php while ($row = $students_result->fetch_assoc()): ?>
                                <?php $isAssigned = isset($assigned_set[$row['studentid']]); ?>
                                <tr>
                                    <td align="center">
                                        <input type="checkbox" class="student-checkbox" name="selected_students[]" value="<?php echo htmlspecialchars($row['studentid']); ?>" <?php echo $isAssigned ? 'disabled' : ''; ?> style="width: 17px; height: 17px; accent-color: var(--accent);">
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($row['fname']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['studentid']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['branch']); ?></td>
                                    <td>Year <?php echo htmlspecialchars($row['year']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                    <td>
                                        <?php if ($isAssigned): ?>
                                            <span class="badge-assigned"><i class="fas fa-check"></i> Assigned</span>
                                        <?php else: ?>
                                            <span class="badge-pending">Not Assigned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" align="center" style="padding: 30px; color: var(--text-muted);">No student records found in the database.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <script>
        function toggleAll(source) {
            var checkboxes = document.querySelectorAll('.student-checkbox:not(:disabled)');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</body>
</html>
