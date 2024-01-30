<?php
// Include the database connection file
include_once("mysql_conn.php");

// Detect the current session
session_start();

// Include the Page Layout header
include("header.php");

// Reading inputs entered in previous page
$email = $_POST["email"];
$pwd = $_POST["password"];

// Create SQL query to select user record from database
$sql = "SELECT Name, ShopperID FROM shopper WHERE email = '$email' AND password = '$pwd'";

// Execute the query and get the result set
$result = $conn->query($sql);

// If no record is found, display an error message
if ($result->num_rows == 0) {
    echo "<h3 style='color:red'>Invalid Login Credentials</h3>";
}else {
    // Save user's info in session variables
    $row = $result->fetch_assoc();
    $_SESSION["ShopperName"] = $row["Name"];
    $_SESSION["ShopperID"] = $row["ShopperID"];

  // To Do 2 (Practical 4): Get active shopping cart
  $qry = 'SELECT sc.ShopCartID, IFNULL(SUM(sci.Quantity), 0) AS TotalQuantity
        FROM ShopCart sc LEFT JOIN ShopCartItem sci
        ON sc.ShopCartID = sci.ShopCartID
        WHERE sc.ShopperID = ? AND sc.OrderPlaced = 0
        GROUP BY sc.ShopCartID';

  $stmt = $conn->prepare($qry);
  $stmt->bind_param("i", $_SESSION["ShopperID"]);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION["NumCartItem"] = $row["TotalQuantity"];
    $_SESSION["Cart"] = $row["ShopCartID"];
  } else {
      // If no cart exists, set NumCartItem to 0
      $_SESSION["NumCartItem"] = 0;
  }

  $stmt->close();
  $conn->close();

  // Redirect to home page
  header("Location: index.php");
  exit;
}
    
// Include the Page Layout footer
include("footer.php");
?>