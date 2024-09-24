<?php
@include "connect.php";

// Check if 'studentid' parameter is set in the URL
if(isset($_GET['studentid'])) {
    // Get the value of 'studentid' parameter
    $studentid = $_GET['studentid'];

    // Fetch user data from the database based on the student ID
    $query = "SELECT * FROM register WHERE studentid='$studentid'";
    $data = mysqli_query($con, $query);

    // Check if the query returned any data
    if(mysqli_num_rows($data) > 0) {
        // Fetch the data into an associative array
        $result = mysqli_fetch_assoc($data);

        // Assign fetched data to variables
        $fullname = $result['fname'];
        $email = $result['email'];
        $dob = $result['dob'];
        $gender = $result['gender'];
        $branch = $result['branch'];
        $phone = $result['phone'];
    } else {
        echo "No data found for the provided student ID.";
        // You can redirect or handle this case according to your requirements
        exit(); // Stop further execution
    }
} else {
    echo "No student ID provided.";
    // You can redirect or handle this case according to your requirements
    exit(); // Stop further execution
}

// Check if form is submitted for update
if(isset($_POST["update"])){
    // Retrieve updated data from the form
    $fname = $_POST['fname'];
    $email = $_POST["email"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $branch = $_POST["branch"];
    $phone = $_POST["phone"];

    // Update query
    $query = "UPDATE register SET fname='$fname', email='$email', dob='$dob', gender='$gender', branch='$branch', phone='$phone' WHERE studentid='$studentid'";
    $data = mysqli_query($con, $query);

    if($data) {
        echo '<script> 
            window.location.href="student_list.php?studentid=' . $studentid . '";  
            alert("Record Updated Successfully!!!");
        </script>';
    } else {
        echo '<script> 
            window.location.href="studentList_update.php?studentid=' . $studentid . '";  
            alert("Failed to Update Record!!!");
        </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
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
            color: #0056b3;
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
            background-color: #0056b3;
            cursor: pointer;
            color: white;
            padding: 10px;
            font-size: large;
            border-radius: 5px;
        }

        button:hover {
            background-color: #004080;
            /* Darker background color on hover */
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><?php echo $fullname; ?>'s Profile</h2>
        <form method="post">
            <table class="Form_table">
                <tbody>
                    <tr>
                        <td><strong>Full Name:</strong></td>
                        <td style="color:gray">
                            <input type="text" name="fname" value="<?php echo $fullname; ?>">
                        </td>
                    </tr>
                    <tr>
                        <!-- Hidden input field to pass studentid -->
                        <input type="hidden" name="studentid" value="<?php echo $studentid; ?>">
                        <td><strong>Student Id:</strong></td>
                        <td style="color:gray">
                            <input type="text" name="studentid" value="<?php echo $studentid; ?>" readonly>
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
            <button type="submit" name="update">Update</button>
        </form>
    </div>
</body>

</html>
