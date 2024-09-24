<?php
session_start();

// Include database connection file
include 'connect.php';

// Check if teacher is logged in
if (!isset($_SESSION['teacherid'])) {
    // Redirect to login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Fetch teacher's ID from session
$teacherid = $_SESSION['teacherid'];

// Fetch title_name from URL parameters
$title_name = isset($_GET['title_name']) ? $_GET['title_name'] : '';

// Initialize an array to store selected student details
$selectedStudents = array();

// Fetch selected students along with their details
$query = "SELECT register.fname, register.studentid, register.email, register.branch, register.year, register.dob, register.gender, register.phone
          FROM selected_student
          INNER JOIN register ON selected_student.studentid = register.studentid
          WHERE selected_student.teacherid = '$teacherid' AND selected_student.title_name = '$title_name'";

$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Fetch selected student details and store them in the array
    while ($row = mysqli_fetch_assoc($result)) {
        $selectedStudents[] = $row;
    }
} else {
    // Handle query error or no result found
    //echo "No selected students found.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selected Student List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header{ 
            background-image: linear-gradient(100deg,white, rgb(184, 149, 149));
            background-color: rgb(184, 149, 149);
        }

        h2 {
            font-size:35px;
            padding:1% 0 1% 0;
            text-align: center;
            margin-top: 15px;
            margin-bottom:0;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            border: 2px solid #888; /* Darker border color */
            text-align: left;
            padding: 8px;
        }

        td.operation {
            text-align: center;
        }


        th {
            background-color: #d9d9d9; 
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: red;
            border:2px solid red;
            padding:7px;
            color:white;
            border-radius:10px;
            justify-content:center;
        }
        .delete-btn:hover{
            background-color: #f44336;
        }

        .back-button {
            display: inline-block;
            background-color: #008CBA;
            color: white;
            margin: 2% 0 30px 5%;
            padding: 10px;
            text-decoration: none;
            border-radius: 50%; /* For making the button circular */
            width: 30px; /* Adjust size of the circle */
            height: 30px; /* Adjust size of the circle */
            line-height: 28px; /* For centering the arrow */
            text-align: center;
            font-size: 24px;
            box-shadow: 4px 4px 6px #888888; /* Adding border shadow */
        }

        .back-button:hover {
            background-color: #005f6b;
        }
    </style>
</head>
<body>
    <header>
    <h2>Selected Student List</h2>
    </header>
    <!-- Back button -->
    <a href="quiz_questions.php?teacherid=<?php echo $teacherid; ?>&title_name=<?php echo urlencode($title_name); ?>" class="back-button" style="font-size: 30px; font-weight: 500;">&#8592;</a>

    <table>
        <tr>
            <th>Name</th>
            <th>Student ID</th>
            <th>Email</th>
            <th>Branch</th>
            <th>Year</th>
            <th class="operation">Operation</th>
        </tr>
        <?php
            // Loop through the selected students and display their details
            foreach ($selectedStudents as $student) {
                echo "<tr>";
                echo "<td>" . $student['fname'] . "</td>";
                echo "<td>" . $student['studentid'] . "</td>";
                echo "<td>" . $student['email'] . "</td>";
                echo "<td>" . $student['branch'] . "</td>";
                echo "<td>" . $student['year'] . "</td>";
                echo "<td class='operation'><button class='delete-btn' onclick='deleteStudent(\"" . $student['studentid'] . "\")'>Delete</button></td>";
                echo "</tr>";
            }
            ?>

    </table>
    <script>
        function deleteStudent(studentId) {
            if (confirm("Are you sure you want to delete this student?")) {
                // Send an AJAX request to delete_student.php with the student ID
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_student.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Reload the page to reflect the changes
                        location.reload();
                    }
                };
                xhr.send("studentid=" + studentId);
            }
        }
    </script>
</body>
</html>
