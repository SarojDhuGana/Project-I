<?php
require_once (__DIR__ . '/../password_attempt/PasswordAttempt.php');
require_once (__DIR__ . '/../config/DatabaseConnection.php');
require_once (__DIR__ . '/../OTP/Mail.php');
require_once (__DIR__ . '/../BuyerManager.php');


error_reporting(E_ERROR); // unnecessary error hide

class SellerManager
{
    private $conn;
    private $passwordAttempt;
    private $mailInstance;

    public function __construct()
    {
        $databaseConnection = new DatabaseConnection();
        $this->conn = $databaseConnection->getConnection();
        $this->passwordAttempt = new PasswordAttempt();

        $this->mailInstance = new Mail();

        if ($this->conn->connect_errno) {
            die("Connection failed: " . $this->conn->connect_error);
        }

    }

    // to handle if email either is created by seller
    public function checkRegisteredSeller($email, $phone_number)
    {
        if (empty($email)) {
            $response['success'] = false;
            $response['error'] = 'Failed';
            $response['message'] = "All fields are required";
        } else {

            $sql_check_email = "SELECT * FROM sellers WHERE email = '$email' OR phone_number='$phone_number'";
            $result_check_email = $this->conn->query($sql_check_email);

            if ($result_check_email->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function createSeller($sellerDetails)
    {
        $response = array();
    
        // Check if required fields are empty
        if (empty($sellerDetails['full_name']) || empty($sellerDetails['email']) || empty($sellerDetails['password']) || empty($sellerDetails['company_name'])) {
            $response['success'] = false;
            $response['error'] = 'Account Creation Failed';
            $response['message'] = "All fields are required";
            return json_encode($response);
        }
    
        // Check if the email already exists
        $sql_check_email = "SELECT * FROM sellers WHERE email = ?";
        $stmt_check_email = $this->conn->prepare($sql_check_email);
        $stmt_check_email->bind_param('s', $sellerDetails['email']);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();
    
        // Check if the phone number already exists
        $sql_check_phone = "SELECT * FROM sellers WHERE phone_number = ?";
        $stmt_check_phone = $this->conn->prepare($sql_check_phone);
        $stmt_check_phone->bind_param('s', $sellerDetails['phone_number']);
        $stmt_check_phone->execute();
        $result_check_phone = $stmt_check_phone->get_result();
    
        $emailOrPhone = $sellerDetails['email'];
        $buyerInstance = new BuyerManager();
        $checkBuyer = $buyerInstance->checkRegisteredBuyers($emailOrPhone);
    
        if ($result_check_email->num_rows > 0) {
            // Email already exists, return error response
            $response = [
                'success' => false,
                'error' => 'Account Creation Failed',
                'message' => 'Email already taken'
            ];
        } elseif ($result_check_phone->num_rows > 0) {
            // Phone number already exists, return error response
            $response = [
                'success' => false,
                'error' => 'Account Creation Failed',
                'message' => 'Phone number already taken'
            ];
        } elseif ($checkBuyer == true) {
            $response = [
                'success' => false,
                'error' => 'Account Creation Failed',
                'message' => 'Email or number already taken'
            ];
        } else {
            // Email and phone number are unique, proceed with inserting into the database
            // Hash the password using password_hash() with PASSWORD_DEFAULT algorithm
            $hashedPassword = password_hash($sellerDetails['password'], PASSWORD_DEFAULT);
    
            // Prepare the SQL statement with placeholders
            $sql_insert_seller = "INSERT INTO sellers (full_name, email, password, phone_number, company_name, company_address) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert_seller = $this->conn->prepare($sql_insert_seller);
    
            // Convert empty strings to NULL
            $phone_number = $sellerDetails['phone_number'] === '' ? NULL : $sellerDetails['phone_number'];
            $company_address = isset($sellerDetails['company_address']) ? $sellerDetails['company_address'] : NULL;
    
            // Bind the parameters
            $stmt_insert_seller->bind_param('ssssss', $sellerDetails['full_name'], $sellerDetails['email'], $hashedPassword, $phone_number, $sellerDetails['company_name'], $company_address);
    
            // Execute the query
            if ($stmt_insert_seller->execute()) {
                $response = [
                    'success' => true,
                    'message' => "Account created successfully"
                ];
                header('Location:../allUserLogin.php');
                exit();
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Account Creation Failed',
                    'message' => "Something went wrong while creating account, try again later"
                ];
            }
        }
    
        return json_encode($response);
    }
    

    public function sellerLogin($email, $password)
    {
        $response = array();
        if (empty($email) || empty($password)) {
            $response['success'] = false;
            $response['message'] = "All fields are required";
            return json_encode($response);
        }
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql_seller_login = "SELECT email,phone_number FROM sellers where email='$email' or phone_number='$email' ";
        $seller_login_result = $this->conn->query($sql_seller_login);

        if (!$seller_login_result) {
            $response['success'] = false;
            $response['message'] = "SQL query execution error: " . $this->conn->error;
        }

        $attempt = $this->passwordAttempt->getRemainingLoginAttempts($email);

        if ($attempt < 1) {
            $response = [
                'success' => false,
                'attempt_left' => $attempt,
                'next attempt' => "Try after 1 min",
                'message' => "Maximum login attempts reached. Please try again later."
            ];

            return json_encode($response);
        }

        if (mysqli_num_rows($seller_login_result) == 1) {
            $sellerDetails = mysqli_fetch_assoc($seller_login_result);
            $hashedPasswordFromDatabase = $sellerDetails['password'];

            if ($sellerDetails['is_verified'] == 1 && password_verify($password, $hashedPasswordFromDatabase)) {
                session_start();
                $_SESSION['seller_id'] = $sellerDetails['seller_id'];
                // $seller_id = $sellerDetails['seller_id'];
                $_SESSION['full_name'] = $sellerDetails['full_name'];
                // header('Location:dashboard.php?seller_id=' . urlencode($seller_id)); // redirect page
                // Reset login attempts for the email address upon successful login
                $this->passwordAttempt->reset_Login_Attempts($email);
                $response['success'] = true;
                $response['message'] = "Logged in successfully";
                $this->passwordAttempt->deleteAttempt($email);
                header('Location:dashboard.php');
                exit();
            } else {
                // send otp to user , if user is not valid
                // if ($otpResult = $this->mailInstance->sendOTP($email)) {
                //     // header('Location:conritmOtp.php'); // redirect user to otp input field form
                // }
                $response['success'] = false;
                $response['message'] = "User is not verified, confirm your email first";
                // $response['result'] = json_decode($otpResult);

            }

        } else {
            // Increment login attempts for the email address
            $this->passwordAttempt->increment_Login_Attempts($email);
            // Get the updated remaining attempts after the increment
            $attempt = $this->passwordAttempt->getRemainingLoginAttempts($email);
            $response['success'] = false;
            $response['attempt_left'] = $attempt; // Extract 'attempts' from the $attempt array
            $response['message'] = "Email and password not match.";
        }

        return json_encode($response);

    }
    public function is_seller($emailOrPhone)
    {
        $response = array();

        try {
            // Use prepared statement to prevent SQL injection
            $checkSeller = "SELECT email, phone_number FROM sellers WHERE email=? OR phone_number=?";
            $stmt = $this->conn->prepare($checkSeller);
            $stmt->bind_param("ss", $emailOrPhone, $emailOrPhone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $response = [
                    'success' => true,
                    'error' => 'Success',
                    'message' => 'User found'
                ];
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'User not found'
                ];
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => 'Exception',
                'message' => 'Error occurred: ' . $e->getMessage()
            ];
        }

        return json_encode($response);
    }

    public function sellerDetails($seller_id)
    {
        $response = array();

        $view_sql = "SELECT full_name, email,phone_number,is_verified, company_name,company_address FROM sellers where seller_id='$seller_id'";
        $result = $this->conn->query($view_sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        }
        // $response = mysqli_fetch_assoc($result);
        return json_encode($response);

    }


    public function updateSellerName($seller_id, $full_name)
    {
        $response = array();

        try {
            // Use prepared statement to update the full_name
            $updateSeller = "UPDATE sellers SET full_name=? WHERE seller_id=?";
            $stmt = $this->conn->prepare($updateSeller);
            $stmt->bind_param("si", $full_name, $seller_id);

            if ($stmt->execute()) {
                $response = [
                    'success' => true,
                    'message' => 'Full name updated successfully'
                ];
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Failed to update full name'
                ];
            }

            $stmt->close();
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => 'Exception',
                'message' => 'Error occurred: ' . $e->getMessage()
            ];
        }

