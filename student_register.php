<?php
@include "connect.php";

if(isset($_POST["submit"])){
$fname= $_POST['fname'];
$studentid=$_POST["studentid"];
$email=  $_POST["email"];
$Branch=  $_POST["Branch"];
$year=  $_POST["year"];
$dob=  $_POST["dob"];
$gender=  $_POST["gender"];
$phone=  $_POST["phone"];
$password=  $_POST["password"];
$cpassword=  $_POST["cpassword"] ;

// Check if user already exists
$select="SELECT * FROM register WHERE studentid='$studentid' && password='$password'";
$result=mysqli_query($con, $select);
if (mysqli_num_rows($result)>0) {
  $error[]="user already exist";
}
else{
  if($password!=$cpassword){
    $error[]="password not matched";
    
  }else{
$query= "INSERT INTO  register  values('$fname','$studentid','$email','$Branch','$year','$dob','$gender','$phone','$password','$cpassword')";
$data= mysqli_query ($con,$query);
if($data)
{
  echo '<script> 
  window.location.href="student_login.php";  
   alert("Registration successful!");
</script>';

}else{
  echo '<script> 
     window.location.href="student_login.php";  
      alert(" failed!!!");
 </script>';
 }
}
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
      input[type="date"],
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
        font-size:30px;
      }
  </style>
</head>
<body>

<div class="register">
<form action="#" method="POST" enctype="multipart/form-data">
  
  <h2>Online Examination Registration</h2>
 
    <label for="fullname">Full Name:</label>
    <input type="text" name="fname" class="box" required></br>

    <label for="studentid">Student ID:</label>
    <input type="text" name="studentid" class="box" required></br>

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

  
    <label for="year">Year of Study:</label>
        <select id="year" name="year" class="box" required>
            <option value="">Select Year</option>
            <option value="1">First Year</option>
            <option value="2">Second Year</option>
            <option value="3">Third Year</option>
        </select>

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
        </div><br><br>

    <input type="reset" name="reset"  class="btn" required>
    <input type="submit" name="submit"  class="btn" required>
    
    <p>You don't have account??<a href="student_login.php">login now</a></p>
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
