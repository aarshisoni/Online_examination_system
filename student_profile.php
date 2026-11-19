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

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $branch = trim($_POST['branch']);
    $year = trim($_POST['year']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $phone = trim($_POST['phone']);

    $stmt_up = $con->prepare("UPDATE register SET fname = ?, email = ?, branch = ?, year = ?, dob = ?, gender = ?, phone = ? WHERE studentid = ?");
    $stmt_up->bind_param("ssssssss", $fullname, $email, $branch, $year, $dob, $gender, $phone, $studentid);

    if ($stmt_up->execute()) {
        $alert_msg = "Profile updated successfully!";
        $alert_type = "success";
    } else {
        $alert_msg = "Error updating profile: " . $con->error;
        $alert_type = "danger";
    }
}

// Fetch current details
$stmt = $con->prepare("SELECT * FROM register WHERE studentid = ?");
$stmt->bind_param("s", $studentid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$fullname = $row['fname'];
$email = $row['email'];
$branch = $row['branch'];
$year = $row['year'];
$dob = $row['dob'];
$gender = $row['gender'];
$phone = $row['phone'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - <?php echo htmlspecialchars($fullname); ?></title>
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 18px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        header h2 { margin: 0; font-size: 22px; font-weight: 600; }
        .container {
            max-width: 650px;
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
        .alert {
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<header>
    <h2><i class="fas fa-user-circle"></i> Student Profile</h2>
    <div style="display: flex; gap: 10px;">
        <a href="student_change_password.php" style="color: white; text-decoration: none; background: #e67e22; padding: 8px 16px; border-radius: 6px; font-weight: 500;">
            <i class="fas fa-key"></i> Change Password
        </a>
        <a href="student_dashboard.php" style="color: white; text-decoration: none; background: rgba(0,0,0,0.15); padding: 8px 16px; border-radius: 6px; font-weight: 500;">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>
</header>

<div class="container">
    <?php if (!empty($alert_msg)): ?>
        <div class="alert alert-<?php echo $alert_type; ?>"><?php echo htmlspecialchars($alert_msg); ?></div>
    <?php endif; ?>

    <form method="post" action="student_profile.php">
        <div class="form-group">
            <label>Student ID:</label>
            <input type="text" value="<?php echo htmlspecialchars($studentid); ?>" disabled style="background: #f1f3f5;">
        </div>
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
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
                <option value="1" <?php if ($year == '1') echo 'selected'; ?>>1st Year</option>
                <option value="2" <?php if ($year == '2') echo 'selected'; ?>>2nd Year</option>
                <option value="3" <?php if ($year == '3') echo 'selected'; ?>>3rd Year</option>
                <option value="4" <?php if ($year == '4') echo 'selected'; ?>>4th Year</option>
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
            <label>Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
        </div>
        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Update Profile</button>
    </form>
</div>
</body>
</html>
