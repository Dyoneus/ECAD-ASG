<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

// Check if user logged in
if (!isset($_SESSION["ShopperID"])) {
    // redirect to login page if the session variable shopperid is not set
    header ("Location: login.php");
    exit;
}
?>

<script type="text/javascript">
function validateForm()
{
    if (document.changePwd.pwd1.value != document.changePwd.pwd2.value) {
        alert("Passwords not matched!");
        return false;   // cancel submission
    }
    return true;  // No error found
}
</script>

<div class="container mt-4">
    <form name="changePwd" method="post" onsubmit="return validateForm()">
        <div class="form-group row">
            <div class="col-sm-12">
                <h4 class="page-title">Change Password</h4>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="pwd1">New Password:</label>
            <div class="col-sm-9">
                <input class="form-control" name="pwd1" id="pwd1" type="password" required />
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="pwd2">Retype Password:</label>
            <div class="col-sm-9">
                <input class="form-control" name="pwd2" id="pwd2" type="password" required />
            </div>
        </div>
        <div class="form-group row">       
            <div class="col-sm-9 offset-sm-3">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>

    <?php
    if (isset($_POST["pwd1"])) {
        include_once("mysql_conn.php");
        $pwd = $_POST["pwd1"];
        $qry = "UPDATE Shopper SET Password=? WHERE ShopperID=?";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("si", $pwd, $_SESSION["ShopperID"]);

        if ($stmt->execute()){
            echo "<div class='alert alert-success text-center' role='alert'>Successfully changed password!</div>";
        }
        else{
            echo "<div class='alert alert-danger' role='alert'>Error inserting record! Please try again later.</div>";
        }
        $stmt->close();
        $conn->close();
    }
    ?>
</div>

<?php include("footer.php"); ?>