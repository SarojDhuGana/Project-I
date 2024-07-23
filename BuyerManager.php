<?php
require_once (__DIR__ . '/config/DatabaseConnection.php');
require_once (__DIR__ . '/password_attempt/PasswordAttempt.php');
require_once (__DIR__ . '/OTP/Mail.php');
require_once (__DIR__ . '/seller/SellerManager.php');

class BuyerManager
{
    private $conn;
    private $passwordAttempt;
    private $mailInstance;

    // Constructor to establish database connection
    public function __construct()
    {
        // Create a new instance of the DatabaseConnection class to establish a database connection
        $databaseConnection = new DatabaseConnection();

        $this->conn = $databaseConnection->getConnection();

        //for password Attempt
        $this->passwordAttempt = new PasswordAttempt();   // create a new instance of the PasswordAttempt class to limit user login attempt

        $this->mailInstance = new Mail(); // create a new instance of mail 

        if ($this->conn->connect_errno) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    public function __destruct()
    {
        $this->conn->close();
    }


    public function checkRegisteredBuyers($emailOrPhone)
    {
        if (empty($full_name) || empty($email) || empty($password)) {
            $response['success'] = false;
            $response['message'] = "All fields are required";
        } else {

            $sql_check_email = "SELECT * FROM buyers WHERE email = '$emailOrPhone' OR phone_number='$emailOrPhone'";
            $result_check_email = $this->conn->query($sql_check_email);

            if ($result_check_email->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    // Method to add buyer user
    public function registerBuyer($full_name, $email, $password, $address, $phone_number)
    {
        $response = array();
        if (empty($full_name) || empty($email) || empty($password)) {
            $response['success'] = false;
            $response['error'] = 'Account creation failed';
            $response['message'] = "All fields are required";
        } else {
            $checkBuyer = "SELECT * FROM buyers WHERE email = '$email' OR phone_number='$phone_number'";
            $resultCheckBuyer = $this->conn->query($checkBuyer);
            // to check if email or phone is used by sellers
            $sellerManager = new SellerManager();
            $checkSeller = $sellerManager->checkRegisteredSeller($email, $phone_number);
            if ($resultCheckBuyer->num_rows > 0) {
                $response['success'] = false;
                $response['error'] = 'Account creation failed';
                $response['message'] = "Email or phone already taken";
            } else if ($checkSeller == true) {
                $response = [
                    'success' => false,
                    'error' => 'Account creation failed',
                    'message' => "Email or phone already taken"
                ];
            } else {

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // for hashing

                $sql_insert_buyer = "INSERT INTO buyers (full_name,email,password,city,phone_number) VALUES ('$full_name', '$email', '$hashedPassword', '$address','$phone_number')";
                $result_insert_buyer = $this->conn->query($sql_insert_buyer);
                // header('') // redirect to otp section and confirm otp 
                if ($result_insert_buyer) {
                    $response['success'] = true;
                    $response['error'] = "success";
                    $response['message'] = "Account created successfully";
                    header('Location:allUserLogin.php');
                    exit();
                } else {
                    $response['success'] = false;
                    $response['error'] = "Error creating account";
                    $response['message'] = "Something went wrong";
                }
            }
        }
        return json_encode($response);
    }

    public function buyerLogin($email, $password)
    {
        if (empty($email) || empty($password)) {
            $response['success'] = false;
            $response['error'] = 'Login Failed';
            $response['message'] = "All fields are required";
            return json_encode($response); // Return the response immediately if fields are empty
        }

        $hashedPassword = md5($password);

        $response = array();
        $attempt = $this->passwordAttempt->getRemainingLoginAttempts($email);

        if ($attempt < 1) {
            $response['success'] = false;
            $response['attempt_left'] = $attempt;
            $response['next attempt'] = "Try after 1 min";
            $response['message'] = "Maximum login attempts reached. Please try again later.";
            return json_encode($response);
        }

        $sql_buyer_login = "SELECT * FROM buyers WHERE email='$email' AND password='$hashedPassword'";
        $result_login = $this->conn->query($sql_buyer_login);

        if (mysqli_num_rows($result_login) === 1) {
            $userDetails = mysqli_fetch_assoc($result_login);
            if ($userDetails['is_verified'] == 1) {
                session_start();
                $_SESSION['buyer_id'] = $userDetails['buyer_id'];
                $_SESSION['name'] = $userDetails['full_name'];

                // Reset login attempts for the email address upon successful login
                $this->passwordAttempt->reset_Login_Attempts($email);
                $response['success'] = true;
                $response['message'] = "Logged in successfully";
                $this->passwordAttempt->deleteAttempt($email);
                exit();
            } else {

                // send otp to user , if user is not valid
                // if ($otpResult = $this->mailInstance->sendOTP($email)) {
                //     // header('Location:conritmOtp.php'); // redirect user to otp input field form
                // }

                $response['success'] = false;
                $response['error'] = "Logging in process";
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
            $response['error'] = "Attempt left " . $attempt;
            $response['message'] = "Email and password not match.";

        }

        return json_encode($response);
    }

    public function allUserLogin($email, $password)
    {
        $response = [];
        if (empty($email) || empty($password)) {
            $response = [
                'success' => false,
                'error' => 'Login Failed',
                'message' => "All fields are required"
            ];
            return json_encode($response); // Return the response immediately if fields are empty
        }
        $isBuyer = $this->mailInstance->isBuyer($email); // function call from Mail class
        $tableName = $isBuyer ? 'buyers' : 'sellers';

        // Prepare the SQL statement
        $sql_user_login = "SELECT * FROM $tableName WHERE email=? OR phone_number=?";
        $stmt = $this->conn->prepare($sql_user_login);
        if (!$stmt) {
            // Handle the case where preparing the statement fails
            $response = [
                'success' => false,
                'error' => 'Error: ' . $stmt->get_warnings(),
                'message' => "Database error"
            ];
            return json_encode($response);
        }

        // Bind parameters and execute the statement
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result_login = $stmt->get_result();

        if ($result_login->num_rows === 1) {
            $userDetails = $result_login->fetch_assoc();
            $hashedPasswordFromDatabase = $userDetails['password'];

            if (password_verify($password, $hashedPasswordFromDatabase)) {
                if ($userDetails['is_verified'] == 1) {
                    session_start();
                    if ($userDetails['buyer_id']) {
                        $_SESSION['buyer_id'] = $userDetails['buyer_id'];
                        $_SESSION['full_name'] = $userDetails['full_name'];
                        $this->passwordAttempt->reset_Login_Attempts($email);

                        $response['success'] = true;
                        $response['message'] = "Logged in successfully";
                        $response['page'] = "bdashboard.php";

                        $this->passwordAttempt->deleteAttempt($email);
                        header('Location: bdashboard.php'); // Redirect to buyer dashboard
                        exit();

                    } else {
                        $_SESSION['seller_id'] = $userDetails['seller_id'];
                        $_SESSION['full_name'] = $userDetails['full_name'];
                        $this->passwordAttempt->reset_Login_Attempts($email);

                        $response['success'] = true;
                        $response['message'] = "Logged in successfully";

                        $this->passwordAttempt->deleteAttempt($email);
                        header('Location: seller/index.php'); // Redirect to seller dashboard
                        exit();
                    }
                } else {
                    $response = [
                        'success' => false,
                        'verified' => false,
                        'error' => 'Login Failed',
                        'message' => 'User is not verified'
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Login Failed',
                    'message' => 'Email or Password not match'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'error' => 'Login Failed',
                'message' => 'User not found'
            ];
        }

        return json_encode($response);
    }


    public function getBuyerDetails($id)
    {
        if (empty($id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'User not not found'
            ];
        }
        $response = array();

        try {
            $buyers_details = "SELECT * from buyers where buyer_id='$id'";
            $result_buyers_details = $this->conn->query($buyers_details);
            if ($result_buyers_details->num_rows > 0) {
                while ($row = $result_buyers_details->fetch_assoc()) {
                    $response[] = $row;
                }
                // $response = mysqli_fetch_assoc($result_buyers_details);
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Buyer not found'
                ];
            }

        } catch (Exception $e) {
            $response['error'] = $e;

        }
        return json_encode($response);
    }

    public function is_buyer($emailOrPhone)
    {
        $response = array();

        try {
            // Use prepared statement to prevent SQL injection
            $checkBuyer = "SELECT email, phone_number FROM buyers WHERE email=? OR phone_number=?";
            $stmt = $this->conn->prepare($checkBuyer);
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
                'error' => 'Failed',
                'message' => 'Error occurred: ' . $e->getMessage()
            ];
        }

        return json_encode($response);
    }



    // ********* function to update buyer name*********

    public function updateBuyerName($buyer_id, $full_name)
    {
        $response = array();

        try {
            // Use prepared statement to update the full_name
            $updateBuyer = "UPDATE buyers SET full_name=? WHERE buyer_id=?";
            $stmt = $this->conn->prepare($updateBuyer);
            $stmt->bind_param("si", $full_name, $buyer_id);

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

    public function updateBuyerEmail($buyer_id, $email)
    {
        $response = array();

        $is_buyer = $this->is_buyer($email);
        $buyerDecode = json_decode($is_buyer, true);
        if ($buyerDecode['success'] == true) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Email already taken'
            ];
        } else {
            $sellerManager = new SellerManager();
            $is_seller = $sellerManager->is_seller($email);
            $sellerDecode = json_decode($is_seller, true);
            if ($sellerDecode['success'] == true) {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Email already taken'
                ];
            } else {
                try {
                    $updateBuyer = "UPDATE buyers SET email=? WHERE buyer_id=?";
                    $stmt = $this->conn->prepare($updateBuyer);
                    $stmt->bind_param("si", $email, $buyer_id);

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
    public function updateBuyerPhone($buyer_id, $phone_number)
    {
        $response = array();
        $is_buyer = $this->is_buyer($phone_number);
        $buyerDecode = json_decode($is_buyer, true);
        if ($buyerDecode['success'] == true) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Phone number already taken'
            ];
        } else {
            $sellerManager = new SellerManager();
            $is_seller = $sellerManager->is_seller($phone_number);
            $sellerDecode = json_decode($is_seller, true);
            if ($sellerDecode['success'] == true) {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Phone number already taken'
                ];
            } else {
                try {
                    $updateBuyer = "UPDATE buyers SET phone_number=? WHERE buyer_id=?";
                    $stmt = $this->conn->prepare($updateBuyer);
                    $stmt->bind_param("si", $phone_number, $buyer_id);

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


    //*************** */ function  to update email and phone number at once ****************8
    public function updateBuyerDetails($buyer_id, $full_name, $email, $phone_number)
    {
        $response = [];

        if (empty($full_name) || empty($email)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Fields are required'
            ];
            return json_encode($response);
        }


        $buyerResponse = $this->is_buyer($email);
        $buyerDecode = json_decode($buyerResponse, true);
        if ($buyerDecode['success'] == true) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Email or phone already taken'
            ];
            return json_encode($response);
        } else {
            // to check if email or phone is taken by sellers
            $sellerManager = new SellerManager();
            $sellerResponse = $sellerManager->is_Seller($email);
            $sellerDecode = json_decode($sellerResponse, true);
            if ($sellerDecode['success'] == true) {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Email or phone already taken'
                ];
                return json_encode($response);
            } else {
                //  if email or phone number is not taken by other users
                try {
                    $update_sql = "UPDATE buyers SET full_name = ?, email = ?, phone_number = ? WHERE buyer_id = ?";
                    $stmt_update = $this->conn->prepare($update_sql);
                    $stmt_update->bind_param("sssi", $full_name, $email, $phone_number, $buyer_id);
                    $stmt_update->execute();
                    if ($stmt_update->affected_rows > 0) {
                        $response = [
                            'success' => true,
                            'error' => 'Success',
                            'message' => 'Profile Successfully Update'
                        ];
                        return json_encode($response);
                    } else {
                        $response = [
                            'success' => false,
                            'error' => 'Failed',
                            'message' => 'Error Updating Profile ',
                            'page' => 'buyerProfile.php'
                        ];
                        return json_encode($response);
                    }
                } catch (Exception $e) {
                    $response = [
                        'success' => false,
                        'error' => 'Failed',
                        'message' => 'Error Updating Profile: ' . $e->getMessage()
                    ];
                }
            }
        }
        return json_encode($response);
    }


    public function logout()
    {
        session_start();
        // $_SESSION = array();
        session_destroy();
        header("Location:allUserLogin.php");
        exit();
    }

}

?>