<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['teacherid'])) {
    // Redirect to login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Include database connection file
include "connect.php";

// Fetch admin's name from the admin_register table using session username
$teacherid = $_SESSION['teacherid'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the new title name from the form submission
    $new_title_name = $_POST["title_name"];
    
    // SQL query to insert the new title name into the quiz_title table 
    $sql_insert_title = "INSERT INTO quiz_title (teacherid, title_name) VALUES ('$teacherid', '$new_title_name')";

    // Execute the query
    if ($con->query($sql_insert_title) === TRUE) {
        // Title name inserted successfully, show alert
        header("Location: admin_dashboard.php");
        echo "<script>alert('Title name created successfully!');</script>";
    } else {
        // Error occurred while inserting title name
        echo "<script>alert('Error creating title name: " . $con->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Title</title>
    <style>
        body {
            font-family: Arial;
            background-color: rgb(235, 210, 210);
            
        }
        .note{
            margin:16% 0 0 22%;
        }
        form {
            background:white;
            margin: 2% 0 0 35%;
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.7);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #45a049;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size:15px;
        }
        input[type="submit"]:hover {
            background-color: green;
        }
    </style> 
</head>
<body>
<div class="note">Note: Underscore is require in title name, if not given then the quiz will not be generated.</div>
<form action="new_title.php" method="post">
    <label for="title_name">New Title Name:</label>
    <input type="text" id="title_name" name="title_name" required>
    <input type="submit" value="Submit">
</form>

</body>
</html>
