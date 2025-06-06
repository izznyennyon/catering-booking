<html>
<head>
    <link rel="stylesheet" href="includes/req_quotation_form.css" type="text/css" media="screen"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<?php 
require("script.php");
$page_title = 'Free Quotation';
include ('includes/header.html');
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = array();    // Check required fields
    $required_fields = ['occasion', 'event_date', 'event_time', 'budget', 'num_pax', 'event_address', 'location', 'contact_person', 'contact_no', 'email', 'company_name'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "You forgot to fill in $field.";
        } else {
            $$field = trim($_POST[$field]);
            // Truncate fields that might be too long for database
            if ($field == 'contact_person' && strlen($$field) > 50) {
                $$field = substr($$field, 0, 50);
            }
            if ($field == 'company_name' && strlen($$field) > 30) {
                $$field = substr($$field, 0, 30);
            }
        }
    }

    // Optional fields
    $special_req = trim($_POST['special_req'] ?? '');
    $promo_code = trim($_POST['promo_code'] ?? '');
    $subscribe = isset($_POST['subscribe']) ? 'Yes' : 'No';

    // Validate numerical fields
    if (!empty($budget) && !is_numeric($budget)) {
        $errors[] = "Budget must be a number.";
    }
    if (!empty($num_pax) && !is_numeric($num_pax)) {
        $errors[] = "Number of Pax must be a number.";
    }
    if (!empty($contact_no) && !is_numeric($contact_no)) {
        $errors[] = "Contact Number must be a number.";
    }    // Validate email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate field lengths
    if (!empty($contact_person) && strlen($contact_person) > 50) {
        $errors[] = "Contact person name is too long (maximum 50 characters).";
    }
    if (!empty($location) && strlen($location) > 40) {
        $errors[] = "Location is too long (maximum 40 characters).";
    }
    if (!empty($email) && strlen($email) > 30) {
        $errors[] = "Email is too long (maximum 30 characters).";
    }

    // Calculate total budget
    if (empty($errors)) {
        $total_budget = $budget * $num_pax;
        $total_budget = number_format($total_budget, 2);

        // Database connection
        require('../mysqli_connect.php');        // Insert query
        $q = "INSERT INTO orders (occasion, event_date, event_time, budget, registration_date, total_budget, num_pax, event_address, location, contact_person, contact_no, email, company_name, special_req, promo_code, subscribe) VALUES ('$occasion', '$event_date', '$event_time', '$budget', NOW(), '$total_budget', '$num_pax', '$event_address', '$location', '$contact_person', '$contact_no', '$email', '$company_name', '$special_req', '$promo_code', '$subscribe')";
        $r = mysqli_query($dbc, $q);        if ($r) {
            // Send email
            $message = "Here are your event details:\n" .
                        "Occasion: $occasion\n" .
                        "Event Date: $event_date\n" .
                        "Event Time: $event_time\n" .
                        "Event Address: $event_address\n" .
                        "Location: $location\n" .
                        "Budget/Pax: RM$budget\n" .
                        "Number of Pax: $num_pax\n" .
                        "Total Budget: RM$total_budget\n" .
                        "Contact Person: $contact_person\n" .
                        "Contact Number: $contact_no\n" .
                        "Email: $email\n" .
                        "Company Name: $company_name\n" .
                        "Special Request: $special_req\n" .
                        "Promo Code: $promo_code\n" .
                        "Subscribe: $subscribe";
            $response = sendMail($email, "Quotation Details", nl2br($message));

            echo '<div class="wrapper1">';
            echo '<h1>Thank you!</h1>';
            echo '<h2>You are now registered!</h2>';


           echo nl2br($message);
            echo '<p>-----------------------------------------------------------------------</p>';
            echo '<p>Thank you for registering with us. We will contact you soon.</p>';
            if ($response == "success") {
                echo '<p class="success">Email has been sent successfully.</p>';
            } else {
                echo "<p class=\"error\">$response</p>";
            }
            echo '</div>';

        } else {
            echo '<h1>System Error</h1>';
            echo '<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>';
            echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $q . '</p>';
        }
        mysqli_close($dbc);
        include('includes/footer.html');
        exit();
    } else {
        // Generate a JavaScript alert with error messages
        echo '<script type="text/javascript">';
        echo 'alert("The following error(s) occurred:\n';
        foreach ($errors as $msg) {
            echo " - $msg\\n";
        }
        echo 'Please try again.");';
        echo '</script>';
    }
}
?>

