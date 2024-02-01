<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<!-- Add the following link tag to include style.css -->
<link rel="stylesheet" type="text/css" href="css/style.css">

<!-- Create a container -->
<div class="container">
    <!-- Display Page Header - Category's name is read 
         from the query string passed from the previous page -->
    <div class="row" style="padding: 5px;">
        <div class="col-12">
            <span class="page-title"><?php echo "$_GET[catName]"; ?></span>
        </div>
    </div>

    <?php 
    // Include the PHP file that establishes the database connection handle: $conn
    include_once("mysql_conn.php");

    // To Do:  Starting ....
    $cid = $_GET["cid"];

    $qry = "SELECT p.ProductID, p.ProductTitle, p.ProductImage, p.Price, p.Quantity, p.Offered, p.OfferedPrice
            FROM CatProduct cp INNER JOIN product p ON cp.ProductID=p.ProductID
            WHERE cp.CategoryID=? ORDER BY ProductTitle";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $cid); // "i" - integer
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // Start a new row
    echo "<div class='row'>";

    // Display each product in a column
    while ($row = $result->fetch_array()) {
        $product = "productDetails.php?pid=$row[ProductID]";
        echo "<div class='col-md-4'>";
        echo "<a href='$product' style='text-decoration: none; color: inherit;'>"; // Remove underline and blue color

        echo "<div class='product-item'>";

        $img = "./Images/products/$row[ProductImage]";
        echo "<img src='$img' alt='$row[ProductTitle]' style='width: 100%;'><br />";

        // Left column - display a text link showing the product's name,
        // display the selling price in red in a new paragraph
        //$formattedPrice = number_format($row["Price"], 2);
        echo "<p style='margin-bottom: 0; font-size: 20px; color:#1a6692;'>$row[ProductTitle]</p><br />";

        // Offer Indicator
        if ($row['Offered'] == 1) {
            // Calculate the percentage discount
            $discountPercentage = round((($row["Price"] - $row["OfferedPrice"]) / $row["Price"]) * 100);

            // Original Price with strikethrough

            // Display "On Offer" and the discount percentage box side by side
            echo "<div style='display: flex; align-items: center;'>";

            // "On Offer" text
            echo "<span style='color: red; margin-right: 10px;'>On Offer</span>";

            // Discount percentage box
            echo "<div style='border: 1px solid #ccc; padding: 5px; border-radius: 5px; display: inline-block;'>";
            echo "<p style='margin: 0;'><span style='font-weight: bold; color: red;'>$discountPercentage% Off</span></p>";
            echo "</div>";

            // Close the flex container
            echo "</div>";

            echo "<p style='margin: 0; font-weight: bold;'>Price: <del>S$ " . number_format($row["Price"], 2) . "</del>" . " " . "<span style='font-weight: bold; color: red; font-size: 1.5em;'>S$ " . number_format($row["OfferedPrice"], 2) . "</span></p>";
        } else {
            // Display the regular price if no offer
            echo "<p style='margin-top: 0; font-weight: bold;'>Price: <span style='font-weight: bold; color: red; font-size: 1.5em;'>S$ " . number_format($row["Price"], 2) . "</span></p>";
        }

        echo "</div>";
        echo "</a>"; // End of the link
        echo "</div>"; // End of a column
    }

    // End the row
    echo "</div>";

    // To Do:  Ending ....

    $conn->close(); // Close database connection
    ?>

</div> <!-- End of container -->

<?php include("footer.php"); // Include the Page Layout footer ?>