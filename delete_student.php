<?php
// Include database connection file
include 'connect.php';

// Check if student ID is provided in the request
if (isset($_POST['studentid'])) {
    // Sanitize the input to prevent SQL injection
    $studentId = mysqli_real_escape_string($con, $_POST['studentid']);

    // Construct the DELETE query
    $query = "DELETE FROM selected_student WHERE studentid = '$studentId'";

    // Execute the query
    if (mysqli_query($con, $query)) {
        // Deletion successful
        echo "Student deleted successfully.";
    } else {
        // Error occurred during deletion
        echo "Error: " . mysqli_error($con);
    }
} else {
    // Student ID not provided in the request
    echo "Student ID not provided.";
}
?>
