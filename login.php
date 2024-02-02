<?php 
// Detect the current session
session_start(); 
// Include the Page Layout header
include("header.php"); 
?>
<!-- Create a centrally located container -->
<div class="container">
    <!-- Create a HTML Form within the container -->
    <form action="checkLogin.php" method="post" class="border p-4 bg-light shadow">
        <!-- 1st row - Header Row with a nicer look -->
        <div class="text-center mb-4">
            <h2 class="page-title">Member Login</h2>
            <p class="lead">Please enter your credentials to login.</p>
        </div>
        <!-- 2nd row - Entry of email address with updated styling -->
        <div class="mb-3">
            <label class="form-label" for="email">Email Address:</label>
            <input class="form-control" type="email" name="email" id="email" required placeholder="Enter your email">
        </div>
        <!-- 3rd row - Entry of password with updated styling -->
        <div class="mb-3">
            <label class="form-label" for="password">Password:</label>
            <input class="form-control" type="password" name="password" id="password" required placeholder="Enter your password">
        </div>
        <!-- 4th row - Login button with Bootstrap button styling -->
        <div class="mb-3 text-center">       
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
        <!-- Additional links for sign up and forgot password -->
        <div class="mb-3 text-center">
            <p>Don't have an account? <a href="register.php">Sign up here</a>.</p>
        </div>
    </form>
</div>
<?php 
// Include the Page Layout footer
include("footer.php"); 
?>