<?php
        session_start();
	$_SESSION['first'] = $_POST['first'];
	$_SESSION['last'] = $_POST['last'];
	$_SESSION['email'] = $_POST['email'];
        $_SESSION['psw'] = $_POST['psw'];
        $email = $_SESSION['email'];
?>

<html>
	<head>
                <title> ShapeShift </title>
                <link rel="stylesheet" href="index.css" />
        </head>

        <body style="color:#008080;" >
                <h1 class="welcome">
                        <img class="img1" src="logo.png">
                        WELCOME TO SHAPESHIFT <br>
                        <p class="mantra">SHIFT YOUR HEALTH, SHAPE YOUR FUTURE</p>
                        <h1 class="register"> REGISTRATION </h1>
                        <p align="center">Already have an account?<a href="/login.php"> Login here </a></p>
                </h1>
                <form action="/login2.php" method="post">
                        <label for="first"><b>First Name</b></label>
                        <input type="text" placeholder="Enter First Name" name="first" id="first" required>
                <br><br>
                        <label for="last"><b>Last Name</b></label>
                        <input type="text" placeholder="Enter Last Name" name="last" id="last" required>

                <br><br>
                        <label for="email"><b>Email</b></label>
                        <input type="text" placeholder="Enter Email" name="email" id="email" required>
                <br><br>
                        <label for="psw"><b>Password</b></label>
                        <input type="password" placeholder="Enter Password" name="psw" id="psw" required>
                <br><br>
                        <label for="psw-repeat"><b>Repeat Password</b></label>
                        <input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat" required>
                <br><br>
                        <button type="submit" class="registerbtn">Register</button>
                </form>
        <body>
</html>

