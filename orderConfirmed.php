<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
include_once("mysql_conn.php"); // Database connection

if(isset($_SESSION["OrderID"])) {	
    $orderId = $_SESSION["OrderID"];
    echo "<div class='order-confirmation'>";

    // Echo the opening of the main content container and row
    echo "<div class='row'>";
    echo "<div class='col-sm-12' style='padding:15px;'>";

    // Styling for the order confirmation and buttons
    echo "<style>
            .order-confirmation {
                margin: auto;
                width: 100%;
                padding: 20px;
                border-radius: 5px;
                text-align: center;
            }
            .order-confirmation h2 {
                color: #5cb85c;
                margin-bottom: 30px;
            }
            .order-details {
                margin-top: 20px;
                text-align: center;
                padding: 15px;
                border-radius: 5px;
            }
            .order-details table {
                width: 100%;
                border-collapse: collapse;
                margin: auto;
            }
            .order-details th, .order-details td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            .order-details th {
                background-color: #f2f2f2;
            }
            .button-container {
                margin-top: 30px;
                text-align: center;
            }
            .custom-button {
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                color: white;
                background-color: #5cb85c;
                margin: 5px;
                display: inline-block;
            }
            .custom-button:hover {
                background-color: #4cae4c;
            }
          </style>";

    echo "<h2>Thank You for Your Purchase!</h2>";
    echo "<p>Your order has been confirmed. Your order number is <strong>$orderId</strong>.</p>";

    // Retrieve ShopCartID from orderdata
    $qry = "SELECT ShopCartID FROM orderdata WHERE OrderID=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $shopCartId = $row["ShopCartID"];
    }
    $stmt->close();

    // Initialize subtotal
    $subtotal = 0;

    // Retrieve and display order details from shopcartitem
    if (isset($shopCartId)) {
        $qry = "SELECT * FROM shopcartitem WHERE ShopCartID=?";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("i", $shopCartId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        echo "<div class='order-details'>";
        echo "<h3>Order Details</h3>";
        echo "<table>";
        echo "<tr><th>Product</th><th>Quantity</th><th>Price</th></tr>";

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $totalPrice = $row["Quantity"] * $row["Price"];
                $subtotal += $totalPrice;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["Quantity"]) . "</td>";
                echo "<td>$" . number_format($totalPrice, 2) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No details found for this order.</td></tr>";
        }

        // Calculate and display financial summary within the table
        $taxAmount = isset($_SESSION["Tax"]) ? $_SESSION["Tax"] : 0;
        $gstAmount = isset($_SESSION["GST"]) ? $_SESSION["GST"] : 0;
        $shipCharge = isset($_SESSION["ShippingCharge"]) ? $_SESSION["ShipCharge"] : 0;
        $total = $subtotal + $taxAmount + ($subtotal * $gstAmount / 100) + $shipCharge;

        echo "<tr><td colspan='2'>Subtotal</td><td>$" . number_format($subtotal, 2) . "</td></tr>";
        echo "<tr><td colspan='2'>GST (" . number_format($gstAmount * 100, 0) . "%)</td><td>$" . number_format($taxAmount, 2) . "</td></tr>";
		// echo "<p>GST (" . number_format($gstAmount * 100, 0) . "%) = S$" . number_format($taxAmount, 2) . "</p>";
        echo "<tr><td colspan='2'>Shipping Charge</td><td>$" . number_format($shipCharge, 2) . "</td></tr>";
        echo "<tr><td colspan='2'><strong>Total</strong></td><td><strong>$" . number_format($total, 2) . "</strong></td></tr>";

        echo "</table>";
        echo "</div>";
    }

    // Buttons
    echo "<div class='button-container'>";
    echo "<button class='custom-button' onclick='window.open(\"printReceipt.php?order_id=$orderId\", \"_blank\", \"toolbar=yes,scrollbars=yes,resizable=yes,top=500,left=500,width=400,height=400\")'>Print Receipt</button><br>";
    echo "<a href='index.php' class='custom-button'>Continue Shopping</a>";
    echo "</div>";

    echo "</div>"; // Closing div for col-sm-12
    echo "</div>"; // Closing div for row
} else {
    echo "<p>There seems to be an issue with your order. Please contact customer support.</p>";
}

include("footer.php");
$conn->close();
?>