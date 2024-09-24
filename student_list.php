
<?php
// Include your configuration file to establish a database connection
include("connect.php");
error_reporting(0);
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['teacherid'])) {
    // Redirect to login page if not logged in
    header("Location: admin_login.php");
    exit();
}

// Fetch teacher's ID from session
$teacherid = $_SESSION['teacherid'];
$title_name = isset($_GET['title_name']) ? $_GET['title_name'] : '';

// Initialize variables for search query and filters
$search_query = "";
$filter_branch = "";
$filter_year = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    // Get search value from form
    $search_query = $_POST["search_query"];
    // Get filter values from form
    $filter_branch = $_POST["filter_branch"];
    $filter_year = $_POST["filter_year"];

    // Modify the SQL query to include search and filter conditions
        $query = "SELECT * FROM register WHERE 1=1"; // Start with a basic query

        if (!empty($search_query)) {
            // Add search condition
            $query .= " AND (fname LIKE '%$search_query%' OR studentid LIKE '%$search_query%')";
        }

        if (!empty($filter_branch) && !empty($filter_year)) {
            // Add branch and year filter conditions
            $query .= " AND branch = '$filter_branch' AND year = '$filter_year'";
        } elseif (!empty($filter_branch)) {
            // Add branch filter condition
            if ($filter_branch == 'CSE') {
                // Modify branch value to match database value
                $filter_branch = 'CSE';
            }
            $query .= " AND branch = '$filter_branch'";
        } elseif (!empty($filter_year)) {
            // Add year filter condition
            $query .= " AND year = '$filter_year'";
        }

} else {
    // Fetch all data if form is not submitted
    $query = "SELECT * FROM register";
}

$data = mysqli_query($con, $query);

if (!$data) {
    // Query execution failed
    echo "Error: " . mysqli_error($con);
    exit;
}

$total = mysqli_num_rows($data);

// Fetch selected students along with their details
$query = "SELECT register.fname, register.studentid, register.email, register.branch, register.year, register.dob, register.gender, register.phone
          FROM selected_student
          INNER JOIN register ON selected_student.studentid = register.studentid
          WHERE selected_student.teacherid = '$teacherid' AND selected_student.title_name = '$title_name'";

