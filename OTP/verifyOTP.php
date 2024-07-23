<?php
require_once ('Mail.php');

$mailInstance = new Mail();

if (!empty($_POST['otp'])) {
    // $email = $_POST['email'];
    $email = isset($_GET['email']) ? urldecode($_GET['email']) : '';
    // echo $email;
    $otp = $_POST['otp'];
    echo $otpDigits;

    // Concatenate OTP digits to form OTP
    $otp = implode('', $otp);
    $result = $mailInstance->verifyRegisterOtp($email, $otp);
    echo $result;
    // // Check if the OTP is incorrect and there's a remaining time to resend OTP
    // $responseDecode = json_decode($result, true);
    // if (isset($responseDecode['success']) && $responseDecode['success'] === false && isset($responseDecode['resend_after'])) {
    //     $timeDifference = $responseDecode['resend_after'];
    // }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Submit Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .otp-form {
            background-color: #fff;
            box-shadow: 0px 0px 8px 0px #02025044;
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            padding: 20px;
            text-align: center;
        }

        .otp-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .otp-input-fields {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .otp-input-fields input {
            width: 40px;
            height: 40px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            text-align: center;
            font-size: 16px;
            outline: none;
        }

        .otp-input-fields input:focus {
            border-color: #4e73df;
        }

        .submit-button {
            background-color: #4e73df;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #3752a1;
        }

        .otp-result {
            font-size: 18px;
            margin-top: 10px;
        }

        .otp-result._ok {
            color: green;
        }

        .otp-result._notok {
            color: red;
        }
    </style>
</head>

<body>
    <form action="" class="otp-form" name="otp-form" method="post">
        <div class="otp-title">OTP Verification</div>
        <div class="otp-input-fields">
            <input type="number" class="otp__digit otp__field__1" name="otp[]" required>
            <input type="number" class="otp__digit otp__field__2" name="otp[]" required>
            <input type="number" class="otp__digit otp__field__3" name="otp[]" required>
            <input type="number" class="otp__digit otp__field__4" name="otp[]" required>
            <input type="number" class="otp__digit otp__field__5" name="otp[]" required>
            <input type="number" class="otp__digit otp__field__6" name="otp[]" required>
        </div>
        <button type="submit" class="submit-button">Submit OTP</button>
        <div id="otp-result" class="otp-result _notok"></div>
    </form>

    <script>
        var otp_inputs = document.querySelectorAll(".otp__digit")
        var mykey = "0123456789".split("")
        otp_inputs.forEach((_) => {
            _.addEventListener("keyup", handle_next_input)
        })

        function handle_next_input(event) {
            let current = event.target
            let index = parseInt(current.classList[1].split("__")[2])
            current.value = event.key

            if (event.keyCode == 8 && index > 1) {
                current.previousElementSibling.focus()
            }
            if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
                var next = current.nextElementSibling;
                next.focus()
            }
            var _finalKey = ""
            for (let { value } of otp_inputs) {
                _finalKey += value
            }
            if (_finalKey.length == 6) {
                document.querySelector("#otp-result").classList.replace("_notok", "_ok")
                document.querySelector("#otp-result").innerText = "OTP Entered Successfully!"
            } else {
                document.querySelector("#otp-result").classList.replace("_ok", "_notok")
                document.querySelector("#otp-result").innerText = ""
            }
        }
    </script>
</body>

</html>