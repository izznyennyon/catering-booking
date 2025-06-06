<?php 
// This script retrieves all the records from the users table.
// This new version links to edit and delete pages.
session_start(); // Start the session.

// If no session value is present, redirect the user:
if (!isset($_SESSION['user_id'])) {
	// Need the functions:
	require ('includes/login_functions.inc.php');
	redirect_user();	
}

$page_title = 'View the Current Users';
include ('includes/header.html');
		?>
		<link rel="stylesheet" href="includes/view_customer.css" type="text/css" media="screen"/>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		
		<div class="wrapper0">
			<div class="wrapper">
				<?php
				echo '<h1 id="logo">Reservation List</h1>';

				require ('../mysqli_connect.php');
				
				// Define the query:
				$q = "SELECT total_budget, contact_person, contact_no, num_pax, event_date, location, special_req, promo_code, subscribe, order_id FROM orders ORDER BY registration_date ASC";
				$r = @mysqli_query ($dbc, $q);

				// Count the number of returned rows:
				$num = mysqli_num_rows($r);

				if ($num > 0) { // If it ran OK, display the records.

					// Print how many users there are:
					echo "<p>There are currently $num booking(s).</p>\n";
					?>
					<form action="" method="GET">
						<div class="input-group mb-3">
							<input type="text" name="search" value="<?php if(isset($_GET['search'])) { echo $_GET['search']; } ?>" class="form-control" placeholder="Search">
							<button type="submit" class="btn btn-primary">Search</button>
						</div>
					</form>
					<?php 
					if(isset($_GET['search'])) {
						$filtervalues = mysqli_real_escape_string($dbc, $_GET['search']);
						$q = "SELECT total_budget, contact_person, contact_no, num_pax, event_date, location, special_req, promo_code, subscribe, order_id FROM orders WHERE contact_person LIKE '%$filtervalues%' ORDER BY registration_date ASC";
						$r = @mysqli_query ($dbc, $q);
					}

					if(mysqli_num_rows($r) > 0) {
						echo '<table class="fl-table" align="center" cellspacing="10" cellpadding="10" width="100%">
						<tr>
							<th align="left"><b>Edit</b></th>
							<th align="left"><b>Delete</b></th>
							<th align="left"><b>Order ID</b></th>
							<th align="left"><b>Contact Person</b></th>
							<th align="left"><b>Contact Number</b></th>
							<th align="left"><b>Number Pax</b></th>
							<th align="left"><b>Total Budget</b></th>
							<th align="left"><b>Event Date</b></th>
							<th align="left"><b>Location</b></th>
							<th align="left"><b>Special Request</b></th>
							<th align="left"><b>Promo Code</b></th>
							<th align="left"><b>Subscribe</b></th>
						</tr>';
						
						// Fetch and print all the records:
						while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
							echo '<tr>
								<td align="left"><a class="button primary edit" href="edit_user.php?id=' . $row['order_id'] . '">Edit</a></td>
								<td align="left"><a class="button primary delete" href="delete_user.php?id=' . $row['order_id'] . '">Delete</a></td>
								<td align="left">' . $row['order_id'] . '</td>
								<td align="left">' . $row['contact_person'] . '</td>
								<td align="left">' . $row['contact_no'] . '</td>
								<td align="left">' . $row['num_pax'] . '</td>
								<td align="left">' . $row['total_budget'] . '</td>
								<td align="left">' . $row['event_date'] . '</td>
								<td align="left">' . $row['location'] . '</td>
								<td align="left">' . $row['special_req'] . '</td>
								<td align="left">' . $row['promo_code'] . '</td>
								<td align="left">' . $row['subscribe'] . '</td>
							</tr>';
						}

						echo '</table>';
						mysqli_free_result ($r); // Free memory associated with $r
					} else {
						echo '<tr><td colspan="12">No Record Found</td></tr>';
					}
				} else { // If no records were returned.
					echo '<p class="error">There are currently no registered users.</p>';
				}
				?>
			</div>
		</div>
		<?php
		mysqli_close($dbc); // Close database connection
		include ('includes/footer.html');
		?>
	</body>
</html>
