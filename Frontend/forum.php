<?php
        session_start();
	$user_data = $_SESSION['user_data'];
	$email = $user_data['email'];
	$first_name = $user_data['first_name'];

	
	// Assuming the response from check9.php is stored in $_SESSION['forum_response']
	$forum_response = $_SESSION['forum_response'];
	$all_posts = [];

	// Check if 'all_posts' is set in the forum response
	if (isset($forum_response['all_posts'])) {
    		$all_posts = $forum_response['all_posts'];
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
                        SHAPESHIFT: COMMUNITY FORUM <br>
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
					<a href="trend.php">7-day Trend</a>
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
                                </div>
                        </div>
                        <a href="aboutus.php">About Us</a>
		</div>
		<div class="container">
			<div class="content">
        			<h1>Nutrition Social</h1>
        			<!-- Post Form -->
        			<form action="check9.php" method="post">
            				<textarea name="postContent" placeholder="What's on your mind?"></textarea>
            				<button type="submit">Post</button>
        			</form>
				<!-- Display Posts -->
				<?php 
				foreach ($all_posts as $post) {
                                        echo '<div class="post">';
                                        echo '<strong>' . ($post['first_name'] ?? $first_name) . '</strong>';
                                        echo '<p>' . $post['post_content'] . '</p>';
                                        echo '</div>';
                                } 
				?>

				<?php
				$posts = [
    					['user' => 'John', 'content' => 'Enjoying a healthy salad today!'],
    					['user' => 'Alice', 'content' => 'Just finished a great workout session.'],
				];

				foreach ($posts as $post) {
                			echo '<div class="post">';
                			echo '<strong>' . $post['user'] . '</strong>';
                			echo '<p>' . $post['content'] . '</p>';
                			echo '</div>';
            			}
				?>
			</div>

			<div class="sidebar">
        			<h2>Friends</h2>
        			<!-- Display Friends -->
        			<?php
					// Fetch and display friends from the database
					// Replace this with actual database queries
					$friends = ['Friend 1', 'Friend 2', 'Friend 3', 'Friend 4'];

					echo '<ul>';
					foreach ($friends as $friend) {
				    		echo '<li>' . $friend . '</li>';
					}
					echo '</ul>';
				?>

			</div>
		</div>
	</body>
</html>
