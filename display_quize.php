<?php
// Include database connection file
include 'connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['teacherid'])) {
    // Redirect to login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Fetch admin's name from the admin_register table using session username
$teacherid = $_SESSION['teacherid'];

// Handle title deletion
if (isset($_POST['delete_title'])) {
    $title_to_delete = $_POST['delete_title'];
    
    // SQL query to delete the title from quiz_title table
    $sql_delete_title = "DELETE FROM quiz_title WHERE teacherid = '$teacherid' AND title_name = '$title_to_delete'";
    
    // Execute the query
    if ($con->query($sql_delete_title) === TRUE) {
        echo "<p>Title '$title_to_delete' deleted successfully.</p>";
        // Optionally, you can redirect to the same page or perform any other actions after deletion
    } else {
        echo "Error deleting title: " . $con->error;
    }
}

// Fetch title names associated with the teacher from the quiz_title table
$sql_fetch_titles = "SELECT title_name FROM quiz_title WHERE teacherid = '$teacherid'";

// Execute the query
$result_titles = $con->query($sql_fetch_titles);

// Check if the query was successful
if ($result_titles === false) {
    // Query failed, display error message
    echo "Error fetching title names: " . $con->error;
} else {
    // Check if there are any rows returned
    if ($result_titles->num_rows > 0) {
        
        // Output table header
        echo "<header><h2> Quiz List</h2></header>";
        // Back button
        echo "<a href='admin_dashboard.php?teacherid=$teacherid' class='back-button' style='font-size: 30px; font-weight: 500;'>&#8592;</a>";

        echo "<table>";
        echo "<tr><th>Title Name</th><th>Delete</th></tr>";
        
        
        // Loop through each row to fetch title names
        while ($row_titles = $result_titles->fetch_assoc()) {
            $title_name = $row_titles['title_name'];

            // Output title name as a clickable link
            echo "<tr>";
            echo "<td><a href='quiz_questions.php?teacherid=$teacherid&title_name=$title_name'>$title_name</a></td>"; // Modified link
          
            echo "<td>
                    <form method='post'>
                        <input type='hidden' name='delete_title' value='$title_name'>
                        <button type='submit' class='action-btn delete-btn' onclick='return confirm(\"Are you sure you want to delete this title?\")'>Delete</button>
                    </form>
                  </td>";
            echo "</tr>";
        }   
        echo "</table>";
    } else {
        echo "<p>No conducted quizzes found for this teacher.</p>";
    }
}


// Check if title name is provided in the URL
if (isset($_GET['title_name'])) {
    $title_name = $_GET['title_name'];

    // Add your logic to display details of the selected title name
    // For example, you can query the database for details of the selected title name and display them.
}

// Close the database connection
$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display All Titles</title>
    <style>
        
        body {
            font-family: Arial;
        }

        header{ 
            background-image: linear-gradient(100deg,white, rgb(184, 149, 149));
            background-color: rgb(184, 149, 149);
        }

        h2{
            font-size:35px;
            padding:1% 0 1% 0;
            text-align: center;
            margin-top: 15px;
            margin-bottom:0;
        }
        .container {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .column {
            width: 50%;
            margin-right: 2%;
        }
        table {
            font-family: arial;
            border-collapse: collapse;
            width: 50%;
            margin:2% 0 0 20%
        }
        th, td {
            font-size:20px;
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            justify-content:20px;
        }
        .action-btn-container {
            display:inline-block;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .action-btn {
            color: white;
            border: none;
            padding: 6px 10px;
            cursor: pointer;
            margin-bottom: 5px;
        }
        .delete-btn {
            background-color: #f44336;
            border:2px solid red;
            border-radius:10px;
            justify-content:center;
        }
        .delete-btn:hover{
            background-color: red;
        }

        .back-button {
            display: inline-block;
            background-color: #008CBA;
            color: white;
            margin: 1% 0 30px 5%;
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
    <div class="container">
        <div class="column">
            <!-- JavaScript functions -->
            <script>
                function deleteTitle(title_name) {
                    var confirmDelete = confirm("Are you sure you want to delete this title?");
                    if (confirmDelete) {
                        // Submit the form for deletion
                        document.getElementById('delete-form').querySelector('input[name="delete_title"]').value = title_name;
                        document.getElementById('delete-form').submit();
                    }
                }
            </script>

            <!-- PHP form for title deletion -->
            <form id="delete-form" method="post">
                <input type="hidden" name="delete_title" value="">
            </form>
        </div>
    </div>
</body>
</html>