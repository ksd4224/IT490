hp
        session_start();
        $_SESSION['first'] = $_POST['first'];
        $_SESSION['last'] = $_POST['last'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['psw'] = $_POST['psw'];
        $email = $_SESSION['email'];
        include 'write.php';
?>

<html>
<head>
    <title>ShapeShift</title>
    <link rel="stylesheet" href="index.css">
</head>
<body style="color:#008080;">
    <h1 class="welcome">
        <img class="img1" src="logo.png">
        LOGIN TO SHAPESHIFT <br>
        <p class="mantra">SHIFT YOUR HEALTH, SHAPE YOUR FUTURE</p>
        <h1 class="register">LOGIN</h1>
    </h1>
    <form action="test.php" method="post">
        <label for="email"><b>Email</b></label>
        <input type="text" placeholder="Enter Email" name="email" id="email" required>
        <br><br>
        <label for="psw"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="psw" id="psw" required>
        <br><br>
        <button type="submit" class="registerbtn">Login</button>
    </form>
</body>
</html>

<script>
        console.log($email);
</script>
