<?php
require_once ('BuyerManager.php');

$buyerManager = new BuyerManager();

if (!empty($_POST)) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['city'];
    $phone_number = $_POST['phone_number'];
    $response = $buyerManager->registerBuyer($full_name, $email, $password, $address, $phone_number);
    $responseDecode = json_decode($response, true);

    if (!empty($responseDecode) && isset($responseDecode['success']) == true) {
        session_start();
        $_SESSION['success_response'] = $responseDecode['message'];
    } else {
        $_SESSION['error_response'] = $responseDecode['message'];
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- <link rel="stylesheet" href="index.css"> -->
    <link rel="stylesheet" href="bdashboard.css">
    <script src="jsFunctions/hamburger.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            height: 100vh;
            /* width: 100%; */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form {
            /* position: absolute; */
            max-width: 430px;
            width: 100%;
            padding: 30px;
            border-radius: 6px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);

        }

        .form.signup {
            opacity: 0;
            pointer-events: none;
        }

        .forms.show-signup .form.signup {
            opacity: 1;
            pointer-events: auto;
        }

        .forms.show-signup .form.login {
            opacity: 0;
            pointer-events: none;
        }

        header {
            font-size: 28px;
            font-weight: 600;
            color: #232836;
            text-align: center;
        }

        form {
            margin-top: 30px;
        }

        .form .field {
            position: relative;
            height: 50px;
            width: 100%;
            margin-top: 20px;
            border-radius: 6px;
        }

        .field input,
        .field button {
            height: 100%;
            width: 100%;
            border: none;
            font-size: 16px;
            font-weight: 400;
            border-radius: 6px;
        }

        .field input {
            outline: none;
            padding: 0 15px;
            border: 1px solid#CACACA;
        }

        .field input:focus {
            border-bottom-width: 2px;
        }

        .eye-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            font-size: 18px;
            color: #8b8b8b;
            cursor: pointer;
            padding: 5px;
        }

        .field button {
            color: white;
            background-color: #428a7d;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .field button:hover {
            background-color: orange;
        }

        .form-link {
            text-align: center;
            margin-top: 10px;
        }

        .form-link span,
        .form-link a {
            font-size: 14px;
            font-weight: 400;
            color: #232836;
        }

        .form a {
            color: #0171d3;
            text-decoration: none;
        }

        .form-content a:hover {
            text-decoration: underline;
        }

        .line {
            position: relative;
            height: 1px;
            width: 100%;
            margin: 36px 0;
            background-color: #d4d4d4;
        }

        .line::before {
            content: 'Or';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #FFF;
            color: #8b8b8b;
            padding: 0 15px;
        }

        .media-options a {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        a.facebook {
            color: #fff;
            background-color: #428a7d;
        }

        a.facebook .facebook-icon {
            height: 28px;
            width: 28px;
            color: #0171d3;
            font-size: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
        }

        .facebook-icon,
        img.google-img {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
        }

        img.google-img {
            height: 20px;
            width: 20px;
            object-fit: cover;
        }

        a.google {
            border: 1px solid #CACACA;
        }

        a.google span {
            font-weight: 500;
            opacity: 0.6;
            color: #232836;
        }

        @media screen and (max-width: 400px) {
            .form {
                padding: 20px 10px;
            }

        }

        /*  to hide search or cataegory  */
        #searchBox {
            display: none;
        }

        /* this value in indexNavigation.php file */
        #searchBox {
            display: none;
        }
    </style>
</head>

<body>
    <?php require_once ('indexNavigation.php'); ?>

    <section class="container forms">
        <div class="form login">

            <div class="form-content">
                <header>Signup</header>
                <form action="" method="post" onsubmit="return validateForm()">
                    <?php require_once ('successMessage.php') ?>
                    <div class="field input-field">
                        <input type="text" name="full_name" value="<?= $_POST['full_name']; ?>" placeholder="Full name"
                            class="input">
                    </div>
                    <div class="field input-field">
                        <input type="email" name="email" value="<?= $_POST['email']; ?>" placeholder="Email"
                            class="input">
                    </div>
                    <div class="field input-field">
                        <input type="text" name="phone_number" value="<?= $_POST['phone_number']; ?>"
                            placeholder="Phone " class="input">
                    </div>

                    <div class="field input-field">
                        <input type="text" name="city" value="<?= $_POST['city']; ?>" placeholder="city" class="city">

                    </div>
                    <div class="field input-field">
                        <input type="password" name="password" placeholder="Password" id="password" class="password">
                        <i class='bx bx-hide eye-icon'></i>
                    </div>

                    <div class="field input-field">
                        <input type="password" name="confirm_password" id="confirm_password"
                            placeholder=" Comfirm Password" class="password">
                        <i class='bx bx-hide eye-icon eye_icon2'></i>
                    </div>


                    <div class="field button-field">
                        <button>Signup</button>
                    </div>
                </form>

                <div class="form-link">
                    <span>Already have an account?<a href="allUserLogin.php" class="signup-link">Login</a></span>
                </div>
            </div>

        </div>

        </div>
    </section>
    <script>

        const eyeIcons = document.querySelectorAll(".eye-icon");

        // Loop through each eye icon
        eyeIcons.forEach(eyeIcon => {
            // Add click event listener to each eye icon
            eyeIcon.addEventListener("click", () => {
                // Find the adjacent password field 
                const passwordField = eyeIcon.parentElement.querySelector(".password");

                // Toggle password visibility
                if (passwordField.type === "password") {
                    passwordField.type = "text"; // Show password
                    eyeIcon.classList.replace("bx-hide", "bx-show"); // Change eye icon
                } else {
                    passwordField.type = "password"; // Hide password
                    eyeIcon.classList.replace("bx-show", "bx-hide"); // Change eye icon
                }
            });
        });

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
</body>

</html>