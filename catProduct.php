<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 60% width of viewport -->
<div style='width:60%; margin:auto;'>
<!-- Display Page Header - Category's name is read 
     from the query string passed from previous page -->
<div class="row" style="padding:5px">
	<div class="col-12">
		<span class="page-title"><?php echo "$_GET[catName]"; ?></span>
	</div>
</div>

<?php 
// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");

// To Do:  Starting ....
$cid=$_GET["cid"];

$qry = "SELECT p.ProductID, p.ProductTitle, p.ProductImage, p.Price, p.Quantity
		FROM CatProduct cp INNER JOIN product p ON cp.ProductID=p.ProductID
		WHERE cp.CategoryID=? ORDER BY ProductTitle";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


while ($row = $result->fetch_array()) {
	echo "<div class='row' style='padding:5px'>";



	$product = "productDetails.php?pid=$row[ProductID]";
	$formattedPrice = number_format($row["Price"], 2);
	echo "<div class='col-8'>";
	echo "<p><a href=$product>$row[ProductTitle]</a></p>";
	echo "Price:<span style='font-weight: bold; color: red;'>
		  S$ $formattedPrice</span>";
	echo "</div>";


	$img = "./Images/products/$row[ProductImage]";
	echo "<div class='col-4'>";
	echo "<img src='$img' />";
	echo "</div>";

	echo "</div>";
}
// To Do:  Ending ....

$conn->close(); // Close database connnection
echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>
