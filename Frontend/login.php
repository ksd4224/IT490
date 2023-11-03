<?php
	session_start();
	ini_set('display_errors', 1); 
	ini_set('display_startup_errors', 1); 
	error_reporting(E_ALL);
	session_regenerate_id(true);
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    		$email = $_POST['email'];
    		$psw = $_POST['psw'];
		echo "in post";
		function isValidLogin($email, $psw){
			//$valide = 'test';
			//$validp = '123';

			//if($email == $valide && $psw == $validp){
				return true;
			//}
			//else{
			//	return false;
			//}
		}

    		if (isValidLogin($email, $psw)) {
        		// Successful login
        		$_SESSION['user_authenticated'] = true;
        		$_SESSION['email'] = $email; // Set email in session
        		$_SESSION['psw'] = $psw; // Set password in session
			echo "Successful login. Redirecting...";
        		// Redirect to write.php
        		header("Location: /write.php");
        		exit;
    		} else {
        	// Login failed; display an error message or redirect back to login
			echo "Login failed. Redirecting...";
			header("Location: /login.php");
        		exit;
    		}
	}
	//$_SESSION['email'] = $_POST['email'];
        //$_SESSION['psw'] = $_POST['psw'];
        //$email = $_SESSION['email'];
	//$psw = $_SESSION['psw'];
?>
<html>
        <head>
                <title> ShapeShift </title>
                <link rel="stylesheet" href="index.css" />
	</head>

        <body style="color:#008080;" >
                <h1 class="welcome">
                        <img class="img1" src="logo.png">
                        LOGIN TO SHAPESHIFT <br>
                        <p class="mantra">SHIFT YOUR HEALTH, SHAPE YOUR FUTURE</p>
                        <h1 class="register"> LOGIN </h1>
                  </h1>
		  <form action="write.php" method="post">
			<label for="email"><b>Email</b></label>
                	<input type="text" placeholder="Enter Email" name="email" id="email" required>
                	<br><br>
                	<label for="psw"><b>Password</b></label>
                	<input type="password" placeholder="Enter Password" name="psw" id="psw" required>
                	<br><br>
                	<button type="submit" class="registerbtn" onsubmit="isValidLogin()" >Login</button>
        	</form>
	<body>
</html>
<!-- 
<script>
	function sendlogin(event){
		event.preventDefault();

		const email = document.getElementById("email").value;
    		const psw = document.getElementById("psw").value;
		
		const registrationData = {
  			email: email,
  			password: psw
		};

		fetch("http://10.248.179.14/api/register/", {
  			method: "POST",
 			headers: {
    			"Content-Type": "application/json",
  			},
  			body: JSON.stringify(registrationData),
		})
  		.then((response) => {
		console.log("Response received");
		if (response.ok) {
      			// Registration was successful
      			return response.json();
    		} else {
      		// Handle registration errors
      			throw new Error("Registration failed");
    		}
  		})
  		.then((data) => {
			console.log("Success response:", data);
			alert("success");
  		})
		.catch((error) => {
			console.log("Success response:", data);
    			alert("not successful");
		});
}
</script>
-->
