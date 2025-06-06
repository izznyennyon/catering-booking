<?php 
// This page processes the login form submission.
// The script now uses sessions.

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Need two helper files:
    require ('includes/login_functions.inc.php');
    require ('../mysqli_connect.php');
        
    // Check the login:
    list ($check, $data) = check_login($dbc, $_REQUEST['email'], $_REQUEST['pass1']);
    
    if ($check) { // OK!
        
        // Set the session data:
        session_start();
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['name'] = $data['name'];
        
        // Redirect:
        redirect_user('loggedin.php');
            
    } else { // Unsuccessful!

        // Assign $data to $errors for login_page.inc.php:
        $errors = $data;

        // Start the session and store the errors:
        session_start();
        $_SESSION['errors'] = $errors;

        // Redirect back to the login form:
        header("Location: login.php");
        exit();
    }
        
    mysqli_close($dbc); // Close the database connection.

} // End of the main submit conditional.

// Create the page:
include ('includes/login_page.inc.php');
?>
