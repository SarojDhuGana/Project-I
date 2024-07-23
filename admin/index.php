<?php
require_once (__DIR__ . '/AdminManager.php');

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $adminManager = new AdminManager();

    // print_r($_POST);
    $result = $adminManager->login($email, $password);
    $responseDecode = json_decode($result, true);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../allUserLogin.css">
    <script src="jsFunctions/hamburger.js"></script>
</head>

<body>
    <section class="container forms">
        <div class="form login">
            <div class="form-content">
                <header>Admin Login</header>
                <!-- for displaying result -->
                <?php require_once ('../successMessage.php') ?>

                <form action="" method="post">
                    <div class="field input-field">
                        <input type="email" name="email"
                            value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>" placeholder="Email"
                            class="input">
                    </div>
                    <div class="field input-field">
                        <input type="password" name="password" placeholder="Password" class="password">
                        <i class='bx bx-hide eye-icon'></i>
                    </div>

                    <div class="form-link">
                        <a href="OTP/sendMail.php" class="forgot-pass">Forgot password?</a>
                    </div>

                    <div class="field button-field">
                        <button>Login</button>
                    </div>

                    <div class="form-link">
                        <span>Don't have an account? <a href="adminRegister.php"
                                class="link signup-link">Signup</a></span>
                    </div>

                </form>
            </div>

        </div>
    </section>
    <!-- <script src="accountregister.js"></script> -->
    <script>
        <?php if (isset($responseDecode['verified']) && $responseDecode['verified'] == false) { ?>
            // Display a confirmation prompt if the user is not verified
            if (confirm("User not verified. Please confirm your email.")) {
                window.location.href = 'otp/sendMail.php'; // Redirect to the OTP sending page
            } else {
                // User clicked cancel, do nothing
            }
        <?php } ?>

        pwShowHide = document.querySelectorAll(".eye-icon"),
            pwShowHide.forEach(eyeIcon => {
                eyeIcon.addEventListener("click", () => {
                    let pwFields = eyeIcon.parentElement.parentElement.querySelectorAll(".password");

                    pwFields.forEach(password => {
                        if (password.type === "password") {
                            password.type = "text";
                            eyeIcon.classList.replace("bx-hide", "bx-show");
                            return;
                        }
                        password.type = "password";
                        eyeIcon.classList.replace("bx-show", "bx-hide");
                    })

                })
            });

    </script>
</body>

</html>