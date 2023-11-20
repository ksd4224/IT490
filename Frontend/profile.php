<?php
        session_start();
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['password'] = $_POST['password'];
        $email = $_SESSION['email'];
        echo "hi, " . $email;
        //include 'write2.php';
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
                        SHAPESHIFT: PROFILE <br>
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
                <div class="main" style="margin-left: 0px;width: 99%;">
                        <h2>PROFILE</h2>
                        <div class="card">
                        <div class="card-body">
                        <i class="fa fa-pen fa-xs edit"></i>
                        <table>
                                <tbody>
                                        <tr>
                                                <td style="width: 150px;">Name</td>
                                                <td>:</td>
                                                <td>ImDezCode</td>
                                        </tr>
                                        <tr>
                                                <td>Email Address</td>
                                                <td>:</td>
                                                <td><?php echo $email ?> </td>
                                        </tr>
                                        <tr>
                                                <td>Address</td>
                                                <td>:</td>
                                                <td>Bali, Indonesia</td>
                                        </tr>
                                        <tr>
                                                <td>Job</td>
                                                <td>:</td>
                                                <td>Web Developer</td>
                                        </tr>
                                </tbody>
                        </table>
                        </div>
                        </div>
                </div>
        </body>
</html>
          
