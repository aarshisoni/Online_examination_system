<?php
@include "connect.php";
error_reporting(0);

if(isset($_POST["submit"])){
    $fname = $_POST['fname'];
    $teacherid = $_POST["teacherid"];
    $email = $_POST["email"];
    $branch = $_POST["Branch"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    // Check if user already exists
    $select = "SELECT * FROM admin_register WHERE teacherid='$teacherid' && password='$password'";
    $result = mysqli_query($con, $select);
    if (mysqli_num_rows($result) > 0) {
        echo '<script> 
                      window.location.href="adm_register.php";  
                      alert("User already exists!");
                      </script>';
    } else {
        // Check if passwords match
        if ($password != $cpassword){
            echo '<script> 
                        window.location.href="adm_register.php";
                        alert("Passwords do not match!");
                      </script>';
        } else {
            // Insert new user
            $insert = "INSERT INTO admin_register (fname, teacherid, email, branch, dob, gender, phone, password, cpassword) VALUES ('$fname', '$teacherid', '$email', '$branch', '$dob', '$gender', '$phone', '$password', '$cpassword')";
            $result = mysqli_query($con, $insert);
            if ($result) {
                echo '<script> 
                        window.location.href="admin_login.php";  
                        alert("Registration successful!");
                      </script>';
            } else {
                $error[] = "Failed to insert data: " . mysqli_error($con);
            }
        }
    }
}

// // Debugging: Display any errors
// if (isset($error)) {
//     foreach($error as $err) {
//         echo $err . "<br>";
//     }
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Examination Registration</title>
  <style>
        body {
            background-color: rgb(235, 210, 210);
            font-family: Arial;
        }

        .register {
            padding: 20px;
            margin: 100px auto;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.7);
            max-width: 400px;
            background-color: #fff;
        }

        .register h2 {
            text-align: center;
        }

        .register form {
            margin-top: 20px;
        }

        .register label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[ type="date"],
        input[type="file"] {
            width: 370px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #gender {
            width: 390px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select {
            width: 390px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .register .btn {
            color: white;
            width: 140px;
            cursor: pointer;
            background-color: #037808;
            margin-top: 15px;
            margin-left: 40px;
            padding: 10px;
            font-size: large;
            border-radius: 5px;
        }

        .register .btn:hover {
            background-color: #229027;
        }

        .register p {
            font-size: 18px;
            margin-top: 15px;
            text-align: center;
            margin-top: 20px;
        }

        .register p a {
            text-decoration: none;
        }

        .register p a:hover {
            text-decoration: underline;
        }

        .password-container {
        position: relative;
        display: inline-block;
        }

        .showPasswordIcon {
        position: absolute;
        top: 60%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        }
  </style>
</head>
<body>

<div class="register">
<form action="#" method="POST" enctype="multipart/form-data">

  <h2>Online Examination Registration</h2>

    <label for="fullname">Full Name:</label>
    <input type="text" name="fname" class="box" required></br>

    <label for="teacherid">Teacher ID :</label>
    <input type="text" name="teacherid" class="box" required></br>

    <label for="email">Email Address:</label>
    <input type="email"  name="email" placeholder="someone@gmail.com" class="box" required></br>

    <label for="Branch">Branch</label>
    <select id="Branch" name="Branch" class="box" required>
      <option value="">Select Branch</option>
      <option value="CSE">CSE</option>
      <option value="Electrical">Electrical</option>
      <option value="Civil">Civil</option>
      <option value="mechanical">mechanical</option>
    </select></br>

    <label for="dob">Date of Birth:</label>
    <input type="date"  name="dob" class="box" required>

    <label for="gender">Gender:</label>
    <select id="gender" name="gender" class="box" required>
      <option value="">Select Gender</option>
      <option value="male">Male</option>
      <option value="female">Female</option>
    </select>

    <label for="phone">Contact Number:</label>
    <input type="text"  name="phone" class="box" required>
   
    <div class="password-container">
            <label>Password:</label>
            <input type="password" name="password" id="password" class="spassword" pattern="{8,}" title="At least 8 or more characters"   placeholder="Password" class="box" required>
            <img id="passwordIcon" class="showPasswordIcon" onclick="togglePasswordVisibility('password', 'passwordIcon')" src="show.png" style="width: 25px; height: 25px;">
        </div>

        <div class="password-container">
            <label>Confirm Password:</label>
            <input type="password" name="cpassword" id="cpassword" class="spassword" pattern="{8,}" title="At least 8 or more characters"  placeholder="Confirm Password" class="box" required>
            <img id="cpasswordIcon" class="showPasswordIcon" onclick="togglePasswordVisibility('cpassword', 'cpasswordIcon')" src="show.png" style="width: 25px; height: 25px;"> 
        </div>
            
            <br><br>
    
    <input type="reset" name="reset"  class="btn" required>
    <input type="submit" name="submit"  class="btn" required>
    <p>You don't have account??<a href="admin_login.php">login now</a></p>
  </form>
</div>

<script>

   function togglePasswordVisibility(passwordId, iconId) {
    var passwordInput = document.getElementById(passwordId);
    var icon = document.getElementById(iconId);

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.src = "hide.png"; // Change the image source to hide icon
    } else {
        passwordInput.type = "password";
        icon.src = "show.png"; // Change the image source to show icon
    }
}

</script>

</body>
</html>
