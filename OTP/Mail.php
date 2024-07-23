<?php
require_once (__DIR__ . '/../config/DatabaseConnection.php');


error_reporting(E_ERROR); //error hide
class Mail
{
    private $conn;
    private $buyerManager;


    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance()->getConnection();
        // $this->buyerManager = new BuyerManager();
    }

    public function isBuyer($emailOrPhone)
    {
        $sql = "SELECT * FROM buyers WHERE email = ? OR phone_number = ?  OR buyer_id='$emailOrPhone'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $emailOrPhone, $emailOrPhone);
        $stmt->execute();
        $result = $stmt->get_result();
        $finalResult = mysqli_fetch_assoc($result);
        // $isBuyer = $finalResult['is_buyer'];
        // if ($result->num_rows  && $finalResult['is_buyer'] == 1 > 0) { // if did this then is_buyer column's value must be 1
        if ($result->num_rows > 0) {
            return true; // User exists in the buyers table
        } else {
            return false; // User does not exist in the buyers table
        }

    }

    public function generateOtp()
    {
        $otp = rand(100000, 999999);
        return $otp;

    }

    public function removeDBOTP($email)
    {
        $delete_otp_sql = "DELETE FROM email_verificationS WHERE user_email= '$email'";

        $delete_otp_sql_result = $this->conn->query($delete_otp_sql);

        // $delete_otp_sql = "DELETE FROM email_verificationS WHERE user_email= '$email' AND  expiration_time < NOW()";
        // $this->conn->close();

    }

    public function sendOTP($email)
    {
        $response = array();
        if (empty($email)) {
            $response = [
                'success' => false,
                'message' => 'Field is required'
            ];
            return json_encode($response);
        }
        $to = $email;
        $from = "dan.mahara.0909@gmail.com";
        $subject = "Verify otp";
        // function called for otp
        $otp = $this->generateOtp();
        $message = "Your otp is: " . $otp;
        $headers = "From: $from";

        $isBuyer = $this->isBuyer($email);
        $tableName = $isBuyer ? 'buyers' : 'sellers';

        $sql = "SELECT * FROM $tableName where email='$email'";
        $result = $this->conn->query($sql);
        if ($result) {

            $otp_sql = "INSERT INTO email_verifications(otp_code,user_email) VALUES('$otp','$email') ";
            $otp_sql_result = $this->conn->query($otp_sql);

            if ($otp_sql_result && mail($to, $subject, $message, $headers)) {
                $response = [
                    'success' => true,
                    'message' => 'Otp has sent'
                ];
                header('Location: verifyOTP.php?email=' . urlencode($email));
                exit(); // Ensure script execution stops after redirection

            } else {
                $response = [
                    'success' => false,
                    'message' => 'Something went wrong, try again later'
                ];
                $this->removeDBOTP($email);
            }

        } else {
            $response = [
                'success' => false,
                'message' => "User does not exist"
            ];
        }

        $this->conn->close();

        return json_encode($response);

    }


    // Function to check if the user is already verified
    public function isUserVerified($tableName, $emailOrPhone)
    {
        $sql = "SELECT is_verified FROM $tableName WHERE email = ? OR phone_number = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $emailOrPhone, $emailOrPhone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $isVerified = $row['is_verified'];
            return $isVerified;

        }
        return false;
    }



    public function verifyRegisterOtp($emailOrPhone, $otp)
    {
        $response = [];
        if (empty($otp)) {
            $response = [
                'success' => false,
                'message' => 'OTP is required'
            ];
            return json_encode($response);
        }
        date_default_timezone_set('Asia/Kathmandu'); // Set the default timezone
        // Determine whether the user is a buyer or a seller
        $isBuyer = $this->isBuyer($emailOrPhone);
        $tableName = $isBuyer ? "buyers" : "sellers";

        // Query to get the last OTP sent
        $sql = "SELECT otp_code, created_at FROM email_verifications WHERE user_email = ? ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $emailOrPhone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $storedOtp = $row['otp_code'];
            $otpGeneratedTime = strtotime($row['created_at']);
            $currentTime = time();
            $timeDifference = $currentTime - $otpGeneratedTime;
            // echo $timeDifference;
            $remainingTime = 60 - $timeDifference;
            // Check if the OTP is correct and not expired (within 1 minute)
            if ($otp == $storedOtp && $timeDifference <= 60) {

                // Redirect to the change password page if the user is already verified
                $isUserVerified = $this->isUserVerified($tableName, $emailOrPhone);
                if ($isUserVerified) {
                    // header("Location: ../changePassword/newPassword.php");
                    header('Location: ../changePassword/newPassword.php?email=' . urlencode($emailOrPhone));
                    exit();
                }
                $updateSql = "UPDATE $tableName SET is_verified = 1 WHERE (email = ? OR phone_number = ?)";
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bind_param("ss", $emailOrPhone, $emailOrPhone);
                if ($updateStmt->execute()) {
                    // OTP verified successfully
                    $response = [
                        'success' => true,
                        'message' => 'OTP matched'
                    ];
                    header('Location:../allUserLogin.php');
                    $this->removeDBOTP($emailOrPhone); // Delete OTP from database after successful verification
                }
            } else if ($timeDifference > 60) {
                // OTP is either incorrect or expired
                $response = [
                    'success' => false,
                    'message' => 'OTP is expired',
                ];
                $this->removeDBOTP($emailOrPhone); // Remove expired OTP from database
            } else {
                $response = [
                    'success' => false,
                    'message' => 'OTP is incorrect',
                    'resend after' => $timeDifference
                ];

            }
        } else {
            // No OTP found for this email
            $response = [
                'success' => false,
                'message' => 'No OTP found for this email'
            ];
            $this->removeDBOTP($emailOrPhone); // Remove expired OTP from database

        }

        $this->conn->close();
        return json_encode($response);
    }

    public function forgetPassword($emailOrPhone)
    {
        date_default_timezone_set('Asia/Kathmandu'); // necessary
        $response = array();
        // Determine whether the user is a buyer or a seller
        $isBuyer = $this->isBuyer($emailOrPhone);

        $tableName = $isBuyer ? "buyers" : "sellers";

        $sql = "SELECT email FROM $tableName WHERE email='$emailOrPhone'";
        $result = mysqli_query($this->conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $result = $this->sendOTP($emailOrPhone);
            $response = [
                'succcess' => true,
                'message' => json_decode($result)
            ];
        } else {
            $response = [
                'succcess' => false,
                'message' => "User not found",
            ];
        }

        return json_encode($response);

    }


    public function confirmRestPasswordOTP($emailOrPhone, $otp)
    {
        $response = array();
        if (empty($otp)) {
            $response = [
                'success' => false,
                'message' => 'OTP is required'
            ];
            return json_encode($response);
        }

        $sql = "SELECT user_email, otp_code, created_at FROM email_verifications WHERE user_email = ? ORDER BY id DESC LIMIT 1"; // Query to get the last OTP sent

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $emailOrPhone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $storedOtp = $row['otp_code'];
            $otpGeneratedTime = strtotime($row['created_at']);
            $currentTime = time();
            $timeDifference = $currentTime - $otpGeneratedTime;

            // Check if the OTP is correct and not expired (within 1 minute)
            if ($otp == $storedOtp && $timeDifference <= 60) {
                $response = [
                    'success' => true,
                    'message' => 'OTP matched'
                ];
                $this->removeDBOTP($emailOrPhone); // Delete OTP from database after successful verification
            } else {
                $response = [
                    'success' => false,
                    'message' => 'OTP is either incorrect or expired'
                ];
                $this->removeDBOTP($emailOrPhone); // Remove expired OTP from database
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'No OTP found for this email'
            ];
        }

        return json_encode($response);
    }

    // function should need anytime
    public function time($email)
    {
        date_default_timezone_set('Asia/Kathmandu'); // necessary

        $otp_sql = "SELECT created_at FROM  email_verifications WHERE user_email='$email'";
        $otp_sql_result = $this->conn->query($otp_sql);

        if ($otp_sql_result) {
            $otp_details = mysqli_fetch_assoc($otp_sql_result);
            $created_at = strtotime($otp_details['created_at']); // Convert to Unix timestamp

            $current_time = time();
            $time_difference = $current_time - $created_at;

        }
        // Convert time difference to hours, minutes, and seconds
        $hours = floor($time_difference / 3600); // 1 hour = 3600 seconds
        $minutes = floor(($time_difference % 3600) / 60); // 1 minute = 60 seconds
        $seconds = $time_difference % 60;

        // Print the time difference
        echo "Time difference: " . $hours . " hours, " . $minutes . " minutes, " . $seconds . " seconds";

        // return json_encode($response);
    }


}

?>