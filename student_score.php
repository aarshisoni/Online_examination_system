<?php
// Start session
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['teacherid'])) {
    // Redirect to login page if not logged in
    header("Location: teacher_login.php");
    exit();
}

// Include database connection file
include "connect.php";

// Fetch teacher ID and title name from the URL parameters
$teacherid = $_GET['teacherid'];
$titleName = $_GET['title_name'];

// Fetch student results for the specific title
$query = "SELECT * FROM student_score WHERE teacherid = '$teacherid' AND title_name = '$titleName'";
$result = mysqli_query($con, $query);

// Check if query was successful and if there are results
if ($result && mysqli_num_rows($result) > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Scores</title>
        <style>
            /* Your CSS styles here */
        </style>
    </head>

    <body>
        <h1>Student Scores for <?php echo $titleName; ?></h1>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through each student result
                while ($row = mysqli_fetch_assoc($result)) {
                    // Fetch student details from register table
                    $studentid = $row['studentid'];
                    $query_student = "SELECT fname, studentid FROM register WHERE studentid = '$studentid'";
                    $result_student = mysqli_query($con, $query_student);
                    $student = mysqli_fetch_assoc($result_student);

                    // Display student name, student ID, and result
                    echo "<tr>";
                    echo "<td>" . $student['fname'] . "</td>";
                    echo "<td>" . $student['studentid'] . "</td>";
                    echo "<td>" . $row['result'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </body>

    </html>
<?php
} else {
    // If no results found
    echo "No student scores available for $titleName.";
}
?>
    