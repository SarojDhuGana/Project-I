<?php
require_once (__DIR__ . '/../config/DatabaseConnection.php');
require_once (__DIR__ . '/../OTP/Mail.php');
class PasswordManager extends DatabaseConnection
{
    private $mailInstance;

    public function __construct()
    {
        parent::__construct();

        $this->mailInstance = new Mail();
    }
    public function changePassword($emailOrPhone, $newPassword, $re_enter_new_password)
    {
        echo $emailOrPhone;
        $response = array();
        if (empty($newPassword) || empty($re_enter_new_password)) {
            $response = [
                'success' => false,
                'message' => 'Both fields are required'
            ];
        } else if ($newPassword != $re_enter_new_password) {

            $response = [
                'success' => false,
                'message' => 'Both password must be same'
            ];
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $is_buyer = $this->mailInstance->isBuyer($emailOrPhone);
        $tableName = $is_buyer ? 'buyers' : 'sellers';

        $updatePassword = "UPDATE $tableName SET password = ? WHERE email = ?";
        $stmt = $this->conn->prepare($updatePassword);
        $stmt->bind_param("ss", $hashedPassword, $emailOrPhone);
        // Execute the statement
        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Password updated successfully.'
            ];
            header('Location:../allUserLogin.php');
        } else {
            $response = [
                'success' => false,
                'message' => 'Error updating password: ' . $stmt->error
            ];
        }
        // Close the statement
        $stmt->close();

        return json_encode($response);
    }



}

?>