<script src="jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Real-time budget calculation
    $("#price, #qty").on('input keyup', function() {
        var price = parseFloat($("#price").val()) || 0;
        var qty = parseInt($("#qty").val()) || 0;
        var total = price * qty;
        
        if (total > 0) {
            $('#total_budget').val("RM " + total.toFixed(2));
            $('#hidden_total_budget').val(total.toFixed(2));
        } else {
            $('#total_budget').val("");
            $('#hidden_total_budget').val("");
        }
    });

    // Form validation
    $('#quotationForm').on('submit', function(e) {
        var isValid = true;
        var errorMessages = [];
        
        // Clear previous error styling
        $('.form-group input, .form-group select, .form-group textarea').removeClass('error-field');
        
        // Validate required fields
        $('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('error-field');
                var label = $(this).closest('.form-group').find('label').text().replace('*', '');
                errorMessages.push(label + " is required");
                isValid = false;
            }
        });
        
        // Validate email format
        var email = $('#email').val();
        if (email && !isValidEmail(email)) {
            $('#email').addClass('error-field');
            errorMessages.push("Please enter a valid email address");
            isValid = false;
        }
        
        // Validate numeric fields
        var budget = $('#price').val();
        if (budget && !isNumeric(budget)) {
            $('#price').addClass('error-field');
            errorMessages.push("Budget must be a valid number");
            isValid = false;
        }
        
        var numPax = $('#qty').val();
        if (numPax && !isNumeric(numPax)) {
            $('#qty').addClass('error-field');
            errorMessages.push("Number of Pax must be a valid number");
            isValid = false;
        }
        
        var contactNo = $('#contact_no').val();
        if (contactNo && !isNumeric(contactNo)) {
            $('#contact_no').addClass('error-field');
            errorMessages.push("Contact Number must contain only numbers");
            isValid = false;
        }
        
        // Validate date is not in the past
        var eventDate = $('#event_date').val();
        if (eventDate) {
            var today = new Date();
            var selectedDate = new Date(eventDate);
            if (selectedDate < today.setHours(0,0,0,0)) {
                $('#event_date').addClass('error-field');
                errorMessages.push("Event date cannot be in the past");
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            showErrorMessages(errorMessages);
            // Scroll to first error field
            $('.error-field').first().focus();
        }
    });
    
    // Real-time validation feedback
    $('input, select, textarea').on('blur', function() {
        $(this).removeClass('error-field');
        
        if ($(this).attr('required') && !$(this).val().trim()) {
            $(this).addClass('error-field');
        }
        
        // Email validation
        if ($(this).attr('type') === 'email' && $(this).val() && !isValidEmail($(this).val())) {
            $(this).addClass('error-field');
        }
    });
    
    // Helper functions
    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isNumeric(value) {
        return !isNaN(parseFloat(value)) && isFinite(value);
    }
    
    function showErrorMessages(messages) {
        var errorHtml = 'Please correct the following errors:\\n\\n';
        messages.forEach(function(msg) {
            errorHtml += '• ' + msg + '\\n';
        });
        alert(errorHtml);
    }
});
</script>

