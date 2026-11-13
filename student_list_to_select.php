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
        $alertMessage = "$added_count student(s) successfully assigned to this quiz!";
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

// Fetch all registered students
$students_result = $con->query("SELECT * FROM register ORDER BY fname ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Students - <?php echo htmlspecialchars($title_name); ?></title>
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
            font-size: 22px;
        }
        .header-nav a {
            color: white;
            text-decoration: none;
            background: #345e70;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .header-nav a:hover {
            background: #233e4a;
        }
        .container {
            max-width: 1100px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-assign {
            background-color: #037808;
            color: white;
            padding: 10px 22px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-assign:hover {
            background-color: #229027;
        }
        .alert {
            padding: 12px 18px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 10px 12px;
            font-size: 14px;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .badge-assigned {
            background: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-user-plus"></i> Assign Students to Quiz: <?php echo htmlspecialchars($title_name); ?></h2>
    <div class="header-nav">
        <a href="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>"><i class="fas fa-arrow-left"></i> Back to Questions</a>
    </div>
</header>

<div class="container">
    <?php if (!empty($alertMessage)): ?>
        <div class="alert alert-<?php echo $alertType; ?>">
            <?php echo htmlspecialchars($alertMessage); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>">
        <div class="toolbar">
            <div>
                <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                <label for="selectAll" style="font-weight: bold; cursor: pointer;"> Select / Deselect All</label>
            </div>
            <div>
                <button type="submit" name="add_students" class="btn-assign">
                    <i class="fas fa-check"></i> Assign Selected Students
                </button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">Select</th>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Email</th>
                    <th>Branch</th>
                    <th>Year</th>
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
                                <input type="checkbox" class="student-checkbox" name="selected_students[]" value="<?php echo htmlspecialchars($row['studentid']); ?>" <?php echo $isAssigned ? 'disabled' : ''; ?>>
                            </td>
                            <td><?php echo htmlspecialchars($row['fname']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['studentid']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['branch']); ?></td>
                            <td><?php echo htmlspecialchars($row['year']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td>
                                <?php if ($isAssigned): ?>
                                    <span class="badge-assigned"><i class="fas fa-check-circle"></i> Assigned</span>
                                <?php else: ?>
                                    <span class="badge-pending">Not Assigned</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" align="center" style="padding: 20px;">No registered students found in database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
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
