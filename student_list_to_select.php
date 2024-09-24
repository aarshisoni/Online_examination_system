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

// Fetch data from the database
$query = "SELECT * FROM register";
$data = mysqli_query($con, $query);

// Initialize alert message
$alertMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_students'])) {
    // Check if any students are selected
    if (isset($_POST['selected_students']) && !empty($_POST['selected_students'])) {
        // Prepare and execute SQL query to insert selected students into selected_student table
        $selected_students = $_POST['selected_students'];
        foreach ($selected_students as $studentid) {
            $sql_insert = "INSERT INTO selected_student (teacherid, title_name, studentid) VALUES ('$teacherid', '$title_name', '$studentid')";
            // Execute the SQL query and check for errors
            $result = mysqli_query($con, $sql_insert);
            if (!$result) {
                // Error message if insertion fails
                $alertMessage = "Error: " . mysqli_error($con);
            }
        }
        // Set success message if there are no errors
        if (empty($alertMessage)) {
            $alertMessage = "Students added successfully!";
        }
    } else {
        // Handle case where no students are selected
        $alertMessage = "No students selected.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <style>
        body {
            font-family: Arial;
            margin: 0;
            padding: 0;
        }

        header{ 
            background-image: linear-gradient(100deg,white, rgb(184, 149, 149));
            background-color: rgb(184, 149, 149);
        }

        h2 {
            text-align: center;
            padding:1% 0 1% 0;
            margin-top: 15px;
            font-size:30px;
        }

        .alert {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ebccd1;
            border-radius: 4px;
        }

        table {
            border-collapse: collapse;
            width: 90%;
            margin-left:5%;
        }

        th, td {
            border: 2px solid #888; /* Darker border color */
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #d9d9d9; 
            text-align: center;
        }

        .add-button {
            margin-bottom: 10px;
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .add-button:hover {
            background-color: #45a049; /* Darker green */
        }

        label {
            display: block;
            margin: 5px;
            font-size: 18px;
            color: #555;
        }

        input[type="checkbox"] {
            transform: scale(1.5);
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
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
    <h2>Student Information</h2>
    </header>
    <!-- Back button -->
    <a href="quiz_questions.php?teacherid=<?php echo $teacherid; ?>&title_name=<?php echo urlencode($title_name); ?>" class="back-button" style="font-size: 30px; font-weight: 500;">&#8592;</a>


    <!-- Display alert message if set -->
    <?php if (!empty($alertMessage)): ?>
    <script>alert("<?php echo $alertMessage; ?>");</script>
    <?php endif; ?>
    <form id="addForm" action="<?php echo $_SERVER['PHP_SELF']; ?>?title_name=<?php echo urlencode($title_name); ?>" method="post">
    
    <input type="hidden" name="teacherid" value="<?php echo $teacherid; ?>">
    
    <!-- Input field to set the time limit -->
    <!-- <div class="duration">
        <label for="quiz_duration">Quiz Duration (in minutes):</label><br>
        <input type="number" id="quiz_duration" name="duration" min="1" required><br><br>
    </div> -->
        
    <table>
        <tr>
            <th><button type="submit" class="add-button" name="add_students">Add</button></th> <!-- Placeholder for the checkbox -->
            <th>Name</th>
            <th>Student ID</th>
            <th>Email</th>
            <th>Branch</th>
            <th>Year</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Phone</th>
        </tr>
        <?php
        if (mysqli_num_rows($data) > 0) {
            while ($row = mysqli_fetch_assoc($data)) {
                echo "<tr>";
                // Add a checkbox in the first column
                echo "<td><input type='checkbox' id='checkbox' name='selected_students[]' value='" . $row['studentid'] . "'></td>";
                echo "<td>" . $row['fname'] . "</td>";
                echo "<td>" . $row['studentid'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['branch'] . "</td>";
                echo "<td>" . $row['year'] . "</td>";
                echo "<td>" . $row['dob'] . "</td>";
                echo "<td>" . $row['gender'] . "</td>";
                echo "<td>" . $row['phone'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No records found</td></tr>";
        }
        ?>
    </table>
</form>

</body>
</html>
