<?php
// require_once ('../config/DatabaseConnection.php');
require_once (realpath(dirname(__FILE__) . '/../config/DatabaseConnection.php'));

// require_once ('../password_attempt/PasswordAttempt.php');
require_once (realpath(dirname(__FILE__) . '/../password_attempt/PasswordAttempt.php'));

class AdminManager
{
    private $conn;
    private $passwordAttempt;
    // Constructor to establish database connection

    public function __construct()
    {
        // Create a new instance of the DatabaseConnection class to establish a database connection
        $databaseConnection = new DatabaseConnection();
        $this->conn = $databaseConnection->conn;
        $this->passwordAttempt = new PasswordAttempt();

        // Check if the connection is still open
        if ($this->conn->connect_errno) {
            die('Connection failed: ' . $this->conn->connect_error);
        }
    }

    // Method to add admin user

    public function createAdmin($password, $email, $full_name)
    {
        $response = array();
        // Initialize an empty array for the response

        // Validate all fields are not empty
        if (empty($password) || empty($email) || empty($full_name)) {
            $response['success'] = false;
            $response['message'] = 'All fields are required';
        } else {
            // Check if the email already exists in the database
            $sql_check_email = "SELECT * FROM admins WHERE email = '$email'";
            $result_check_email = $this->conn->query($sql_check_email);

            if ($result_check_email->num_rows > 0) {
                // if email already exists
                $response['success'] = false;
                $response['error'] = "Failed";
                $response['message'] = 'Email already exists';
            } else {
                // Email is unique, proceed with inserting into the database
                // $password = md5( $password );

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                // for hashing

                $sql_insert_admin = "INSERT INTO admins (password, email, full_name, role) VALUES ('$hashedPassword', '$email', '$full_name', 'admin')";
                $result_insert_admin = $this->conn->query($sql_insert_admin);
                if ($result_insert_admin) {
                    $response['success'] = true;
                    $response['message'] = 'Data stored successfully';
                    Header('Location:index.php');
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Something went wrong';
                }
            }
        }
        // Return the response in JSON format
        return json_encode($response);
    }

    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            $response['success'] = false;
            $response['error'] = 'Failed';
            $response['message'] = 'All fields are required';
            return json_encode($response);
        }
        try {

            $admin_login = "SELECT * FROM admins WHERE email='$email'";
            $result_login = $this->conn->query($admin_login);

            if (mysqli_num_rows($result_login) === 1) {
                $userDetails = mysqli_fetch_assoc($result_login);
                $hashedPasswordFromDatabase = $userDetails['password'];

                if (password_verify($password, $hashedPasswordFromDatabase)) {
                    // session_start();
                    if ($userDetails['admin_id']) {
                        $_SESSION['admin_id'] = $userDetails['admin_id'];
                        $_SESSION['full_name'] = $userDetails['full_name'];
                        $this->passwordAttempt->reset_Login_Attempts($email);

                        $response['success'] = true;
                        $response['error'] = 'Success';
                        $response['message'] = 'Logged in successfully';

                        // $response[ 'details' ] = array( $userDetails );
                        $this->passwordAttempt->deleteAttempt($email);
                        header('Location:adminDashboard.php');
                        exit();

                    }

                } else {
                    $response['success'] = false;
                    $response['error'] = 'Failed';
                    $response['message'] = 'Email or Password not matched';
                }
            } else {
                $response['success'] = false;
                $response['error'] = 'Failed';
                $response['message'] = 'User not found';

            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['error'] = 'Exception Failed';
            $response['message'] = 'Login Failed ' . $e->getMessage();
        }
        return json_encode($response);

    }

    public function sellerList()
    {
        $response = array();
        $sellerList_sql = 'SELECT  seller_id, full_name,email FROM sellers ';
        $result = $this->conn->query($sellerList_sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }
            $response['success'] = true;
            $response['message'] = "Data found";

        }

        return json_encode($response);
    }

    public function buyerList()
    {
        $response = array();
        $sellerList_sql = 'SELECT  buyer_id, full_name,email FROM buyers ';
        $result = $this->conn->query($sellerList_sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        }
        return json_encode($response);
    }

    public function isBuyerById($buyer_id)
    {
        $sql = 'SELECT * FROM buyers WHERE buyer_id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $buyer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $finalResult = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    // can use buyer and sellers for delete

    function deleteUserById($user_id)
    {
        $response = array();
        if (empty($user_id)) {
            $response = [
                'success' => true,
                'message' => 'Item not selected'
            ];
            return json_encode($response);
        }

        $is_buyer = $this->isBuyerById($user_id);
        $tableName = $is_buyer ? 'buyers' : 'sellers';

        $id = '';
        if ($is_buyer == true) {
            $id = 'buyer_id';
        } else {
            $id = 'seller_id';
        }
        // exit();
        $query = "DELETE FROM $tableName WHERE $id = '$user_id'";
        $result = $this->conn->query($query);
        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Successfully deleted'
            ];
        } else {
            $response = [
                'success' => true,
                'message' => 'Can not deleted user'
            ];
        }
        return json_encode($response);
    }

    // can use buyer and sellers delete

    public function searchBuyersName($name)
    {
        $response = array();

        if (empty($name)) {
            return json_encode($response);
        }

        // Escaping the input to prevent SQL injection
        $escapedName = $this->conn->real_escape_string($name);
        $query = "SELECT * FROM buyers WHERE full_name LIKE '%$escapedName%'";

        $result = $this->conn->query($query);

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $response[] = $row;
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'No user found'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Query execution failed'
            ];
        }

        return json_encode($response);
    }

    public function searchSellersName($name)
    {
        $response = array();

        if (empty($name)) {
            return json_encode($response);
        }

        // Escaping the input to prevent SQL injection
        $escapedName = $this->conn->real_escape_string($name);
        $query = "SELECT * FROM sellers WHERE full_name LIKE '%$escapedName%'";

        $result = $this->conn->query($query);

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $response['data'][] = $row;
                }
                $response['success'] = true;
                $response['message'] = "Data found";

            } else {
                $response = [
                    'success' => false,
                    'message' => 'No user found'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Query execution failed'
            ];
        }

        return json_encode($response);
    }

    public function deleteMultipleBuyers($user_ids)
    {

        $response = array();
        if (empty($user_ids)) {
            $response = [
                'success' => false,
                'message' => 'Item not selected'
            ];
            return json_encode($response);
        }

        $sql = 'DELETE FROM buyers WHERE buyer_id IN (' . implode(',', $user_ids) . ')';
        $result = $this->conn->query($sql);

        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Successfully deleted'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Something went wrong'
            ];
        }
        return json_encode($response);
    }

    public function deleteMultipleSellers($user_ids)
    {
        $response = array();
        if (empty($user_ids)) {
            $response = [
                'success' => false,
                'message' => 'Item not selected'
            ];
            return json_encode($response);
        }
        $sql = 'DELETE FROM sellers WHERE seller_id IN (' . implode(',', $user_ids) . ')';
        $result = $this->conn->query($sql);

        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Successfully deleted'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Something went wrong'
            ];
        }
        return json_encode($response);
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location:index.php');
        exit();
    }
}

?>