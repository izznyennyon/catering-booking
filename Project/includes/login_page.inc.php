<?php 
session_start();
?>
<html>
<head>
    <link rel="stylesheet" href="includes/login.css" type="text/css" media="screen"/>
    <style>
        .erorrh1 {
            color: red;
            text-align: center;
            font-size: 50px;
        }
        .errorp {
            color: red;
            text-align: center;
            font-size: 15px;
        }
        .error {
            color: red;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        .form-control.error-border {
            border-color: red;
        }
    </style>
</head>
<body>
<?php

// This page prints any errors associated with logging in
// and it creates the entire login page, including the form.

// Include the header:
$page_title = 'Login';
include ('includes/header.html');

// Retrieve any error messages from the session:
$email_error = $password_error = '';

if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $msg) {
        if (strpos($msg, 'email') !== false) {
            $email_error = $msg;
        } elseif (strpos($msg, 'password') !== false) {
            $password_error = $msg;
        }
    }
    unset($_SESSION['errors']); // Clear the errors after displaying
}
?>

<div class="wrapper">
    <form class="form-signin" action="login.php" method="post">
        <h1 id="logo">Login</h1>
        <input type="text" class="form-control <?php echo !empty($email_error) ? 'error-border' : ''; ?>" placeholder="Email Address" name="email" size="20" maxlength="60" required/>
        <?php if (!empty($email_error)): ?>
            <div class="error"><?php echo $email_error; ?></div>
        <?php endif; ?>
        <input type="password" class="form-control <?php echo !empty($password_error) ? 'error-border' : ''; ?>" placeholder="Password" name="pass1" size="20" maxlength="60" required/>
        <?php if (!empty($password_error)): ?>
            <div class="error"><?php echo $password_error; ?></div>
        <?php endif; ?>
        <p><input class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="Login" /></p>
    </form>
</div>
<div class="background-slider"></div>

<?php include ('includes/footer_loggedin.html'); ?>
</body>
</html>
