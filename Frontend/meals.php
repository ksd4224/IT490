<?php
        session_start();
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['password'] = $_POST['password'];
        $email = $_SESSION['email'];
        //echo "hi, " . $email;
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
    </br>
    <form style="font-size:30px" id="nutritionForm">
        <label for="foodQuery">Enter a Food Item:</label>
        <input type="text" id="foodQuery" name="foodQuery" required>
        </br> </br>
        <!-- Add servings input and buttons -->
        <label for="servings">Number of Servings:</label>
        <input type="number" id="servings" name="servings" value="1" min="1">
        <button class="button1" type="button" onclick="adjustServings(1)">Increase Servings</button>
        <button class="button1" type="button" onclick="adjustServings(-1)">Decrease Servings</button>
        </br></br>
        <button class="button1" type="button" onclick="getNutritionData()">Get Nutrition Data</button>
    </form>

    <div id="result"></div>
    <button class="button1" type="button" id="addMealButton" style="display: none;" onclick="sendDataToWritePHP()">Add Meal</button>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function getNutritionData() {
            // Get the user input
            var foodQuery = document.getElementById("foodQuery").value;

            // Make a request to the Ninja Nutrition API
            $.ajax({
                method: 'GET',
                url: 'https://api.api-ninjas.com/v1/nutrition?query=' + encodeURIComponent(foodQuery),
                headers: { 'X-Api-Key': 'GpG1j0xD2Zjw5rN+zPlV3Q==b2pGItxJcN1RFSCH' }, // Replace with your actual API key
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

        function displayNutritionData(data) {
            var resultContainer = document.getElementById("result");

            // Clear previous results
            resultContainer.innerHTML = "";

            if (data.length > 0) {
                // Assuming there is only one result in the array, you can access it directly
                var foodData = data[0];

                // Extract nutrition values
                var name = foodData.name;
                var calories = foodData.calories;
                var protein = foodData.protein_g;
                var carbohydrates = foodData.carbohydrates_total_g;
                var fat = foodData.fat_total_g;
                var sugar = foodData.sugar_g;
                var servingSize = foodData.serving_size_g;

                // Get the number of servings from the input field
                var servings = parseInt(document.getElementById("servings").value, 10);

                // Calculate adjusted nutrition values based on the number of servings
                var adjustedCalories = calories * servings;
                var adjustedProtein = protein * servings;
                var adjustedCarbohydrates = carbohydrates * servings;
                var adjustedFat = fat * servings;
                var adjustedSugar = sugar * servings;
                // Display the adjusted nutrition data
                resultContainer.innerHTML = `
                    <h2>${name}</h2>
                    <p>Calories: ${adjustedCalories} kcal</p>
                    <p>Protein: ${adjustedProtein} g</p>
                    <p>Carbohydrates: ${adjustedCarbohydrates} g</p>
                    <p>Fat: ${adjustedFat} g</p>
                    <p>Sugar: ${adjustedSugar} g</p>
                    <p>Serving Size: ${servingSize} g</p>
                    <p>Number of Servings: <span id="displayedServings">${servings}</span></p>
                `;
                
                document.getElementById("addMealButton").style.display = "block";
                // Send data to write.php
                //sendDataToWritePHP(name, adjustedCalories, adjustedProtein, adjustedCarbohydrates, adjustedFat, adjustedSugar, servingSize, servings);
            } else {
                // Display an error message if no results are found
                displayError('Food item not found. Please try another.');
            }
        }

        function sendDataToWritePHP(name, calories, protein, carbohydrates, fat, sugar, servingSize, servings) {
            // Make a request to write.php to send data to RabbitMQ
            $.ajax({
                method: 'POST',
                url: 'write.php',
                data: {
                    name: name,
                    calories: calories,
                    protein: protein,
                    carbohydrates: carbohydrates,
                    fat: fat,
                    sugar: sugar,
                    servingSize: servingSize,
                    servings: servings
                },
                success: function(result) {
                    console.log(result);
                    // You can handle the success response if needed
                    // Display an alert based on the result
                    if (result === "Success") {
                        alert("Meal added successfully!");
                    } else {
                        alert("Failed to add meal. Please try again.");
                    }
                },
                error: function ajaxError(jqXHR) {
                    console.error('Error: ', jqXHR.responseText);
                    // Handle the error as needed
                    alert("Failed to add meal. Please try again.");
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

</body>
</html>
