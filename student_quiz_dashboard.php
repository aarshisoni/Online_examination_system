
<?php
// Start session
session_start();

// Check if student is logged in
if (!isset($_SESSION['studentid'])) {
    // Redirect to login page if not logged in
    header("Location: student_login.php");
    exit();
}

// Include database connection file
include "connect.php";

// Fetch student's name from the register table using session username
$studentid = $_SESSION['studentid'];
$query = "SELECT fname FROM register WHERE studentid = '$studentid'";
$result = mysqli_query($con, $query);

// Check if query was successful and if a row was returned
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the first row from the result set
    $row = mysqli_fetch_assoc($result);
    // Store the student's name in a variable
    $studentName = $row['fname'];
} else {
    // Default student name if query fails or no row is returned
    $studentName = "Student";
}

// Fetch title_name and teacherid from selected_student table
$query_info = "SELECT title_name, teacherid FROM selected_student WHERE studentid = '$studentid'";
$result_info = mysqli_query($con, $query_info);

// Check if query was successful and if a row was returned
if ($result_info && mysqli_num_rows($result_info) > 0) {
    // Fetch the first row from the result set
    $row_info = mysqli_fetch_assoc($result_info);
    // Store the title_name and teacherid in variables
    $titleName = $row_info['title_name'];
    $teacherId = $row_info['teacherid'];
} else {
    // Default values if query fails or no row is returned
    $titleName = "Title";
    $teacherId = "Teacher ID";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="student_dashboardcss.css"> -->
    <script src="https://kit.fontawesome.com/5ec31c46d7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css" integrity="sha384-BY+fdrpOd3gfeRvTSMT+VUZmA728cfF9Z2G42xpaRkUGu2i3DyzpTURDo5A6CaLK" crossorigin="anonymous">
    <title><?php echo $studentName; ?></title>
    <style>
        /* Your CSS styles here */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial;
            /* Added a fallback font */
        }
        /* responsive */
        
        @media screen and (max-width:1050px) {
            .side-menu li {
                font-size: 20px;
            }
        }
        
        @media screen and (max-width:940px) {
            .side-menu li span {
                display: none;
            }
        }
        /* Global Styles */
        
        body {
            min-height: 100vh;
        }
        
        a {
            text-decoration: none;
        }
        /* Header Styles */
        
        header {
            height: 80px;
            color: white;
            background-color: rgb(184, 149, 149);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }
        
        .menu_bar {
            cursor: pointer;
            font-size: 24px;
            display: flex;
        }
        
        h3 {
            color: #fff;
            font-weight: bold;
        }
        
        .side-menu {
            background: rgb(184, 149, 149);
            width: 20vw;
            min-height: calc(100vh - 60px);
            /* Adjusted height */
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Center items vertically */
            transition: transform 0.3s ease;
            /* Added transition */
        }
        
        .side-menu.collapsed {
            transform: translateX(-100%);
        }
        
        .side-menu ul {
            list-style: none;
            /* Remove bullet points */
            padding: 10px 0;
            /* Adjusted padding */
        }
        
        .side-menu li {
            font-size: 18px;
            padding: 10px 0 15px 20px;
            /* Adjusted padding */
            color: #fff;
            cursor: pointer;
            width: 20vw;
            transition: width 0.3s ease;
            /* Added transition */
        }
        
        .side-menu li:hover {
            background-color: white;
        }
        
        .fa-solid {
            position: relative;
            color: #345e70;
            margin-right: 8px;
            /* Adjusted margin */
            font-size: 20px;
            text-decoration: none;
        }
        /* Admin Container Styles */
        
        .student_container {
            position: relative;
            display: flex;
            flex-direction: row;
            /* Align items side by side */
            /* justify-content: flex-end; Align items to the right */
            height: 100vh;
        }
        /* Admin Header Styles */
        
        .student_header {
            width: 80vw;
            height: 650px;
            /* Reduced height */
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
        }
        
        .student_header .nav {
            width: 90%;
            display: flex;
            align-items: center;
        }
    </style>
</head>

<body>
    <header>
        <div class="menu_bar" id="menuToggle">
            <h3><i class="fas fa-bars"></i>&nbsp;&nbsp;Student Dashboard</h3>
        </div>
    </header>
    <div class="student_container" id="studentContainer">
        <div class="side-menu" id="sideMenu">
            <ul>
                <li><a href="student_start.php?studentid=<?php echo $studentid; ?>"><i class="fas fa-pencil-alt"></i>&nbsp;<span><?php echo $titleName; ?></span></a></li>

            </ul>
        </div>
        <div class="student_header">
            <div class="nav">
                <!-- Your navigation content here -->
            </div>
        </div>
    </div>
    <script>
        document.getElementById("menuToggle").addEventListener("click", function() {
            document.getElementById("sideMenu").classList.toggle("collapsed");
        });
    </script>
</body>

</html>
