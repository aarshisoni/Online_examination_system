<?php
session_start();


if (!isset($_SESSION['teacherid'])) {
    
    header("Location: login.php");
    exit();
}


include "connect.php";


$teacherid = $_SESSION['teacherid'];
$query = "SELECT * FROM admin_register WHERE teacherid = '$teacherid'";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    
    $row = mysqli_fetch_assoc($result);
 
    $fullname = $row['fname'];
    $teacherid = $row['teacherid'];
    $gender = $row['gender'];
    $dob = $row['dob'];
    $email = $row['email'];
    $branch = $row['branch'];
    $phone = $row['phone'];
   
} else {
    
    $error = "Failed to fetch user data";
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated information from the form
    $new_fullname = $_POST['fullname'];
    $new_email = $_POST['email'];
    $new_dob = $_POST['dob'];
    $new_gender = $_POST['gender'];
    $new_branch = $_POST['branch'];
    $new_phone = $_POST['phone'];
    
    // Update user's profile 
    $update_query = "UPDATE admin_register SET fname='$new_fullname', email='$new_email', dob='$new_dob', gender='$new_gender', branch='$new_branch', phone='$new_phone' WHERE teacherid='$teacherid'";
    $update_result = mysqli_query($con, $update_query);
    
    if ($update_result) {
        // Redirect to profile page after successful update
        header("Location: admin_profile.php");
        exit();
    } else {

        $error = "Failed to update profile information";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    
</head>
<style>
/* Your CSS styles here */
</style>
<body>
    <div class="container">
        <h2><?php echo $fullname; ?>'s Profile</h2>
        <?php if(isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php else: ?>
            <form method="post">
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo $fullname; ?>"><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>"><br>
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo $dob; ?>"><br>
                <label for="gender">Gender:</label>
                <input type="text" id="gender" name="gender" value="<?php echo $gender; ?>"><br>
                <label for="branch">Branch:</label>
                <input type="text" id="branch" name="branch" value="<?php echo $branch; ?>"><br>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>"><br>
                <input type="submit" value="Update Profile">
            </form>
        <?php endif; ?>
        
    </div>
</body>
</html>