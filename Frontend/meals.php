<?php
session_start();
$user_data = $_SESSION['user_data'];
$email = $user_data['email'];
$user_id = $user_data['user_id'];

    // Now you can use $email and $user_id in your code
    echo "Email: " . $email . "<br>";
    echo "User ID: " . $user_id . "<br>";
if (isset($_SESSION['user_data'])) {
                $user_data = $_SESSION['user_data'];
                $id = $user_data['user_id'];
                $email = $user_data['email'];
                $first = $user_data['first_name'];
                $last = $user_data['last_name'];
                $password = $user_data['password'];
                $movie = $user_data['movie'];
                $color = $user_data['color'];

        } else {
                // Handle the case where user data is not available
                echo "User data not available.";
        }
        if (isset($_SESSION['nutrition_data']) && $_SESSION['nutrition_data'] != 'None') {
                $nutrition_data = $_SESSION['nutrition_data'];
                $data_id = $nutrition_data['data_id'];
                $meal_id = $nutrition_data['meal_id'];
                $calories = $nutrition_data['calories'];
                $protein = $nutrition_data['protein'];
                $carbs = $nutrition_data['carbohydrates'];
                $fat = $nutrition_data['fat'];
                $sugar = $nutrition_data['sugar'];
                $serving_size = $nutrition_data['serving_size'];
                $servings = $nutrition_data['servings'];

        } else {
                // Handle the case where user data is not available
                $nutrition_data = $_SESSION['nutrition_data'];
                $data_id = 0;
                $meal_id = 0;
                $calories = 0;
                $protein = 0;
                $carbs = 0;
                $fat = 0;
                $sugar = 0;
                $serving_size = 0;
                $servings = 0;
        }

        echo "email: " . $email . " pass: " . $password . " name: " . $first . " " . $last . " movie: " . $movie . " color: " . $color . " id: " . $id;

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
                        SHAPESHIFT: LOG FOOD <br>
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
                                        <a href="profile.php">Edit profile</a>
                                        <a href="goals.php">Edit Goals</a>
                                        <a href="trend.php">Trend</a>
                                        <a href="meals.php">Add Meals</a>
                                        <a href="workout.php">Add Workout</a>
                                        <a href="add_weight.php">Add Weight</a>
                                </div>
                        </div>
                        <div class="dropdown">
                                <button class="dropbtn"> Community &#8609;
                                </button>
                                <div class="dropdown-content">
                                        <a href="forum.php">Community Forum</a>
                                        <a href="friends.php">Friends</a>
                                </div>
                        </div>
                        <a href="aboutus.php">About Us</a>
                </div>
                <p style="font-size:30px">
                        TODAY: <script> document.write(new Date().toDateString()); </script>
                </p>

                <div class="main" style="margin-left: 0px;width: 99%;">
                        <h2>ADD MEALS</h2>
                        <div class="card">
                                <div class="card-body">

                                        <form style="font-size:24px" id="nutritionForm">
                                                <label for="foodQuery">Enter a Food Item:</label>
                                                <input style="font-size: 20px" type="text" id="foodQuery" name="foodQuery" required>
                                                </br> </br>
                                                <!-- Add servings input and buttons -->
                                                <div style="width: 1000px;">
                                                        <label for="servings">Number of Servings:</label>
                                                        <input style="font-size: 20px" type="number" id="servings" name="servings" value="1" min="1">

                                                        <button class="button1" type="button" onclick="adjustServings(1)">Increase Servings</button>
                                                        <button class="button1" type="button" onclick="adjustServings(-1)">Decrease Servings</button>
                                                </div>
                                                </br></br>
                                                <button class="button1" type="button" onclick="getNutritionData()">Get Nutrition Data</button>
                                        </form>

                                        <div id="result"></div>
                                        <button class="button1" type="button" id="addMealButton" style="display: none;" onclick="sendDataToWritePHP()">Add Meal </button>
                                        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

                                                <!-- ... Your HTML code ... -->
