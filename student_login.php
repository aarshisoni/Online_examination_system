<?php
include("connect.php");
session_start();
if(isset($_POST["submit"])) {
    $studentid = $_POST["studentid"]; // Corrected variable name
    $password = $_POST["password"];

    $select = "SELECT * FROM register WHERE studentid='$studentid' && password='$password'"; // Corrected variable name
    $result = mysqli_query($con, $select);
    $total = mysqli_num_rows($result);
    if($total == 1) {
        $_SESSION['studentid'] = $studentid;
        // Redirect to student_dashboard.php with student ID as query parameter
        header("Location: student_dashboard.php?studentid=$studentid");
        exit();
    } else {
        header("Location: student_login.php");
        exit();
    }
}
?>
<html>
    <head>
    <title>login page</title>
    <style>
        body {
            background-color: rgb(235, 210, 210);
            margin: 0;
            padding: 0;
            font-family: Arial;
        }

        .login {
            background-color: white;
            width: 25%;
            margin: 120px auto;
            padding: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.7);
            border-radius: 8px;
        }

        .login h2 {
            text-align: center;
        }

        .login label {
            font-size: 20px;
        }

        .login .box {
            height: 30px;
            margin-bottom: 15px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn {
            color: white;
            background-color: #037808;
            padding: 10px;
            font-size: large;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #229027;
        }

        .login p {
            font-size: 18px;
            margin-top: 15px;
            text-align: center;
        }

        .login p a {
            text-decoration: none;
            color: #037808;
        }

        .login p a:hover {
            text-decoration: underline;
        }

        
        .password-container {
            position: relative;
            display: inline-block;
        }

        #showPasswordIcon {
            position: absolute;
            top: 35%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login">
    <form action="#" method="POST" enctype="multipart/form-data">
    <h2>LOGIN NOW</h2>
    <label>Student ID: </label>
    <input type="text" name="studentid" placeholder="Student ID" class="box" required><br><br>
    <div class="password-container">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Password" id="spassword" class="box" required>
                <img id="showPasswordIcon" onclick="togglePasswordVisibility()" src="show.png" style="width: 25px; height: 25px;">
            </div><br><br>
    <input type="submit" name="submit" value="Login" class="btn">
    <p>You don't have an account? <a href="user_register.php">Register now</a></p>
</form>
</div>

<script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("spassword");
            var icon = document.getElementById("showPasswordIcon");

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
