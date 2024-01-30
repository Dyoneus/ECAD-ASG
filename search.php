<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<div style="width:80%; margin:auto;"> <!-- Container -->

    <form name="frmSearch" method="get" action="">
        <div class="mb-3 row"> <!-- 1st row -->
            <div class="col-sm-9 offset-sm-3">
                <span class="page-title">Product Search</span>
            </div>
        </div> <!-- End of 1st row -->

        <div class="mb-3 row"> <!-- 2nd row -->
            <label for="keywords" class="col-sm-3 col-form-label">Product Title:</label>
            <div class="col-sm-6">
                <input class="form-control" name="keywords" id="keywords" type="search" />
            </div>
        </div> <!-- End of 2nd row -->

        <div class="mb-3 row"> <!-- 3rd row -->
    <label for="minPrice" class="col-sm-3 col-form-label">Min Price:</label>
    <div class="col-sm-3">
        <input class="form-control" name="minPrice" id="minPrice" type="number" min="0" step="0.01" />
    </div>
    <label for="maxPrice" class="col-sm-3 col-form-label">Max Price:</label>
    <div class="col-sm-3">
        <input class="form-control" name="maxPrice" id="maxPrice" type="number" min="0" step="0.01" />
    </div>
</div> <!-- End of 3rd row -->

<div class="mb-3 row"> <!-- 4th row -->
    <div class="col-sm-9 offset-sm-3">
        <button type="submit">Search</button>
    </div>
</div> <!-- End of 4th row -->

</form>


    <?php
    include_once("mysql_conn.php");

    if (isset($_GET["keywords"]) && trim($_GET['keywords']) != "" && isset($_GET['minPrice']) && trim($_GET['minPrice']) == "" && isset($_GET['maxPrice']) && trim($_GET['maxPrice']) == "") {
        // Search by title only
        $searchText = $conn->real_escape_string($_GET["keywords"]);
        $qry = "SELECT ProductID, ProductTitle, ProductDesc FROM product 
                WHERE ProductTitle LIKE '%$searchText%' OR ProductDesc LIKE '%$searchText%'";
    }
    elseif (isset($_GET["keywords"]) && trim($_GET['keywords']) == "" && isset($_GET['minPrice']) && isset($_GET['maxPrice']) && trim($_GET['maxPrice']) == "") {
        // Search by price range only
        $minPrice = floatval($_GET['minPrice']);
        $qry = "SELECT ProductID, ProductTitle, ProductDesc FROM product 
                WHERE Price >= $minPrice";
    }
    elseif (isset($_GET["keywords"]) && trim($_GET['keywords']) == "" && isset($_GET['minPrice']) && trim($_GET['minPrice']) == "" && isset($_GET['maxPrice'])) {
        // Search by price range only
        $maxPrice = floatval($_GET['maxPrice']);
        $qry = "SELECT ProductID, ProductTitle, ProductDesc FROM product 
                WHERE Price <= $maxPrice";
    }
    elseif (isset($_GET["keywords"]) && trim($_GET['keywords']) == "" && isset($_GET['minPrice']) && isset($_GET['maxPrice'])) {
        // Search by price range only
        $minPrice = floatval($_GET['minPrice']);
        $maxPrice = floatval($_GET['maxPrice']);
        $qry = "SELECT ProductID, ProductTitle, ProductDesc FROM product 
                WHERE Price BETWEEN $minPrice AND $maxPrice";
    }
    elseif (isset($_GET["keywords"]) && trim($_GET['keywords']) != "" && isset($_GET['minPrice']) && isset($_GET['maxPrice']) && trim($_GET['maxPrice']) == "") {
        // Search by title and price range
        $searchText = $conn->real_escape_string($_GET["keywords"]);
        $minPrice = floatval($_GET['minPrice']);
        $qry = "SELECT ProductID, ProductTitle, ProductDesc FROM product 
                WHERE (ProductTitle LIKE '%$searchText%' OR ProductDesc LIKE '%$searchText%') 
                AND Price >= $minPrice";
    }
    elseif (isset($_GET["keywords"]) && trim($_GET['keywords']) != "" && isset($_GET['minPrice']) && trim($_GET['minPrice']) == "" && isset($_GET['maxPrice'])) {
        // Search by title and price range
        $searchText = $conn->real_escape_string($_GET["keywords"]);
        $maxPrice = floatval($_GET['maxPrice']);
        $qry = "SELECT ProductID, ProductTitle, ProductDesc FROM product 
                WHERE (ProductTitle LIKE '%$searchText%' OR ProductDesc LIKE '%$searchText%') 
                AND Price <= $maxPrice";
    }
    elseif (isset($_GET["keywords"]) && trim($_GET['keywords']) != "" && 
            isset($_GET['minPrice']) && isset($_GET['maxPrice'])) {
        // Search by title and the entered price, assume the other price is infinity
        $searchText = $conn->real_escape_string($_GET["keywords"]);
        $minPrice = floatval($_GET['minPrice']);
        $maxPrice = floatval($_GET['maxPrice']);
        $qry = "SELECT ProductID, ProductTitle, ProductDesc FROM product 
                WHERE (ProductTitle LIKE '%$searchText%' OR ProductDesc LIKE '%$searchText%') 
                AND (Price >= $minPrice AND Price <= $maxPrice)";
    }
    else {
        // No search criteria provided
        echo "<p>No search criteria provided.</p>";
    }
    
    if (isset($qry)) {
        $qry .= " ORDER BY ProductID";
        $stmt = $conn->prepare($qry);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    
        if ($result->num_rows > 0) {
            echo "<span style='font-weight: bold;'>Search results:</span>";
            echo "<table class='table'>";
            echo "<thead><tr><th>Product Title</th><th>Product Description</th></tr></thead>";
            echo "<tbody>";
    
            while ($row = $result->fetch_array()) {
                $product = "productDetails.php?pid=$row[ProductID]";
                echo "<tr><td><a href='$product'>$row[ProductTitle]</a></td><td>$row[ProductDesc]</td></tr>";
            }
    
            echo "</tbody></table>";
        } else {
            echo "<p>No results found.</p>";
        }
    }

    echo "</div>"; // End of container
    include("footer.php"); // Include the Page Layout footer
    ?>