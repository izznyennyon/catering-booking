<?php # Script 10.3 - edit_user.php
session_start(); // Start the session.

// This page is for editing a user record.
// This page is accessed through view_users.php.

// If no session value is present, redirect the user:
if (!isset($_SESSION['user_id'])) {
	// Need the functions:
	require ('includes/login_functions.inc.php');
	redirect_user();	
}
?>
<html>
<head>
	<link rel="stylesheet" href="includes/deleteUser.css">
</head>
<body>
<?php
$page_title = 'Delete a User';
include ('includes/header.html');
echo '<h1>Delete a User</h1>';
?>
<div class="delete">

<?php
// Check for a valid user ID, through GET or POST:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) { // From view_users.php
	$id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
	$id = $_POST['id'];
} else { // No valid ID, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	include ('includes/footer.html'); 
	exit();
}

require ('../mysqli_connect.php');

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($_POST['sure'] == 'Yes') { // Delete the record.

		// Make the query:
		$q = "DELETE FROM orders WHERE order_id=$id LIMIT 1";		
		$r = @mysqli_query ($dbc, $q);
		if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

			// Print a message:
			echo '<p>The user has been deleted.</p><br><a href=view_customer.php><input type="submit" name="submit" value="Back" /></a>';
			

		} else { // If the query did not run OK.
			echo '<p class="error">The user could not be deleted due to a system error.</p>'; // Public message.
			echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $q . '</p>'; // Debugging message.
			echo '<a href=view_customer.php><input type="submit" name="submit" value="Back" /></a>';
		}
	
	} else { // No confirmation of deletion.
		echo '<p>The user has NOT been deleted.</p><br>
		<a href=view_customer.php><input type="submit" name="submit" value="Continue" /></a>';	
	}

} else { // Show the form.

	// Retrieve the user's information:
	$q = "SELECT CONCAT(order_id, ', Name: ',contact_person ) FROM orders WHERE order_id=$id";
	$r = @mysqli_query ($dbc, $q);

	if (mysqli_num_rows($r) == 1) { // Valid user ID, show the form.

		// Get the user's information:
		$row = mysqli_fetch_array ($r, MYSQLI_NUM);
		
		// Display the record being deleted:
		echo "<h3>Order ID: $row[0]</h3>
		Are you sure you want to delete this user?";
		
		// Create the form:
		echo '<form action="delete_user.php" method="post">
	<input type="radio" name="sure" value="Yes" /> Yes 
	<input type="radio" name="sure" value="No" checked="checked" /> No
	<input type="submit" name="submit" value="Submit" />
	<input type="hidden" name="id" value="' . $id . '" />
	</form><br>
	<a href=view_customer.php><input type="submit" name="submit" value="Back" /></a>';
	
	} else { // Not a valid user ID.
		echo '<p class="error">This page has been accessed in error.</p><br><a href=view_customer.php><input type="submit" name="submit" value="Back" /></a>';
	}

} // End of the main submission conditional.

mysqli_close($dbc);
?>
</div>
<?php
include ('includes/footer_loggedin.html');
?>
</body>
</html>
