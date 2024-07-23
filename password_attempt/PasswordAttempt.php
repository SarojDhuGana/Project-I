<?php
require_once(__DIR__ . '/../config/DatabaseConnection.php');
session_start();

class PasswordAttempt
{
    private $conn;

    public function __construct()
    {
        $this->conn = DatabaseConnection::getInstance()->getConnection();


        if ($this->conn->connect_errno) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function deleteAttempt($email)
    {
        $response = array();
        $sql = "DELETE FROM  login_attempts WHERE email='$email'";
        $result = mysqli_query($this->conn, $sql);

        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Attempt deleted'
            ];
        }
        return json_encode($response);

    }

    private function incrementLoginAttempts($email)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $loginAttempts = isset($_SESSION['login_attempts'][$email]) ? $_SESSION['login_attempts'][$email] : 0;
        $loginAttempts++;
        $_SESSION['login_attempts'][$email] = $loginAttempts;
        $sql = "INSERT INTO login_attempts (ip_address, email, attempts, created_at) VALUES ('$ipAddress', '$email', $loginAttempts, NOW()) ON DUPLICATE KEY UPDATE attempts = attempts + 1, created_at = NOW()";
        $this->conn->query($sql);

    }

    public function increment_Login_Attempts($email)
    {
        $this->incrementLoginAttempts($email);
    }

    private function resetLoginAttempts($email)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        unset($_SESSION['login_attempts'][$email]);

        $sql = "DELETE FROM login_attempts WHERE ip_address = '$ipAddress' AND email = '$email'";
        $this->conn->query($sql);
    }

    public function reset_Login_Attempts($email)
    {
        $this->resetLoginAttempts($email);
    }

    private function isMaxLoginAttemptsReached($email)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $maxAttempts = 3;

        $sql = "SELECT attempts FROM login_attempts WHERE ip_address = '$ipAddress' AND email = '$email'";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return $row['attempts'] >= $maxAttempts;
        }

        return false;
    }

    public function isMax_Login_Attempts_Reached($email)
    {
        return $this->isMaxLoginAttemptsReached($email);
    }
    public function getRemainingLoginAttempts($email)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $maxAttempts = 3;
        $timeThreshold = time() - 60; // 60 seconds threshold

        $sql = "SELECT attempts, created_at FROM login_attempts WHERE ip_address = '$ipAddress' AND email = '$email' AND created_at > FROM_UNIXTIME($timeThreshold)";

        $result = $this->conn->query($sql);

        $loginAttempts = $result->num_rows;
        $remainingAttempts = max(0, $maxAttempts - $loginAttempts);
        $response = array();

        if (!$result) {
            $response['success'] = false;
            $response['message'] = "No user found";
            return $response;
        }

        return $remainingAttempts;
    }


}
?>