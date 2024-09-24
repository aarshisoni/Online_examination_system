<?php
@include "connect.php";

// Check if 'studentid' parameter is set in the URL
if(isset($_GET['studentid'])) {
    // Get the value of 'studentid' parameter
    $studentid = $_GET['studentid'];

    // Prepare the query to delete the record with the given studentid
    $query = "DELETE FROM register WHERE studentid='$studentid'";

    // Execute the query
    $data = mysqli_query($con, $query);

    // Check if the query executed successfully
    if($data){ 
        // If successful, echo a JavaScript alert message
        echo "<script>alert('Record deleted');</script>";
    } else {
        // If failed, echo a JavaScript alert message
        echo "<script>alert('Failed to delete');</script>";
    }
} else {
    // If 'studentid' parameter is not provided, echo a JavaScript alert message
    echo "<script>alert('No studentid provided');</script>";
}
?>
