<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<!-- Add the following link tag to include style.css -->
<link rel="stylesheet" type="text/css" href="css/style.css">

<!-- Create a container, 90% width of viewport -->
<div style='width:90%; margin:auto;'>

<?php 
$pid=$_GET["pid"]; // Read Product ID from query string

// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php"); 
$qry = "SELECT * from product where ProductID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $pid); 	// "i" - integer 
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// To Do 1:  Display Product information. Starting ....
while ($row = $result->fetch_array()) {
    // Display Page Header
    // Product's name is read from the "ProductTitle" column of "product" table.
    

    echo "<div class='row'>"; // Start a new row

    // Left column - display the product's image
    $img = "./Images/products/$row[ProductImage]";
    echo "<div class='col-sm-3' style='vertical-align:top; padding:5px'>"; 
    echo "<p><img src='$img' style='max-width: 100%; height: auto;' /></p>";
    echo "</div>";

    // Right column - display the product's description, specification, and price
    echo "<div class='col-sm-9' style='padding: 5px'>";
    echo "<span class='page-title'>$row[ProductTitle]</span>";
    echo "<p>$row[ProductDesc]</p>";

    // Left column - display the product's Specification,
    $qry = "SELECT s.SpecName, ps.SpecVal from productspec ps
            INNER JOIN specification s ON ps.SpecID=s.SpecID
            WHERE ps.ProductID=?
            ORDER BY ps.priority";
    $stmt = $conn->prepare ($qry);
    $stmt->bind_param("i", $pid); // "1" - integer
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();
    while ($row2 = $result2->fetch_array()) {
        echo $row2["SpecName"].": ".$row2["SpecVal"]."<br />";
    }
    echo "<br />";
    
    //Offer Indicator
    $formattedOriginalPrice = number_format($row["Price"], 2);
    $formattedPrice = number_format($row["OfferedPrice"], 2);
    if ($row['Offered'] == 1) {
        // Calculate the percentage discount
        $discountPercentage = round((($row["Price"] - $row["OfferedPrice"]) / $row["Price"]) * 100);
        echo "<div style='display: flex; align-items: center;'>";

        // "On Offer" text
        echo "<span style='color: red; margin-right: 10px;'>On Offer</span>";

        // Discount percentage box
        echo "<div style='border: 1px solid #ccc; padding: 5px; border-radius: 5px; display: inline-block;'>";
        echo "<p style='margin: 0;'><span style='font-weight: bold; color: red;'>$discountPercentage%</span> Off</p>";
        echo "</div>";

        // Close the flex container
        echo "</div>";

        // Original Price with strikethrough
        echo "<p style='margin: 0; font-weight: bold;'>Original Price: <span style='text-decoration: line-through; color: grey;'>S$ " . number_format($row["Price"], 2) . "</span></p>";;

        // Display the discount percentage and Offered Price
        echo "<p style='font-weight: bold; color: red;'>Offered Price: <span style='font-size: 1.5em;'>S$ " . number_format($row["OfferedPrice"], 2) . "</span></p>";

    } else {
        // Display the regular price
        echo "<p style='font-weight: bold;'>Price: <span style='color:red; font-size: 1.5em;'>S$ $formattedOriginalPrice</span></p>";
    }

    // Right column - display the Add to Cart button and quantity input
    echo "<div style='clear: both;'></div>"; // Add a clearfix
    echo "<div class='col-sm-12' style='padding: 5px'>";
    if ($row["Quantity"] <= 0) {
        // Out of Stock indicator and disable the Add to Cart button
        echo "<p style='color:red;'>Out of Stock</p>";
        echo "<button type='button' disabled>Add to Cart</button>";
    } else {
        // Display the form for adding the product to the shopping cart
        echo "<form action='cartFunctions.php' method='post'>";
        echo "<input type='hidden' name='action' value='add' />";
        echo "<input type='hidden' name='product_id' value='$pid' />";
        echo "Quantity: <input type='number' name='quantity' value='1'
            min='1' max='10' style='width:40px' required /> ";
        echo "<button type='submit'>Add to Cart</button>";
        echo "</form>";
    }

    echo "</div>"; // End of right column
    echo "</div>"; // End of row

    // To Do 1:  Ending ....

    $conn->close(); // Close database connection
    echo "</div>"; // End of container
    include("footer.php"); // Include the Page Layout footer
}
?>  