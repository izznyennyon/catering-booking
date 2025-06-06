<?php
// This page lets the user logout.
// This version uses sessions.
session_start(); // Access the existing session.

// If no session variable exists, redirect the user:
if (!isset($_SESSION['user_id'])) {

	// Need the functions:
	require ('includes/login_functions.inc.php');
	redirect_user();	
	
} else { // Cancel the session:

	$_SESSION = array(); // Clear the variables.
	session_destroy(); // Destroy the session itself.
	setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0); // Destroy the cookie.

}

// Set the page title and include the HTML header:
$page_title = 'Logged Out!';
include ('includes/header.html');

?>
<link rel="stylesheet" href="includes/loggedin.css" type="text/css" media="screen"/>
<div class="wrapper">
<?php
// Print a customized message:
echo "<h1>Logged Out!</h1>
<p>You are now logged out!</p>";

?>

</div>
<div class="background-slider"></div>
<?php


include ('includes/footer_loggedin.html');
?>