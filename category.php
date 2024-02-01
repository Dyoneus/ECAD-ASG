<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<!-- Add the following link tag to include style.css -->
<link rel="stylesheet" type="text/css" href="css\style.css">

<!-- Create a container -->
<div class="container">

    <!-- Display Page Header -->
    <div class="row header-row">
        <div class="col-12">
            <span class="page-title">Product Categories</span>
            <p>Select a category listed below:</p>
        </div>
    </div>

    <div class="row">

        <?php 
        // Include the PHP file that establishes database connection handle: $conn
        include_once("mysql_conn.php");

        // To Do:  Starting ....
        $qry = "SELECT * FROM Category ORDER BY CatName";  // Form SQL to select all categories
        $result = $conn->query($qry);  // Execute the SQL and get the result

        // Display each category in a box container
        while ($row = $result->fetch_array()) {
            // Left column - display a text link showing the category's name,
            //               display category's description in a new paragraph
            $catname = urlencode($row["CatName"]);
            $catproduct = "catProduct.php?cid=$row[CategoryID]&catName=$catname";
            echo "<div class='col-md-4 col-sm-6 category-box'>";  // Each category takes 4 columns for larger screens, 6 columns for smaller screens
            echo "<h2><a href=$catproduct style='text-decoration: none; color: #1a6692;'>$row[CatName]</a></h2>";
            echo "<p>$row[CatDesc]</p>";
            // Right column - display the category's image
            $img = "./Images/category/$row[CatImage]";
            echo "<img src='$img' alt='$row[CatName]' style='width: 100%; height: auto;' />";
            echo "</div>"; // End of category box
        }
        // To Do:  Ending ....

        $conn->close(); // Close database connection
        ?>

    </div> <!-- End of row -->

</div> <!-- End of container -->

<?php include("footer.php"); // Include the Page Layout footer ?>