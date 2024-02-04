<?php 
session_start();
if (isset($_POST['action'])) {
 	switch ($_POST['action']) {
    	case 'add':
        	addItem();
            break;
        case 'update':
            updateItem();
            break;
		case 'remove':
            removeItem();
            break;
    }
}

function addItem() {
	// Check if user logged in 
	if (!isset($_SESSION["ShopperID"])) {
		// redirect to login page if the session variable shopperid is not set
		header ("Location: login.php");
		exit;
	}
	// TO DO 1
	// Write code to implement: if a user clicks on "Add to Cart" button, insert/update the 
	// database and also the session variable for counting number of items in shopping cart.
	include_once("mysql_conn.php"); // Establish database connection handle: $conn
	// Check if a shopping cart exist, if not create a new shopping cart
	if (!isset($_SESSION["Cart"])){
		$qry = "INSERT INTO Shopcart(ShopperID) VALUES(?)";
		$stmt = $conn->prepare($qry);
		$stmt->bind_param("i", $_SESSION["ShopperID"]); //i - integer
		$stmt->execute();
		$stmt->close();
		$qry = "SELECT LAST_INSERT_ID() AS ShopCartID";
		$result = $conn->query($qry);
		$row = $result->fetch_array();
		$_SESSION["Cart"] = $row["ShopCartID"];
	}

  	// If the ProductID exists in the shopping cart, 
  	// update the quantity, else add the item to the Shopping Cart.
  	$pid = $_POST["product_id"];
	$quantity = $_POST["quantity"];
	$qry = "SELECT Quantity FROM ShopCartItem WHERE ShopCartID = ? AND ProductID = ?";
	//$qry = "SELECT * FROM ShopCartItem WHERE ShopCartID = ? AND ProductID = ?";

	$stmt = $conn->prepare($qry);
	$stmt->bind_param("ii", $_SESSION["Cart"], $pid); 
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) { // Selected product exists in shopping cart
        $row = $result->fetch_assoc();
        $newQty = $row["Quantity"] + $quantity;
        // Update the quantity of the existing item
        $qry = "UPDATE ShopCartItem SET Quantity=? WHERE ShopCartID=? AND ProductID=?";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("iii", $newQty, $_SESSION["Cart"], $pid);
        $stmt->execute();
        $stmt->close();
    } else {
		// Check if there is an offered price
        $qryOfferedPrice = "SELECT Price, OfferedPrice FROM Product WHERE ProductID = ?";
        $stmtOfferedPrice = $conn->prepare($qryOfferedPrice);
        $stmtOfferedPrice->bind_param("i", $pid);
        $stmtOfferedPrice->execute();
        $resultOfferedPrice = $stmtOfferedPrice->get_result();
        $rowOfferedPrice = $resultOfferedPrice->fetch_assoc();
        $stmtOfferedPrice->close();

        // Use the offered price if it is not null, otherwise use the regular price
        $priceToUse = isset($rowOfferedPrice["OfferedPrice"]) ? $rowOfferedPrice["OfferedPrice"] : $rowOfferedPrice["Price"];

        // Insert the item into the shopping cart with the correct price
        $qry = "INSERT INTO ShopCartItem(ShopCartID, ProductID, Price, Name, Quantity)
                SELECT ?, ?, ?, ProductTitle, ? FROM Product WHERE ProductID = ?";      
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("iidii", $_SESSION["Cart"], $pid, $priceToUse, $quantity, $pid);
        $stmt->execute();
        $stmt->close();
		/*
		$qry = "INSERT INTO ShopCartItem(ShopCartID, ProductID, Price, Name, Quantity)
				SELECT ?, ?, Price, ProductTitle, ? FROM Product WHERE ProductID = ?";		
		$stmt = $conn->prepare($qry);
		
		//"iiii" - 3 integers
		
		$stmt->bind_param("iiii", $_SESSION["Cart"], $pid, $quantity, $pid);
		$stmt->execute();
		$stmt->close();
		*/
		$addNewItem = 1;	
		
	}
  	

	// Recalculate the total number of items in the cart
    recalculateCartItemCount($conn);
	
	$conn->close();

	// Redirect shopper to shopping cart page
    header("Location: shoppingCart.php");
    exit;

}

// Function to recalculate the total item count in the cart
function recalculateCartItemCount($conn) {
    $qry = "SELECT SUM(Quantity) AS ItemCount FROM ShopCartItem WHERE ShopCartID = ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $_SESSION["Cart"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $_SESSION["NumCartItem"] = $row["ItemCount"];
    $stmt->close();
}


function updateItem() {
	// Check if shopping cart exists 
	if (!isset($_SESSION["Cart"])) {
		// redirect to login page if the session variable cart is not set
		header ("Location: login.php");
		exit;
	}
	// TO DO 2
	// Write code to implement: if a user clicks on "Update" button, update the database
	// and also the session variable for counting number of items in shopping cart.
	$cartid = $_SESSION["Cart"];
	$pid = $_POST["product_id"];
	$quantity = $_POST["quantity"];
	include_once("mysql_conn.php"); // Establish database connection handle: $conn
	$qry = "UPDATE ShopCartItem SET Quantity = ? WHERE ProductID= ? AND ShopCartID= ?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("iii", $quantity, $pid, $cartid); // i - integer
	$stmt->execute();
	$stmt->close();
	

	// Recalculate the total number of items in the cart
    recalculateCartItemCount($conn);
	$conn->close();
	header("Location: shoppingCart.php");
	exit;
}

function removeItem() {
	if (! isset($_SESSION["Cart"])) {
		// redirect to login page if the session variable cart is not set
		header ("Location: login.php");
		exit;
	}
	// TO DO 3
	// Write code to implement: if a user clicks on "Remove" button, update the database
	// and also the session variable for counting number of items in shopping cart.
	$cartid = $_SESSION["Cart"];
	$pid = $_POST["product_id"];
	include_once("mysql_conn.php"); // Establish database connection handle: $conn

	$qry = "DELETE FROM shopcartitem WHERE ProductID = ? AND ShopCartID = ?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("ii", $pid, $cartid); // ii - integer
	$stmt->execute();
	$stmt->close();
	

	if(isset($_SESSION["NumCartItem"])){
		$_SESSION["NumCartItem"] = $_SESSION["NumCartItem"] - 1;
	}
	else{
		$_SESSION["NumCartItem"] = 0;
	}

	// Recalculate the total number of items in the cart
	recalculateCartItemCount($conn);
	$conn->close();

	header("Location: shoppingCart.php");
	exit;
}		
?>