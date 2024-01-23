<?php
//Detect the current session
session_start();
//Include the Page Layout header
include("header.php");

if (isset($_SESSION["ShopperID"])){ //Redirect user to index if already logged in
	header ("Location: index.php");
	exit;
}
?>

<div style="width:80%; margin:auto;">
<form action="checkLogin.php" method="post">
<!--First Row (Header)-->
<div class="form-group row">
    <div class="col-sm-9 offset-sm-3">
        <span class="page-title">Member Login</span>
    </div>
</div>
<!--Second Row (Email)-->
<div class="form-group row">
    <label class="col-sm-3 col-form-label" for="email">
        Email Address:
    </label>
    <div class="col-sm-9">
        <input class="form-control" type="email"
        name="email" id="email" required/>
    </div>
</div>
<!--Third Row (Password)-->
<div class="form-group row">
    <label class="col-sm-3 col-form-label" for="password">
        Password:
    </label>
    <div class="col-sm-9">
        <input class="form-control" type="password"
        name="password" id="password" required/>
    </div>
</div>
<!--Fourth Row (Login)-->
<div class="form-group row">
    <div class="col-sm-9 offset-sm-3">
        <button type="submit">Login</button>
        <p>Please sign up if you do not have an account. </p>
        <p><a href="forgetPassword.php">Forget Password</a></p>
    </div>
</div>
</form>
</div>
<?php
//Include the Page Layout footer
include("footer.php");
?>