<script>
    function getNutritionData() {
        // Get the user input
        var foodQuery = document.getElementById("foodQuery").value;

        // Make a request to the Ninja Nutrition API
        $.ajax({
            method: 'GET',
            url: 'https://api.api-ninjas.com/v1/nutrition?query=' + encodeURIComponent(foodQuery),
            headers: { 'X-Api-Key': 'GpG1j0xD2Zjw5rN+zPlV3Q==b2pGItxJcN1RFSCH' },
            contentType: 'application/json',
            success: function (result) {
                // Display the nutrition data
                displayNutritionData(result);
            },
            error: function ajaxError(jqXHR) {
                console.error('Error: ', jqXHR.responseText);
                displayError('Error fetching nutrition data. Please try again.');
            }
        });
    }
    var adjustedCalories, adjustedProtein, adjustedCarbohydrates, adjustedFat, adjustedSugarm, name, servingSize, servings;
    function displayNutritionData(data) {
        var resultContainer = document.getElementById("result");

        // Clear previous results
        resultContainer.innerHTML = "";

        if (data.length > 0) {
            // Assuming there is only one result in the array, you can access it directly
            var foodData = data[0];

            // Extract nutrition values
            name = foodData.name;
            var calories = foodData.calories;
            var protein = foodData.protein_g;
            var carbohydrates = foodData.carbohydrates_total_g;
            var fat = foodData.fat_total_g;
            var sugar = foodData.sugar_g;
            servingSize = foodData.serving_size_g;

            // Get the number of servings from the input field
            servings = parseInt(document.getElementById("servings").value, 10);

            // Calculate adjusted nutrition values based on the number of servings
            adjustedCalories = calories * servings;
            adjustedProtein = protein * servings;
            adjustedCarbohydrates = carbohydrates * servings;
            adjustedFat = fat * servings;
            adjustedSugar = sugar * servings;
            // Display the adjusted nutrition data
            resultContainer.innerHTML = `
                    <h2>${name}</h2>
                    <p style="font-size: 24px">Calories: ${adjustedCalories} kcal</p>
                    <p style="font-size: 24px">Protein: ${adjustedProtein} g</p>
                    <p style="font-size: 24px">Carbohydrates: ${adjustedCarbohydrates} g</p>
                    <p style="font-size: 24px">Fat: ${adjustedFat} g</p>
                    <p style="font-size: 24px">Sugar: ${adjustedSugar} g</p>
                    <p style="font-size: 24px">Serving Size: ${servingSize} g</p>
                    <p style="font-size: 24px">Number of Servings: <span id="displayedServings">${servings}</span></p>
                `;

            document.getElementById("addMealButton").style.display = "block";
        } else {
            // Display an error message if no results are found
            displayError('Food item not found. Please try another.');
        }
    }


    function sendDataToWritePHP() {
    // Get the adjusted nutrition values
    sessionStorage.setItem('mealName', name);
    sessionStorage.setItem('mealCalories', adjustedCalories);
    sessionStorage.setItem('mealProtein', adjustedProtein);
    sessionStorage.setItem('mealCarbohydrates', adjustedCarbohydrates);
    sessionStorage.setItem('mealFat', adjustedFat);
    sessionStorage.setItem('mealSugar', adjustedSugar);
    sessionStorage.setItem('mealServingSize', servingSize);
    sessionStorage.setItem('mealServings', servings);

    var mealDateTime = new Date().toISOString();
    sessionStorage.setItem('mealDateTime', mealDateTime);
    console.log("DateTime: " + mealDateTime);
    console.log("Name: " + name);
    console.log("Calories: " + adjustedCalories);
        

    // Use AJAX to send data to check4.php
    $.ajax({
        type: 'POST',
        url: 'check4.php',
        data: {
            mealName: name,
            mealCalories: adjustedCalories,
            mealProtein: adjustedProtein,
            mealCarbohydrates: adjustedCarbohydrates,
            mealFat: adjustedFat,
            mealSugar: adjustedSugar,
            mealServingSize: servingSize,
            mealServings: servings,
            mealDateTime: mealDateTime,
        },
        success: function(response) {
            // Handle the response from the server if needed
                // Extract script content from the response
                console.log("AJAX Response: " + response);
                //var scriptContent = $(response).filter('script').text();

                // Execute the extracted script
                //if (scriptContent.trim() !== '') {
        //              var script = new Function(response);
        //              script();
                //}
                // Replace '#result-container' with the appropriate selector for your use case
                //$('#result-container').html(response);
                window.location.href = 'test.php?meal=success';
        },
        error: function(jqXHR, textStatus, errorThrown, response) {
            console.log("AJAX Response: " + response);
            console.error('AJAX Error:', textStatus, errorThrown);
            window.location.href = 'test.php?meal=success';
        }
    });
    }

    function adjustServings(change) {
        // Adjust the number of servings based on the change (1 for increase, -1 for decrease)
        var servingsInput = document.getElementById("servings");
        var currentServings = parseInt(servingsInput.value, 10);
        var newServings = Math.max(1, currentServings + change); // Ensure the minimum number of servings is 1
        servingsInput.value = newServings;

        // Update the displayed number of servings
        var displayedServings = document.getElementById("displayedServings");
        if (displayedServings) {
            displayedServings.textContent = newServings;
        }
    }

    function displayError(message) {
        var resultContainer = document.getElementById("result");
        resultContainer.innerHTML = `<p style="color: red;">${message}</p>`;
    }
</script>
<!-- ... The rest of your HTML code ... -->


                                </div>
                        </div>
                </div>
        </body>
</html>