// Check if a branch filter is applied
if (isset($_GET['branch']) && !empty($_GET['branch'])) {
    $branchFilter = $_GET['branch'];
    // Add branch filter to the query
    $query .= " AND register.branch = '$branchFilter'";
}

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record</title>
    <style>

        body {
            font-family: Arial;
        }

        header{ 
            background-image: linear-gradient(100deg,white, rgb(184, 149, 149));
            background-color: rgb(184, 149, 149);
        }

        a.update {
            border: 0;
            outline: none;
            border-radius: 5px;
            height: 20%;
            width: 25%;
            margin-left: 10px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            /* padding: 2px 2px 2px 0; */
            display: inline-block;
            background-color:rgb(80, 170, 178);
            color: white;
            padding: 5% 8% 5% 6%;
        }

        a.update:hover {
            background-color: #008CBA;
        }

        a.delete {
            border: 0;
            outline: none;
            border-radius: 5px;
            height: 20%;
            width: 25%;
            margin-left: 20px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            /* padding: 2px 2px 2px 0; */
            display: inline-block;
            background-color:#f44336;
            color: white;
            padding: 10px 8px 10px 12px;
        }

        a.delete:hover{
            background-color: red;
        }

        h2{
            font-size:35px;
            text-align:center;
            padding:1% 0 1% 0;
            margin-top:15px;
        }

        .year{
            margin-left: 30px;
            /* text-align:center; */
        }

        label {
            margin-bottom: 5px;
            font-size: large;
            text-align: center;
        }

        .box {
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 200px;
            margin-bottom: 10px;
        }

        .box:focus,
        .btn:focus
        .add:focus {
            outline: none;
            border-color:black;
            box-shadow: 0 0 2px black;
        }

        .option {
            padding: 5px;
        }
        .btn{
            background-color:gainsboro;
            cursor: pointer;
            color: black;
            margin-left: 10px;
            height: 30px;
            width: 70px;
            outline: none; }

            .add{
                background-color: rgb(1, 4, 5);
            }

   
           table {
               font-family: arial;
               border-collapse: collapse;
               width: 90%;
               margin:5% 0 0 3%
           }
   
            td, 
           th,
           tr {
               border: 2px solid grey;
               text-align: left;
               padding: 8px;
              
           }
           th{
                background-color: #dfdfdf;
               text-align: center; 
           }
   
           tr:nth-child(even) {
               background-color: #dddddd;
           } 
   
           input[type="checkbox"],button{
           width: 50px;
           height: 17px;
           cursor: pointer;   
       }

       input[type="text"],
        select {
            width: 200px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 16px;
        }

        /* CSS for search button */
        button[type="submit"] {
            width:10%;
            height:6%;
            background-color: #4CAF50; /* Green background */
            color: white; /* White text */
            padding: 10px 20px; /* Padding */
            border: none; /* No border */
            border-radius: 4px; /* Rounded corners */
            cursor: pointer; /* Cursor style */
            font-size: 16px; /* Font size */
        }

        /* Hover effect */
        button[type="submit"]:hover {
            background-color: #45a049; /* Darker green */
        }

        .back-button {
            display: inline-block;
            background-color: #008CBA;
            color: white;
            margin: 0 0 2% 5%;
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
    <h2 align="center">Student Record</h2>
    </header>   
    <!-- Back button -->
    <a href="admin_dashboard.php?teacherid=<?php echo $teacherid; ?>&title_name=<?php echo urlencode($title_name); ?>" class="back-button" style="font-size: 30px; font-weight: 500;">&#8592;</a>

    <!-- Add a search form -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="search_query">Search by Name or Student ID:</label>
        <input type="text" id="search_query" name="search_query" value="<?php echo $search_query; ?>">
        <label for="filter_branch">Filter by Branch:</label>
        <select id="filter_branch" name="filter_branch">
            <option value="">All</option>
            <option value="CSE" <?php if ($filter_branch == 'CSE') echo 'selected'; ?>>CSE</option>
            <option value="Electrical" <?php if ($filter_branch == 'Electrical') echo 'selected'; ?>>Electrical</option>
            <option value="Civil" <?php if ($filter_branch == 'Civil') echo 'selected'; ?>>Civil</option>
            <option value="Mechanical" <?php if ($filter_branch == 'Mechanical') echo 'selected'; ?>>Mechanical</option>
            <!-- Add more options as needed -->
        </select>
        <label for="filter_year">Filter by Year:</label>
        <select id="filter_year" name="filter_year">
            <option value="">All</option>
            <option value="1" <?php if ($filter_year == '1') echo 'selected'; ?>>First Year</option>
            <option value="2" <?php if ($filter_year == '2') echo 'selected'; ?>>Second Year</option>
            <option value="3" <?php if ($filter_year == '3') echo 'selected'; ?>>Third Year</option>
            <!-- Add more options as needed -->
        </select>
        <button type="submit">Search</button>
    </form>

    <?php if ($total != 0) { ?> 
        <table class="table-border">
            <thead>
                <tr>
                    <th width="12%">Full name</th>
                    <th width="12%">StudentId</th>
                    <th width="16%">E-mail</th>
                    <th width="15%">Branch</th>
                    <th width="4%">Year</th>
                    <th width="9%">DOB</th>
                    <th width="6%">Gender</th>
                    <th width="7%">Phone</th>
                    <th width="20%">Operation</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($result = mysqli_fetch_assoc($data)) { ?>
                    <tr>
                        <td><?php echo $result['fname']; ?></td>
                        <td><?php echo $result['studentid']; ?></td>
                        <td><?php echo $result['email']; ?></td>
                        <td><?php echo $result['branch']; ?></td>
                        <td><?php echo $result['year']; ?></td>
                        <td><?php echo $result['dob']; ?></td>
                        <td><?php echo $result['gender']; ?></td>
                        <td><?php echo $result['phone']; ?></td>
                        <td>
                            <a class="update" href="studentList_update.php?studentid=<?php echo $result['studentid']; ?>">Update</a>
                            <a class="delete" href="Delete.php?studentid=<?php echo $result['studentid']; ?>" onclick="return checkdelete()">Delete</a>
                        </td>
                    </tr>
                <?php 
            } ?>
            </tbody>
        </table>
    <?php } else {
        echo "Record not found";
    } ?>

    <script>
        function checkdelete() {
            return confirm('Are you sure you want to delete this record?');
        }

        function applyBranchFilter() {
    // Get the selected branch value
    var branchFilter = document.getElementById("branchFilter").value;
    // Redirect to the page with the selected branch filter
    window.location.href = "selected_student_list.php?teacherid=<?php echo $teacherid; ?>&title_name=<?php echo urlencode($title_name); ?>&branch=" + branchFilter;
}

    </script>
</body>
</html>
