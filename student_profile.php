<?php
// Include necessary files and start session
include "connect.php";
session_start();

// Check if the user is logged in
if (!isset($_SESSION['studentid'])) {
    // Redirect to login page if not logged in
    header("Location: student_login.php");
    exit();
}

// Fetch user's profile information from the database
$studentid = $_SESSION['studentid'];
$query = "SELECT * FROM register WHERE studentid = '$studentid'";
$result = mysqli_query($con, $query);

// Check if query was successful and if a row was returned
if ($result && mysqli_num_rows($result) > 0) {
    // Fetch the first row from the result set
    $row = mysqli_fetch_assoc($result);
    // Store the user's profile information in variables
    $fullname = $row['fname']; 
    $gender = $row['gender'];
    $dob = $row['dob'];
    $email = $row['email'];
    $branch = $row['branch'];
    $phone = $row['phone'];
} else {
    $error = "Failed to fetch user data";
}


// Check if form is submitted for updating information
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve updated information from the form
    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $branch = $_POST['branch'];
    $phone = $_POST['phone'];

    // Update the information in the database
    $update_query = "UPDATE register SET fname='$fullname', gender='$gender', dob='$dob', email='$email', branch='$branch', phone='$phone' WHERE studentid='$studentid'";
    $update_result = mysqli_query($con, $update_query);
    if ($update_result) {

        $_SESSION['update_success'] = true;
        // Redirect to the same page to refresh the profile information
        header("Location: student_profile.php");
        exit();
    } else {
        // Handle error if update fails
        $error = "Failed to update information";
    }
}

// Check if there was a successful update and display alert message
$update_success_message = '';
if (isset($_SESSION['update_success']) && $_SESSION['update_success']) {
    $update_success_message = "<script>alert('Record updated successfully');</script>";
    unset($_SESSION['update_success']); // unset the session variable to prevent the message from displaying again on page refresh
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Font Awesome library -->
    <script src="https://kit.fontawesome.com/5ec31c46d7.js" crossorigin="anonymous"></script>
    <title>User Profile</title>

    <style>
        body {
            font-family: Arial;
            background-color: rgb(235, 210, 210);
        }

        h2 {
            color: red;
        }

        .container {
            width: 60%;
            font-size: 20px;
            margin: 6% auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.7);
        }

        table {
            border-collapse: separate;
            text-indent: initial;
            border-spacing: 2px;
            width: 70%;
        }

        tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
            font-size: 19px;
            border: 10px solid grey;
            text-align: left;
            border-radius: 10px;

        }

        td {
            margin-right: 5px;
            padding: 7px;
        }

        .container a {
            text-decoration: none;
            color: #007bff;
        }

        .container a:hover {
            text-decoration: underline;
            color:red;
        }

        input {
            border: none;
            vertical-align: inherit;
            border-color: inherit;
            font-size: 19px;
            color: gray;
            text-align: left;

        }

        button {
            margin-top: 10px;
            background-color:rgb(80, 170, 178);
            cursor: pointer;
            color: white;
            padding: 10px;
            font-size: large;
            border-radius: 5px;
        }

        button:hover {
            background-color: #008CBA;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><?php echo $fullname; ?>'s Profile</h2>
        <?php echo $update_success_message; ?>
        <?php if (isset($error)) : ?>
            <p><?php echo $error; ?></p>
        <?php else : ?>
            <form method="post">
                <table class="Form_table">
                    <tbody>
                        <tr>
                            <td><strong>Full Name:</strong></td>
                            <td style="color:gray">
                                <input type="text" name="fullname" value="<?php echo $fullname; ?>">
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Student Id:</strong></td>
                            <td style="color:gray">
                                <input type="text" name="studentid" value="<?php echo $studentid; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong> </td>
                            <td style="color:gray">
                                <input type="email" name="email" value="<?php echo $email; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Date of Birth:</strong> </td>
                            <td style="color:gray">
                                <input type="text" name="dob" value="<?php echo $dob; ?>">
                            </td>
                        </tr>

                        <tr>
                            <td><strong>Gender:</strong></td>
                            <td style="color:gray">
                                <input type="text" name="gender" value="<?php echo $gender; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Branch:</strong> </td>
                            <td style="color:gray">
                                <input type="text" name="branch" value="<?php echo $branch; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong> </td>
                            <td style="color:gray">
                                <input type="text" name="phone" value="<?php echo $phone; ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button>Update</button>
            </form>
            <?php endif; ?>
        <!-- Logout link with Font Awesome icon -->
        <p><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></p>
    </div>
</body>

</html>