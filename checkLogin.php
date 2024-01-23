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
  $qry = 'SELECT sc.ShopCartID, COUNT(sci.ProductID) AS NumItems
      FROM ShopCart sc LEFT JOIN ShopCartItem sci
      ON sc.ShopCartID=sci.ShopCartID
      WHERE sc.ShopperID=? AND sc.OrderPlaced=0';
  
  $stmt = $conn->prepare($qry);
  $stmt->bind_param("i", $_SESSION["ShopperID"]);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($stmt -> execute()){
    $result = $stmt->get_result();
    if ($result->num_rows != 0){
      while($row = $result->fetch_array()){
        $_SESSION["NumCartItem"] = $row["NumItems"];	
        $_SESSION["Cart"] = $row["ShopCartID"];
      }
    }
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