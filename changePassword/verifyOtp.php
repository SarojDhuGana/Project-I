<?php
require_once(__DIR__ . '/../otp/Mail.php');
$mailInstance = new Mail();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $otp = $_POST['otp'];
    $response = $mailInstance->confirmRestPasswordOTP($email, $otp);
    echo $response;

} else {

    http_response_code(405); // Method Not Allowed
    echo json_encode(array("success" => false, "message" => "Method not allowed."));
}


?>