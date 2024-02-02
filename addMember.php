<?php
session_start();

include_once("mysql_conn.php");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $address = $_POST["address"];
    $country = $_POST["country"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Define the INSERT SQL statement
    $qry = "INSERT INTO Shopper (Name, Address, Country, Phone, Email, Password)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($qry);
    // "ssssss" - 6 string parameters
    $stmt->bind_param("ssssss", $name, $address, $country, $phone, $email, $password);

    if ($stmt->execute()){ // SQL statement executed successfully
        // Retrieve the Shopper ID assigned to the new shopper for the message
        $qry = "SELECT LAST_INSERT_ID() AS ShopperID";
        $result = $conn->query($qry); // Execute the SQL and get the returned result
        $shopperID = "";
        while ($row = $result->fetch_array()){
            $shopperID = $row["ShopperID"];
        }

        // Successful message (do not save ShopperID and ShopperName in session)
		$Message = "<div class='alert alert-success text-center' role='alert'>
						<h4 class='alert-heading'>Registration Successful!</h4><br/>
                        Your ShopperID is $shopperID.<br/>
                        Please proceed to <a href='login.php' class='alert-link'>login</a> to access your account.
                    </div>";
    } else { // Error message
        $Message = "<div class='alert alert-danger' role='alert'>
                        Error inserting record
                    </div>";
    }

    // Release the resource allocated for prepared statement
    $stmt->close();
    // Close database connection
    $conn->close();
} else {
    // Redirect back to the registration page if the form wasn't submitted
    header("Location: register.php");
    exit;
}

// Include the Page Layout header
include("header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-sm-12">
            <?php echo $Message; ?>
        </div>
    </div>
</div>

<?php
// Include the Page Layout footer
include("footer.php");
?>