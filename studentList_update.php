<?php
session_start();

if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";

if (!isset($_GET['studentid']) || empty($_GET['studentid'])) {
    header("Location: student_list.php");
    exit();
}

$studentid = trim($_GET['studentid']);

// Fetch student data
$stmt = $con->prepare("SELECT * FROM register WHERE studentid = ?");
$stmt->bind_param("s", $studentid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Student record not found.'); window.location.href='student_list.php';</script>";
    exit();
}

$row = $result->fetch_assoc();
$fullname = $row['fname'];
$email = $row['email'];
$dob = $row['dob'];
$gender = $row['gender'];
$branch = $row['branch'];
$year = $row['year'];
$phone = $row['phone'];

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $fname = trim($_POST['fname']);
    $email = trim($_POST['email']);
    $branch = trim($_POST['branch']);
    $year = trim($_POST['year']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $phone = trim($_POST['phone']);

    $update_stmt = $con->prepare("UPDATE register SET fname = ?, email = ?, branch = ?, year = ?, dob = ?, gender = ?, phone = ? WHERE studentid = ?");
    $update_stmt->bind_param("ssssssss", $fname, $email, $branch, $year, $dob, $gender, $phone, $studentid);

    if ($update_stmt->execute()) {
        echo "<script>alert('Student record updated successfully!'); window.location.href='student_list.php';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update student: " . addslashes($con->error) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - <?php echo htmlspecialchars($studentid); ?></title>
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
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 18px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        header h2 { margin: 0; font-size: 22px; }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 35px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 14px;
            color: #4a5568;
        }
        input[type="text"], input[type="email"], input[type="date"], select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
        }
        .btn-submit {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .btn-submit:hover { background: #219150; }
        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #718096;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-user-edit"></i> Edit Student Record</h2>
    <a href="student_list.php" style="color: white; text-decoration: none; background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
        <i class="fas fa-arrow-left"></i> Back to Student List
    </a>
</header>

<div class="container">
    <form method="post" action="studentList_update.php?studentid=<?php echo urlencode($studentid); ?>">
        <div class="form-group">
            <label>Student ID (Cannot be changed):</label>
            <input type="text" value="<?php echo htmlspecialchars($studentid); ?>" disabled style="background: #f1f3f5;">
        </div>
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="fname" value="<?php echo htmlspecialchars($fullname); ?>" required>
        </div>
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label>Branch / Department:</label>
            <select name="branch" required>
                <option value="CSE" <?php if ($branch == 'CSE') echo 'selected'; ?>>CSE</option>
                <option value="Electrical" <?php if ($branch == 'Electrical') echo 'selected'; ?>>Electrical</option>
                <option value="Civil" <?php if ($branch == 'Civil') echo 'selected'; ?>>Civil</option>
                <option value="mechanical" <?php if (strtolower($branch) == 'mechanical') echo 'selected'; ?>>Mechanical</option>
            </select>
        </div>
        <div class="form-group">
            <label>Academic Year:</label>
            <select name="year" required>
                <option value="1" <?php if ($year == '1') echo 'selected'; ?>>First Year</option>
                <option value="2" <?php if ($year == '2') echo 'selected'; ?>>Second Year</option>
                <option value="3" <?php if ($year == '3') echo 'selected'; ?>>Third Year</option>
                <option value="4" <?php if ($year == '4') echo 'selected'; ?>>Fourth Year</option>
            </select>
        </div>
        <div class="form-group">
            <label>Date of Birth:</label>
            <input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
        </div>
        <div class="form-group">
            <label>Gender:</label>
            <select name="gender">
                <option value="male" <?php if (strtolower($gender) == 'male') echo 'selected'; ?>>Male</option>
                <option value="female" <?php if (strtolower($gender) == 'female') echo 'selected'; ?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label>Contact Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
        </div>
        <button type="submit" name="update" class="btn-submit"><i class="fas fa-save"></i> Save Changes</button>
        <a href="student_list.php" class="btn-cancel">Cancel</a>
    </form>
</div>
</body>
</html>
