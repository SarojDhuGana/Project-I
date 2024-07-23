<?php
require_once ('SellerManager.php');

// to send form request
if (!empty($_POST)) {

    $sellerDetails = [
        'full_name' => $_POST['full_name'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'phone_number' => $_POST['phone_number'],
        'company_name' => $_POST['company_name']
    ];

    $sellerManager = new SellerManager();
    $response = $sellerManager->createSeller($sellerDetails);
    $responseDecode = json_decode($response, true);

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <title>Seller Registration Form</title>
    <link rel="stylesheet" href="sellerRegister.css">
    <link rel="stylesheet" href="../bdashboard.css">

    <style>

    </style>
</head>

<body>
<div class="container">
        <header>Seller Registration</header>
        <?php
        if ($responseDecode['success'] == false) {
            echo "<div style='color: red; margin-top:15px;font-size: 16px;'>" . $responseDecode['message'] . "</div>";
        } else {
            echo "<div style='color: green; margin-top:15px;font-size: 16px;'>" . $responseDecode['message'] . "</div>";
        }
        ?>

        <form action="" method="post" onsubmit="return validateForm()">
            <div class="form first">
                <div class="details personal">
                    <span class="title">Supplier Personal Details</span>

                    <div class="fields">
                        <div class="input-field">
                            <label>Full Name</label>
                            <input type="text" name="full_name" placeholder="Enter your name"
                                value="<?php echo isset($_POST['full_name']) ? $_POST['full_name'] : '' ?>">
                        </div>

                        <div class="input-field">
                            <label>Email</label>
                            <input type="text" name="email" placeholder="Enter your email"
                                value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
                        </div>

                        <div class="input-field">
                            <label>Mobile Number</label>
                            <input type="text" name="phone_number" placeholder="Enter your number"
                                value="<?php echo isset($_POST['phone_number']) ? $_POST['phone_number'] : '' ?>">
                        </div>

                        <div class="input-field">
                            <label>Password</label>
                            <input type="password" name="password" id="password" placeholder="Enter password">
                        </div>

                        <div class="input-field">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password"
                                placeholder="Confirm password">
                        </div>
                    </div>
                </div>

                <div class="details ID">
                    <span class="title">Store Details</span>

                    <div class="fields">
                        <div class="input-field">
                            <label>Store Name</label>
                            <input type="text" name="company_name" placeholder="Enter your company name"
                                value="<?php echo isset($_POST['company_name']) ? $_POST['company_name'] : '' ?>">
                        </div>
                    </div>
                    <button type="submit">
                        <span class="btnText">Register</span>
                    </button>
                </div>
            </div>
        </form>

        <script>
            function validateForm() {
                var password = document.getElementById("password").value;
                var confirmPassword = document.getElementById("confirm_password").value;

                if (password.length < 6) {
                    alert("Password must be at least 6 characters long.");
                    return false;
                }

                if (password !== confirmPassword) {
                    alert("Passwords do not match.");
                    return false;
                }

                // If all validations pass, return true to submit the form
                return true;
            }
        </script>
    </div>
</body>

</html>