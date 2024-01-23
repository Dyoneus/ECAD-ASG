<?php
// Detect the current session
session_start();

// Include the Page Layout header
include("header.php");

// Reading inputs entered in previous page
$email = $_POST["email"];
$pwd = $_POST["password"];

// To Do 1 (Practical 2): Validate login credentials with database
include_once("mysql_conn.php");

$qry= "SELECT ShopperID, Name, Password FROM Shopper WHERE Email = ?";
$stmt = $conn->prepare($qry);
$stmt -> bind_param("s", $email); 

if ($stmt -> execute()){
	$result = $stmt->get_result();
	if ($result->num_rows != 0){
		//While loop in the case that there are multiple results due to duplicate email
		while($row = $result->fetch_array()){
			// Get the hashed password from database
			$hashed_pwd = $row["Password"];
			// Verifies that a password matches a hash //Checking of password
			if (password_verify($pwd, $hashed_pwd) == true){
				$_SESSION["ShopperName"] = $row["Name"];
				$_SESSION["ShopperID"] = $row["ShopperID"];
				
				//TO DO 2:  retrieve the “ShopCartID” associated to the “ShopperID” of the login account
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

				header("Location: index.php");
			}
			//Invalid Login Credentials - Incorrect Password
			else{
				echo  "<h3 style='color:red'>Invalid Login Credentials! Click <a href='login.php'><u>here</u></a> to return to login page.</h3>";
			}
		}	
	}
	else {
		//Invalid Login Credentials - Email not found
		echo  "<h3 style='color:red'>Email not found!<br/><br>
		To sign up a new account: <a href='register.php'><u>Register</u></a><br/>
		To return back to login page: <a href='login.php'><u>Login</u></a> 
		</h3>";
	}	
}
else {
	//Query failed to execute
	echo  "<h3 style='color:red'>Login failure. Please try again.</h3>";
}	

//Close database connection
$stmt->close();
$conn->close();

// Include the Page Layout footer
include("footer.php");
?>