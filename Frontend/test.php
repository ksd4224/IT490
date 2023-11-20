<?php
        session_start();
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['password'] = $_POST['password'];
        $email = $_SESSION['email'];
        include 'write2.php';
        //include 'read.php';
        //if (isset($_SESSION['received_message'])){
        //      $receivedMessage = $_SESSION['received_message'];

                // Now, you can use $receivedMessage in your HTML content
        //      echo "Received Message: " . $receivedMessage;
        //} else{
        //      echo "No message received yet";
        //}
?>
<html>
        <head>
                <title> ShapeShift </title>
                <link rel="stylesheet" href="/index.css" />
        </head>

        <body style="color:#008080;" >
                <h1 class="welcome" style="margin-bottom: 1px;">
                        <img class="img2" src="logo.png">
                        <a href="login2.php">
                                <img class="img3" src="logout.png" style="width:5%; padding-right: 1%">
                        </a>
                        WELCOME TO SHAPESHIFT <br>
                        <p class="mantra">SHIFT YOUR HEALTH, SHAPE YOUR FUTURE</p> 
                        <div align="right" style="font-size:20px; font-style:normal;padding-right: 40px;">
                                Logout
                        </div>
                </h1>
                <div class="navbar">
                        <a href="test.php">Home</a>
                        <div class="dropdown">
                                <button class="dropbtn"> Profile &#8609;
                                </button>
                                <div class="dropdown-content">
                                        <a href="#">Edit profile</a>
                                        <a href="#">Edit Goals</a>
                                        <a href="#">Trend</a>
                                </div>
                        </div>
                        <div class="dropdown">
                                <button class="dropbtn"> Community &#8609;
                                </button>
                                <div class="dropdown-content">
                                        <a href="#">Community Forum</a>
                                        <a href="#">Friends</a>
                                </div>
                        </div>
                        <a href="test.php">About Us</a>
                </div>
                <p style="font-size:30px">
                        TODAY: <script> document.write(new Date().toDateString()); </script>
                </p>

                <div align="center">
                <button class="button1">
                        + Add Meals
                </button>
                <button class="button1">
                        + Add Goal
                </button>
                <button class="button1">
                        + Add Workout
                </button>
                <button class="button1">
                        + Add Weight
                </button>
                </div>
                <br><br>

                <div class="parent">
                <p style="font-size:20px; color:black"> Calories Intake: <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?> Protein Intake: <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?> Carbs Intake: <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?> Fiber Intake: <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" ?> </p>
                        <div class="circle"> 878 / 1600 </div>
                                <?php echo "&nbsp;&nbsp;" ?>
                        <div class="circle"> 45 / 120 g  </div>
                                <?php echo "&nbsp;&nbsp;" ?>
                        <div class="circle"> 190 / 325 g </div>
                                <?php echo "&nbsp;&nbsp;" ?>
                        <div class="circle"> 10 / 30 g </div>
                                <?php echo "&nbsp;&nbsp;" ?>
                </div>
                <br><br>
        </body>
</html>
