<?php
session_start();
include_once("mysql_conn.php");

if(isset($_GET["order_id"])) {
    $orderId = $_GET["order_id"];

    echo "<div class='receipt'>";
    // Styling for the receipt
    echo "<style>
            .receipt {
                font-family: Arial, sans-serif;
                margin: auto;
                width: 80%;
                padding: 20px;
                text-align: center;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
          </style>";

    // Display the shop name
    echo "<h1>FloraFarWest</h1>";
    echo "<h2>Receipt for Order: $orderId</h2>";

    // Fetch order details from OrderData
    $qryOrderData = "SELECT ShopCartID, ShipName, DateOrdered, ShipAddress FROM OrderData WHERE OrderID=?";
    $stmtOrderData = $conn->prepare($qryOrderData);
    $stmtOrderData->bind_param("i", $orderId);
    $stmtOrderData->execute();
    $resultOrderData = $stmtOrderData->get_result();
    $orderData = $resultOrderData->fetch_assoc();
    $stmtOrderData->close();

    if ($orderData) {
        // Display customer details and order information
        echo "<p>Customer Name: " . htmlspecialchars($orderData["ShipName"]) . "</p>";
        echo "<p>Order Date: " . $orderData["DateOrdered"] . "</p>";
        echo "<p>Shipping Address: " . htmlspecialchars($orderData["ShipAddress"]) . "</p>";

        $shopCartId = $orderData["ShopCartID"];

        // Initialize subtotal
        $subtotal = 0;

        // Fetch items from shopcartitem
        $qryItems = "SELECT * FROM shopcartitem WHERE ShopCartID=?";
        $stmtItems = $conn->prepare($qryItems);
        $stmtItems->bind_param("i", $shopCartId);
        $stmtItems->execute();
        $resultItems = $stmtItems->get_result();
        $stmtItems->close();

        if ($resultItems->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Product</th><th>Quantity</th><th>Price</th></tr>";
            while ($rowItem = $resultItems->fetch_assoc()) {
                $totalPrice = $rowItem["Quantity"] * $rowItem["Price"];
                $subtotal += $totalPrice;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($rowItem["Name"]) . "</td>";
                echo "<td>" . htmlspecialchars($rowItem["Quantity"]) . "</td>";
                echo "<td>$" . number_format($totalPrice, 2) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No details found for this order.</p>";
        }

        // Calculate tax, GST, and shipping charges
        $taxAmount = isset($_SESSION["Tax"]) ? $_SESSION["Tax"] : 0;
        $gstAmount = isset($_SESSION["GST"]) ? $_SESSION["GST"] : 0;
        $shipCharge = isset($_SESSION["ShippingCharge"]) ? $_SESSION["ShipCharge"] : 0;

        // Display subtotal, tax, GST, and total
        echo "<p>Subtotal: $" . number_format($subtotal, 2) . "</p>";
        echo "<p>GST (" . number_format($gstAmount * 100, 0) . "%) = S$" . number_format($taxAmount, 2) . "</p>";
        echo "<p>Shipping Charge: $" . number_format($shipCharge, 2) . "</p>";
        $total = $subtotal + $taxAmount + $shipCharge;
        echo "<p><strong>Total: $" . number_format($total, 2) . "</strong></p>";
    } else {
        echo "<p>Unable to find order details for this order.</p>";
    }

    echo "</div>";
    // Add a JavaScript print command
    echo "<script>window.onload = function() { window.print(); }</script>";
} else {
    echo "<p>Invalid order ID.</p>";
}
?>