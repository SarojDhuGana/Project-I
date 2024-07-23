<?php
require_once ('PasswordManager.php');

if (!empty($_POST)) {
    $newPassword = $_POST['new_password'];
    $re_enter_new_password = $_POST['re_enter_new_password'];
    $email = isset($_GET['email']) ? urldecode($_GET['email']) : '';
    $passwordManager = new PasswordManager();
    $response = $passwordManager->changePassword($email, $newPassword, $re_enter_new_password);
    echo $response;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> All user Login</title>
    <!-- <link rel="stylesheet" href="../style/commonStyling.css"> -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            column-gap: 30px;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;

        }

        .form {
            position: absolute;
            max-width: 430px;
            width: 100%;
            padding: 30px;
            border-radius: 6px;
            background-color: #fff;
            /* Change background color to white */
            box-shadow: 0 0px 7px rgba(0, 0, 0, 0.4);
            /* Add shadow */
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

        .media-options a {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media screen and (max-width: 400px) {
            .form {
                padding: 20px 10px;
            }

        }
    </style>
</head>

<body>

    <section class="container forms">
        <div class="form login">
            <div class="form-content">
                <header>Change passoword</header>
                <form action="" method="post">
                    <div class="field input-field">
                        <input type="password" name="new_password" placeholder="Re-enter new password" class="password">
                        <i class='bx bx-hide eye-icon'></i>
                    </div>
                    <div class="field input-field">
                        <input type="password" name="re_enter_new_password" placeholder="Re-enter new password"
                            class="password">
                        <i class='bx bx-hide eye-icon'></i>
                    </div>

                    <div class="field button-field">
                        <button>Continue</button>
                    </div>
                </form>

            </div>
        </div>
    </section>
    <script>
        const eyeIcons = document.querySelectorAll(".eye-icon");
        eyeIcons.forEach(eyeIcon => {
            eyeIcon.addEventListener("click", () => {
                const passwordField = eyeIcon.parentElement.querySelector(".password");

                // Toggle password visibility
                if (passwordField.type === "password") {
                    passwordField.type = "text"; // Show password
                    eyeIcon.classList.replace("bx-hide", "bx-show");
                } else {
                    passwordField.type = "password"; // Hide password
                    eyeIcon.classList.replace("bx-show", "bx-hide"); // Change eye icon
                }
            });
        });
    </script>

</body>

</html>