        return json_encode($response);
    }

    // ********** function to update seller email
    public function updateSellerEmail($seller_id, $email)
    {
        $response = array();
        $buyerManager = new BuyerManager();
        $is_buyer = $buyerManager->is_buyer($email);
        $buyerDecode = json_decode($is_buyer, true);
        if ($buyerDecode['success'] == true) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Email already taken'
            ];
        } else {
            $is_seller = $this->is_seller($email);
            $sellerDecode = json_decode($is_seller, true);
            if ($sellerDecode['success'] == true) {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Email already taken'
                ];
            } else {
                try {
                    $updateBuyer = "UPDATE sellers SET email=? WHERE seller_id=?";
                    $stmt = $this->conn->prepare($updateBuyer);
                    $stmt->bind_param("si", $email, $seller_id);

                    if ($stmt->execute()) {
                        $response = [
                            'success' => true,
                            'error' => 'Success',
                            'message' => 'Email updated successfully'
                        ];
                    } else {
                        $response = [
                            'success' => false,
                            'error' => 'Failed',
                            'message' => 'Failed to update email'
                        ];
                    }

                    $stmt->close();
                } catch (Exception $e) {
                    $response = [
                        'success' => false,
                        'error' => 'Exception',
                        'message' => 'Error occurred: ' . $e->getMessage()
                    ];
                }
            }
        }
        return json_encode($response);
    }



    // ******** for update phone number
    public function updateSellerPhone($seller_id, $phone_number)
    {
        $buyerManager = new BuyerManager();
        $is_buyer = $buyerManager->is_buyer($phone_number);
        $buyerDecode = json_decode($is_buyer, true);
        if ($buyerDecode['success'] == true) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Phone number already taken'
            ];
        } else {
            $is_seller = $this->is_seller($phone_number);
            $sellerDecode = json_decode($is_seller, true);
            if ($sellerDecode['success'] == true) {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Phone number already taken'
                ];
            } else {
                try {
                    $updateBuyer = "UPDATE sellers SET phone_number=? WHERE seller_id=?";
                    $stmt = $this->conn->prepare($updateBuyer);
                    $stmt->bind_param("si", $phone_number, $seller_id);

                    if ($stmt->execute()) {
                        $response = [
                            'success' => true,
                            'error' => 'Success',
                            'message' => 'Phone number updated successfully'
                        ];
                    } else {
                        $response = [
                            'success' => false,
                            'error' => 'Failed',
                            'message' => 'Failed to update phone number'
                        ];
                    }

                    $stmt->close();
                } catch (Exception $e) {
                    $response = [
                        'success' => false,
                        'error' => 'Exception',
                        'message' => 'Error occurred: ' . $e->getMessage()
                    ];
                }
            }
        }
        return json_encode($response);
    }

    public function viewAllProducts($seller_id)
    {

        $response = array();
        if (empty($seller_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Product not selected'
            ];
            return json_encode($response);
        }
        try {

            $sql = "SELECT * FROM products WHERE seller_id='$seller_id'";
            $result = $this->conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $response['data'][] = $row;
                }
                $response['success'] = true;
                $response['message'] = 'Proucts not found';
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Product not found '
                ];
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ];
        }
        return json_encode($response);
    }





    public function logout()
    {
        $_SESSION = array();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }
}

?>