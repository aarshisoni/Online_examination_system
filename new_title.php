<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['teacherid'])) {
    header("Location: admin_login.php");
    exit();
}

include "connect.php";
$teacherid = $_SESSION['teacherid'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title_name = trim($_POST["title_name"]);
    
    if (!empty($new_title_name)) {
        $stmt = $con->prepare("SELECT id FROM quiz_title WHERE teacherid = ? AND title_name = ?");
        $stmt->bind_param("ss", $teacherid, $new_title_name);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo "<script>alert('A quiz with this title already exists!'); window.location.href='new_title.php';</script>";
            exit();
        }

        $stmt = $con->prepare("INSERT INTO quiz_title (teacherid, title_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $teacherid, $new_title_name);

        if ($stmt->execute()) {
            header("Location: quiz_questions.php?teacherid=" . urlencode($teacherid) . "&title_name=" . urlencode($new_title_name));
            exit();
        } else {
            echo "<script>alert('Error creating title: " . addslashes($con->error) . "'); window.location.href='new_title.php';</script>";
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(235, 210, 210);
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: rgb(184, 149, 149);
            color: white;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header h2 {
            margin: 0;
        }
        .back-link {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            background: #345e70;
            padding: 8px 16px;
            border-radius: 5px;
        }
        .back-link:hover {
            background: #254452;
        }
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 80px;
        }
        form {
            background: white;
            width: 360px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #037808;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #229027;
        }
    </style> 
</head>
<body>
<div class="header">
    <h2>Conduct Exam - Create Quiz</h2>
    <a href="admin_dashboard.php" class="back-link">&#8592; Back to Dashboard</a>
</div>
<div class="form-container">
    <form action="new_title.php" method="post">
        <label for="title_name">Enter Quiz Title / Subject:</label>
        <input type="text" id="title_name" name="title_name" placeholder="e.g. Java_Basics or Database_Quiz" required>
        <input type="submit" value="Create & Add Questions &#8594;">
    </form>
</div>
</body>
</html>
