<?php 
// Include the code that contains shopping cart's functions.
// Current session is detected in "cartFunctions.php, hence need not start session here.
include_once("cartFunctions.php");
include("header.php"); // Include the Page Layout header

if (! isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: login.php");
	exit;
}

echo "<div id='myShopCart' style='margin:auto'>"; // Start a container
if (isset($_SESSION["Cart"])) {
    include_once("mysql_conn.php");

    // Retrieve the latest GST rate applicable as of today
    $currentDate = date("Y-m-d");
    $gstQuery = "SELECT TaxRate FROM gst WHERE EffectiveDate <= ? ORDER BY EffectiveDate DESC LIMIT 1";
    $gstStmt = $conn->prepare($gstQuery);
    $gstStmt->bind_param("s", $currentDate); // 's' for string type
    $gstStmt->execute();
    $gstResult = $gstStmt->get_result();
    $gstRate = 0.07; // Default GST rate
    if ($gstRow = $gstResult->fetch_assoc()) {
        $gstRate = $gstRow["TaxRate"] / 100;
    }
    $gstStmt->close();

	$qry = "SELECT *, (Price*Quantity) AS Total
			FROM ShopCartItem WHERE ShopCartID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("i", $_SESSION["Cart"]); //i - integer
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	
	if ($result->num_rows > 0) {
		// To Do 2 (Practical 4): Format and display 
		// the page header and header row of shopping cart page
		echo "<p class='page-title' style='text-align:center'>Shopping Cart</p>"; 
		echo "<div class='table-responsive' >"; // Bootstrap responsive table
		echo "<table class='table table-hover'>"; // Start of table
		echo "<thead class='cart-header'>"; //Start of table's header section
		echo "<tr>"; //Start of header row
		echo "<th width= '250px'> Item </th>";
		echo "<th width= '90px'> Price (S$) </th>";
		echo "<th width= '60px'> Quantity </th>";
		echo "<th width= '120px'> Total (S$)</th>";
		echo "<th>&nbsp</th>";
		echo "<tr>"; //End of header row
		echo "<thead>"; //End of table's header section
		
		// To Do 5 (Practical 5):
		// Declare an array to store the shopping cart items in session variable 
		$_SESSION["Items"]=array();

		// To Do 3 (Practical 4): 
		// Display the shopping cart content
		$subTotal = 0; // Declare a variable to compute subtotal before everything
		$totalAmt = 0; // Declare a variable to compute subtotal after everything

		echo "<tbody>"; // Start of table's body section
		while ($row = $result->fetch_array()) {
			echo "<tr>";
			echo "<td style='width:50%'>$row[Name]<br />";
			echo "Product ID: $row[ProductID]</td>";
			$formattedPrice = number_format($row["Price"], 2);
			echo "<td>$formattedPrice</td>";
			echo "<td>"; //Column for updated quantity of purchase
			echo "<form action = 'cartFunctions.php' method = 'post'>";
			echo "<select name='quantity' onChange='this.form.submit()'>";
			for ($i = 1; $i <= 10; $i++){
				if($i == $row["Quantity"])
					//Select drop-down list item with value same as the quantity of purcahse
					$selected = 'selected';
				else
					$selected = ""; //No Specific item is selected 
				echo "<option value='$i' $selected>$i</option>";
			}
			echo "</select>";
			echo "<input type='hidden' name='action' value='update' />";
			echo "<input type='hidden' name='product_id' value='$row[ProductID]' />";
			echo "</form>";
			echo "</td>";
			$formattedTotal = number_format($row["Total"], 2);
			echo "<td>$formattedTotal</td>";
			echo "<td>"; // Column for remove item from shopping cart
			echo "<form action = 'cartFunctions.php' method = 'post'>";
			
			echo "<input type='hidden' name='action' value='remove' />";
			echo "<input type='hidden' name='product_id' value='$row[ProductID]' />";
			echo "<input type='image' src='images/trash-can.png' title='Remove Item'/>";
			echo "</form>";
			echo "</td>";
			echo "</tr>";
			// To Do 6 (Practical 5):
		    // Store the shopping cart items in session variable as an associate array
			$_SESSION["Items"][] = array("productId"=>$row["ProductID"],
                             "name"=>$row["Name"],
                             "price"=>$row["Price"],
                             "quantity"=>$row["Quantity"]);

			// Accumulate the running sub-total
			$subTotal += $row["Total"];
		}
		echo "</tbody>"; // End of table's body section
		echo "</table>"; // End of table
		echo "</div>"; // End of Bootstrap responsive table

		// Check if delivery charge should be waived for orders above $300
		if ($subTotal > 300) {
			$shipCharge = 0;  // Set shipCharge to 0 if waived
			echo "<div style='text-align:right; color: green; font-size: 18px; margin-top: 10px;'>";
			echo "Congratulations! Your delivery charge has been waived.";
			echo "</div>";
		} else {
			echo "<div style='text-align:right; font-size:15px; margin-top:20px;'>";
			echo "<form action='' method='post' id='deliveryOptionForm'>"; // Form submits to the same page
			echo "<strong>Select your delivery option:</strong><br>";
			echo "<select name='delivery_option' onchange='document.getElementById(\"deliveryOptionForm\").submit();' style='margin-top:10px; margin-bottom:10px;'>";
			
			// Check which option was previously selected
			$selectedStandard = 'selected';
			$selectedExpress = '';
			if (isset($_POST['delivery_option']) && $_POST['delivery_option'] == 'express') {
				$selectedExpress = 'selected';
				$selectedStandard = '';
			}
			
			// Add tooltips for each delivery option
			$standardDeliveryTooltip = "title='Standard Delivery: \$5 per trip, within 2 working days after an order is placed'";
			$expressDeliveryTooltip = "title='Express Delivery: \$10 per trip, delivered within 24 hours after an order is placed'";
			
			echo "<option value='standard' $selectedStandard $standardDeliveryTooltip>Standard Delivery ($5)</option>";
			echo "<option value='express' $selectedExpress $expressDeliveryTooltip>Express Delivery ($10)</option>";
			echo "</select><br>";
			echo "</form>";
			echo "</div>";			
			
			// Determine shipping charge based on selection
			$shipCharge = 5.00; // Default to standard shipping
			if (isset($_POST['delivery_option']) && $_POST['delivery_option'] == 'express') {
				$shipCharge = 10.00;
			}
		}

		// Calculate GST and delivery charge
		$gstAmount = ($subTotal + $shipCharge) * $gstRate;
		$totalAmt = $subTotal + $shipCharge + $gstAmount;

		// Display the subtotal at the end of the shopping cart
		echo "<p style='text-align:right; font-size:20px; margin-top:10px;'>";
		echo "Subtotal = S$" . number_format($subTotal, 2) . "<br>";
		echo "Delivery Charge = S$" . number_format($shipCharge, 2) . "<br>";
		// Display the GST rate dynamically
		echo "GST (" . number_format($gstRate * 100, 0) . "%) = S$" . number_format($gstAmount, 2) . "<br>";
		echo "Total = S$" . number_format($totalAmt, 2) . "</p>";

		$_SESSION["SubTotal"] = round($subTotal, 2);  
		
		// To Do 7 (Practical 5):
		// Add PayPal Checkout button on the shopping cart page
		echo "<form method='post' action='checkoutProcess.php'>";
		echo "<input type='image' style='float:right;'
             src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif'>";
		echo "</form></p>";		
	}
	else {
		echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
	}
	$conn->close(); // Close database connection
}
else {
	echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
}
echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>