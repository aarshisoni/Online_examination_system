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

// Fetch selected students along with their details
$query = "SELECT register.fname, register.studentid, register.email, register.branch, register.year, register.dob, register.gender, register.phone
          FROM selected_student
          INNER JOIN register ON selected_student.studentid = register.studentid
          WHERE selected_student.teacherid = ? AND selected_student.title_name = ?
          ORDER BY register.fname ASC";

$stmt = $con->prepare($query);
$stmt->bind_param("ss", $teacherid, $title_name);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selected Students - <?php echo htmlspecialchars($title_name); ?></title>
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
        .header-nav {
            display: flex;
            gap: 10px;
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
        .btn-remove {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-remove:hover {
            background-color: #bd2130;
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-users"></i> Assigned Students for: <?php echo htmlspecialchars($title_name); ?></h2>
    <div class="header-nav">
        <a href="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>"><i class="fas fa-user-plus"></i> Assign More</a>
        <a href="quiz_questions.php?teacherid=<?php echo urlencode($teacherid); ?>&title_name=<?php echo urlencode($title_name); ?>"><i class="fas fa-arrow-left"></i> Back to Questions</a>
    </div>
</header>

<div class="container">
    <h3>Total Students Assigned: <?php echo $result->num_rows; ?></h3>
    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Student ID</th>
                <th>Email</th>
                <th>Branch</th>
                <th>Year</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['fname']); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['studentid']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['branch']); ?></td>
                        <td><?php echo htmlspecialchars($row['year']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td>
                            <a class="btn-remove" href="delete_student.php?studentid=<?php echo urlencode($row['studentid']); ?>&title_name=<?php echo urlencode($title_name); ?>" onclick="return confirm('Remove this student from the quiz?');">
                                <i class="fas fa-user-minus"></i> Remove
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" align="center" style="padding: 20px;">No students have been assigned to this quiz yet. <a href="student_list_to_select.php?title_name=<?php echo urlencode($title_name); ?>">Assign students now</a>.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
