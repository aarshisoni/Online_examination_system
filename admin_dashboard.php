<?php
// Start session
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
$query = "SELECT fname FROM admin_register WHERE teacherid = '$teacherid'";
$result = mysqli_query($con, $query);

// Check if query was successful and if a row was returned
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the first row from the result set
    $row = mysqli_fetch_assoc($result);
    // Store the admin's name in a variable
    $adminName = $row['fname'];
} else {
    // Default admin name if query fails or no row is returned
    $adminName = "Admin";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="admin_dashboardcss.css"> -->
    <script src="https://kit.fontawesome.com/5ec31c46d7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css" integrity="sha384-BY+fdrpOd3gfeRvTSMT+VUZmA728cfF9Z2G42xpaRkUGu2i3DyzpTURDo5A6CaLK" crossorigin="anonymous">
    <title><?php echo $adminName; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial;
        }


        /* Global Styles*/

        body {
            min-height: 100%;
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

        .container {
            display: flex;
            font-size: 18px;
            /* margin-left: 1000px; */
        }

        .navbar {
            margin: auto;
            padding: 0.5rem 1.5rem;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            color: rgb(255, 255, 255);
            list-style: none;
        }

        .side-menu {
            background: rgb(184, 149, 149);
            width: 20%;
            min-height: calc(100px - 60px);
            /* Adjusted height */
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Center items vertically */
            transition: transform 0.3s ease;
            /* Added transition */
        }

        .side-menu.collapsed {
            /* x for horizontal */
            transform: translateX(-100%);
        }

        .side-menu ul {
            list-style: none;
            padding: 10px 0;
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

        .admin_container {
            position: relative;
            display: flex;
            flex-direction: row;
            /* Align items side by side */
            /* justify-content: flex-end; Align items to the right */
            height: 100%;
        }


        /* Admin Header Styles */

        .admin_header {
            width: 80%;
            height: 650px;
            /* Reduced height */
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.1);
        }

        .admin_header .nav {
            width: 90%;
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="menu_bar" id="menuToggle">
            <h3><i class="fas fa-bars"></i>&nbsp;&nbsp;<?php echo $adminName; ?></h3>
            <div class="container">
                <nav class="navbar">
                    <ul class="nav-links">
                        <li class="nav-link">
                            <!-- <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a> -->
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <div class="admin_container" id="adminContainer">
        <div class="side-menu" id="sideMenu">
            <ul>
                <li><a href="admin_profile.php?teacherid=<?php echo $teacherid; ?>"><i class="fas fa-user"></i>&nbsp;<span>My Profile</span></a></li>
                <li><a href="new_title.php?teacherid=<?php echo $teacherid; ?>"><i class="fas fa-clipboard"></i>&nbsp;<span>Conduct Exam</span></a></li>
                <li><a href="display_quize.php?teacherid=<?php echo $teacherid; ?>"><i class="fas fa-file-lines"></i>&nbsp;<span>quiz list</span></a></li>
                <li><a href="admin_list.php?teacherid=<?php echo $teacherid; ?>"><i class="fas fa-tags"></i>&nbsp;<span>Admin List</span></a></li>
                <li><a href="student_list.php?teacherid=<?php echo $teacherid; ?>"><i class="fas fa-user-graduate"></i>&nbsp;<span>Manage Student Details</span></a></li>
                <!-- <li><a href="student_score_dashboard.php?teacherid=<?php echo $teacherid; ?>"><i class="fas fa-graduation-cap"></i>&nbsp;<span>Student Score</span></a></li> -->
                <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i>&nbsp; Logout</a></li>
            </ul>
        </div>
        <div class="admin_header">
            <div class="nav">
            </div>
        </div>
    </div>
    <!-- <script>
        document.getElementById("menuToggle").addEventListener("click", function() {
            document.getElementById("sideMenu").classList.toggle("collapsed");
        });
    </script> -->
</body>
</html>
