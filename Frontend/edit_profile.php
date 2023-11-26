<?php
session_start();

// Check if the user is logged in
//if (!isset($_SESSION['email'])) {
//    header('Location: login2.php'); // Redirect to the login page if not logged in
//    exit();
//}

// Sample user data (replace with actual database retrieval)
$userData = [
    'email' => $_SESSION['email'],
    'firstName' => 'John',
    'lastName' => 'Doe',
    'password' => 'hashed_password', // Replace with actual hashed password
    'height' => '5ft 6in',
    'weight' => '140 lbs',
];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data and update the user profile (replace with actual database update)

    // Sample update for first name, last name, password, and height
    $newFirstName = $_POST['firstName'];
    $newLastName = $_POST['lastName'];
    $newPassword = $_POST['password']; // Hash the new password
    $newHeight = $_POST['height'];

    // Update the user data in the database (replace with actual update query)
    // write.php
    echo $newFirstName . " " . $newLastName;

    // Redirect to the profile page after updating
    //header('Location: profile.php');
    exit();
}
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
                        SHAPESHIFT: EDIT PROFILE <br>
