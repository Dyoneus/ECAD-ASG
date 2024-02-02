<?php 
// Detect the current session
session_start(); 
// Include the Page Layout header
include("header.php"); 
?>
<script type="text/javascript">
function validateForm() {
    // Check if password matched
    if (document.register.password.value != document.register.password2.value) {
        alert("Passwords do not match!");
        return false;
    }
    // Check if telephone number is entered correctly
    if (document.register.phone.value != "") {
        var str = document.register.phone.value;
        if (str.length != 8) {
            alert("Please enter an 8-digit phone number.");
            return false;
        } else if (str.substr(0,1) != "6" && str.substr(0,1) != "8" && str.substr(0,1) != "9") {
            alert("Phone number in Singapore should start with 6, 8 or 9.");
            return false;        
        }
    }
    return true; // No error found
}
</script>

<!-- Central container for the form -->
<div class="container">
    <form name="register" action="addMember.php" method="post" onsubmit="return validateForm()" class="border p-4 bg-light shadow">
        <div class="text-center mb-4">
            <h2 class="page-title">Member Registration</h2>
            <p class="lead">Join our community. It only takes a minute.</p>
        </div>
        <!-- Form fields with Bootstrap classes -->
        <div class="mb-3">
            <label class="form-label" for="name">Name (required):</label>
            <input class="form-control" name="name" id="name" type="text" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="address">Address:</label>
            <textarea class="form-control" name="address" id="address" rows="4"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="country">Country:</label>
            <input class="form-control" name="country" id="country" type="text">
        </div>
        <div class="mb-3">
            <label class="form-label" for="phone">Phone:</label>
            <input class="form-control" name="phone" id="phone" type="text">
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">Email Address (required):</label>
            <input class="form-control" name="email" id="email" type="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Password (required):</label>
            <input class="form-control" name="password" id="password" type="password" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="password2">Retype Password (required):</label>
            <input class="form-control" name="password2" id="password2" type="password" required>
        </div>
        <div class="mb-3 text-center">       
            <button type="submit" class="btn btn-primary">Register</button>
        </div>
    </form>
</div>
<?php 
// Include the Page Layout footer
include("footer.php"); 
?>