<div class="wrapper">
    <h1>Enquire Now! Request FREE Quote</h1>
    <form action="req_quotation_form.php" method="post" id="quotationForm">
        <div class="column2">
            <h2>Event Details</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="occasion">Occasion *</label>
                    <?php
                    $occasion = array('', 'Company Event', 'Happy Birthday Event', 'Wedding Event');
                    echo '<select name="occasion" id="occasion" required>';
                    foreach ($occasion as $key => $value) {
                        $selected = (isset($_POST['occasion']) && $_POST['occasion'] == $value) ? 'selected' : '';
                        echo "<option value=\"$value\" $selected>$value</option>\n";
                    }
                    echo '</select>';
                    ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="event_date">Event Date *</label>
                    <input type="date" name="event_date" id="event_date" required 
                           value="<?php if (isset($_POST['event_date'])) echo $_POST['event_date']; ?>" />
                </div>
                <div class="form-group half-width">
                    <label for="event_time">Event Time *</label>
                    <input type="time" name="event_time" id="event_time" required 
                           value="<?php if (isset($_POST['event_time'])) echo $_POST['event_time']; ?>" />
                </div>
            </div>
        </div>
        
        <div class="column2">
            <h2>Budget Information</h2>
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="price">Budget/Pax (RM) *</label>
                    <input type="text" id="price" name="budget" placeholder="e.g. 25.00" required 
                           value="<?php if (isset($_POST['budget'])) echo $_POST['budget']; ?>" />
                </div>
                <div class="form-group half-width">
                    <label for="qty">Number of Pax *</label>
                    <input type="text" id="qty" name="num_pax" placeholder="e.g. 50" required 
                           value="<?php if (isset($_POST['num_pax'])) echo $_POST['num_pax']; ?>" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group full-width">
                    <div class="tb">
                        <label for="total_budget">Total Budget (RM)</label>
                        <input type="text" placeholder="Auto calculated" id="total_budget" disabled />
                        <input type="hidden" name="total_budget" id="hidden_total_budget" 
                               value="<?php if (isset($_POST['total_budget'])) echo $_POST['total_budget']; ?>" />
                    </div>
                </div>
            </div>
        </div>        <div class="column2">
            <h2>Event Location & Contact</h2>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="event_address">Event Address *</label>
                    <textarea name="event_address" id="event_address" placeholder="Enter your event address" required><?php if (isset($_POST['event_address'])) echo $_POST['event_address']; ?></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="location">Location *</label>
                    <?php
                    $location = array('', 'Kuala Lumpur', 'Selangor');
                    echo '<select name="location" id="location" required>';
                    foreach ($location as $key => $value) {
                        $selected = (isset($_POST['location']) && $_POST['location'] == $value) ? 'selected' : '';
                        echo "<option value=\"$value\" $selected>$value</option>\n";
                    }
                    echo '</select>';
                    ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="contact_person">Contact Person *</label>
                    <input type="text" name="contact_person" id="contact_person" placeholder="Your Name" maxlength="50" required 
                           value="<?php if (isset($_POST['contact_person'])) echo $_POST['contact_person']; ?>" />
                </div>
                <div class="form-group half-width">
                    <label for="contact_no">Contact Number *</label>
                    <input type="text" name="contact_no" id="contact_no" placeholder="Your Phone Number" maxlength="15" required 
                           value="<?php if (isset($_POST['contact_no'])) echo $_POST['contact_no']; ?>" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="company_name">Company Name *</label>
                    <input type="text" name="company_name" id="company_name" placeholder="Your Company Name" maxlength="30" required 
                           value="<?php if (isset($_POST['company_name'])) echo $_POST['company_name']; ?>" />
                </div>
                <div class="form-group half-width">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" placeholder="Your Email" maxlength="30" required 
                           value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" />
                </div>
            </div>
        </div>        <div class="column3">
            <h2>Additional Information</h2>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="special_req">Special Request</label>
                    <textarea name="special_req" id="special_req" placeholder="e.g. Kambing golek nak garing"><?php if (isset($_POST['special_req'])) echo $_POST['special_req']; ?></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="promo_code">Promo Code</label>
                    <input type="text" name="promo_code" id="promo_code" placeholder="Enter promo code if available" maxlength="20" 
                           value="<?php if (isset($_POST['promo_code'])) echo $_POST['promo_code']; ?>" />
                </div>
                <div class="form-group half-width">
                    <div class="checkbox-wrapper-19" style="margin-top: 25px;">
                        <label for="cbtest-19" style="display: flex; align-items: center; gap: 10px; font-weight: 500;">
                            <input type="checkbox" id="cbtest-19" name="subscribe" value="Yes" 
                                   <?php if (isset($_POST['subscribe']) && $_POST['subscribe'] == 'Yes') echo 'checked'; ?> />
                            <span class="check-box"></span>
                            Subscribe to our newsletter
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="textsubmit">
            <input type="submit" name="submit" value="Submit for FREE Quote" />
        </div>
    </form>
</div>

<?php include('includes/footer.html'); ?>
</body>
</html>
