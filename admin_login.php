<?php
include("connect.php");
session_start();
$error_msg = "";

if(isset($_POST["submit"])){
    $teacherid = trim($_POST["teacherid"]);
    $password = $_POST["password"];

    $stmt = $con->prepare("SELECT * FROM admin_register WHERE teacherid = ? AND password = ?");
    $stmt->bind_param("ss", $teacherid, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows == 1){
        $_SESSION['teacherid'] = $teacherid;
        header("Location: admin_dashboard.php?teacherid=" . urlencode($teacherid));
        exit();
    } else {
        $error_msg = "Invalid Teacher ID or Password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login page</title>
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
            top: 36%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login">
        <form action="#" method="POST" enctype="multipart/form-data">
            <h2>TEACHER LOGIN</h2>
            <?php if (!empty($error_msg)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>
            <label>Teacher ID:</label>
            <input type="text" name="teacherid" placeholder="Teacher ID" class="box" required><br><br>
            <div class="password-container">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Password" id="spassword" class="box" required>
                <img id="showPasswordIcon" onclick="togglePasswordVisibility()" src="show.png" style="width: 25px; height: 25px;">
            </div><br><br>
            <input type="submit" name="submit" value="Login" class="btn">
            <p>You don't have an account? <a href="adm_register.php">Register now</a></p>
            <p><a href="MainPage.html">&#8592; Back to Home</a></p>
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
