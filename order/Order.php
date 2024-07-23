<?php
require_once (__DIR__ . '/../cart/CartManager.php');
require_once (__DIR__ . '/../config/DatabaseConnection.php');
require_once (__DIR__ . '/../product/ProductManager.php');
class Order extends DatabaseConnection
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getBuyerOrderList($buyer_id)
    {
        $response = array();
        if (empty($buyer_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Buyer not found'
            ];
            return json_encode($response);
        }

        $sql = "SELECT products.image,products.name,products.price,orders.order_id,orders.total_amount,orders.created_at,orders.payment_status,orders.quantity,orders.delivery_status 
        from orders
        INNER JOIN products
        ON orders.product_id=products.product_id
        where buyer_id='$buyer_id'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
                // $response['data'] = $row;
            }
            $response['success'] = true;
            $response['message'] = 'Data found';
        } else {
            $response = [
                'success' => false,
                'error' => 'No Orders',
                'message' => 'No orders yet'
            ];
        }
        return json_encode($response);
    }
    public function getSellerOrderList($seller_id)
    {
        $response = array();
        if (empty($seller_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Order not found'
            ];
            return json_encode($response);
        }

        // Specify the table name for the seller_id column to avoid ambiguity
        $sql = "SELECT products.image, products.name, products.price, orders.order_id, orders.total_amount, orders.created_at, orders.payment_status, orders.quantity, orders.delivery_status 
                FROM orders
                INNER JOIN products ON orders.product_id = products.product_id
                WHERE orders.seller_id = '$seller_id'";

        $result = $this->conn->query($sql);

        if ($result === false) {
            // Log the error message and return a user-friendly response
            error_log("Database query failed: " . $this->conn->error);
            $response = [
                'success' => false,
                'error' => 'Database Error',
                'message' => 'An error occurred while retrieving the orders'
            ];
            return json_encode($response);
        }

        if ($result->num_rows > 0) {
            $response['data'] = array();
            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }
            $response['success'] = true;
            $response['message'] = 'Data found';
        } else {
            $response = [
                'success' => false,
                'error' => 'No Orders',
                'message' => 'No orders yet'
            ];
        }
        return json_encode($response);
    }


    public function getBuyerOrderDetails($buyer_id, $order_id)
    {
        $response = array();
        if (empty($buyer_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Order not found'
            ];
            return json_encode($response);
        }

        $sql = "SELECT products.*,orders.* ,shipping_address.*
        from orders
        INNER JOIN 
        products ON orders.product_id=products.product_id
        INNER JOIN 
        shipping_address ON orders.shipping_address_id=shipping_address.address_id
        where order_id='$order_id'";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // $response['data'][] = $row;
                $response[] = $row;

                // $response['success'] = true;
                // $response['message'] = 'Data found';
            }
        } else {
            $response = [
                'success' => false,
                'error' => 'No Orders',
                'message' => 'No orders yet'
            ];
        }
        return json_encode($response);

    }
    public function getSellerOrderDetails($seller_id, $order_id)
    {
        $response = array();
        if (empty($seller_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Order not found'
            ];
            return json_encode($response);
        }

        $sql = "SELECT products.*,orders.* ,shipping_address.*
        from orders
        INNER JOIN 
        products ON orders.product_id=products.product_id
        INNER JOIN 
        shipping_address ON orders.shipping_address_id=shipping_address.address_id
        where order_id='$order_id'";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // $response['data'][] = $row;
                $response[] = $row;

                // $response['success'] = true;
                // $response['message'] = 'Data found';
            }
        } else {
            $response = [
                'success' => false,
                'error' => 'No Orders',
                'message' => 'No orders yet'
            ];
        }
        return json_encode($response);

    }


    public function getSellerDetailsFromProductId($product_id)
    {
        $response = array();
        if (empty($product_id)) {
            return "id not found";
        }
        $sql = "SELECT
        sellers.seller_id
        FROM
        sellers
        INNER JOIN
        products ON sellers.seller_id = products.seller_id
        WHERE
        products.product_id = $product_id
    ";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Seller not found'
            ];

        }
        return json_encode($response);
    }
    public function checkOut($product_id) // display information 
    {
        $response = array();
        if (empty($product_id)) {
            return 'id not get';
        }
        $sql = "SELECT buyers.buyer_id,
                       buyers.full_name,
                       buyers.email,
                       buyers.phone_number,
                       buyers.district,
                       buyers.city,
                       buyers.street,
                       buyers.state,
                       buyers.zip,
                       products.*
                FROM buyers
                LEFT JOIN carts ON buyers.buyer_id = carts.buyer_id 
                LEFT JOIN products ON carts.product_id = products.product_id
                WHERE products.product_id = '$product_id'";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Product not found'
            ];
        }

        $this->conn->close();
        return json_encode($response);
    }


    public function cartDetails($cart_id)
    {
        $response = array();
        $sql = "SELECT products.*, carts.* 
        FROM carts
        INNER JOIN products ON carts.product_id=products.product_id 
         where cart_id='$cart_id'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Cart checkout not found'
            ];
            // $productManager = new ProductManager();
            // $productResponse = $productManager->getProductDetails($cart_id);
            // $response = json_decode($productResponse, true);
        }
        return json_encode($response);

    }

    //  buyer_id and product id  can delete by
    public function makeOrder($buyer_id, $seller_id, $product_id, $quantity, $total_amount, $shippingDetails)
    {
        try {
            $response = array();
            $decreaseQuantityDecode['success'] = '';
            // to decreasee quantity from products if user buy products without adding to carts
            if (empty($shippingDetails['cart_id'])) {
                // echo $shippingDetails['cart_id'];
                // echo "cart id is empty";
                $productManager = new ProductManager();
                $decreaseQuantity = $productManager->decreaseProductQuantity($quantity, $product_id);
                $decreaseQuantityDecode = json_decode($decreaseQuantity, true);
                // print_r($decreaseQuantity['success']);
                // print_r($decreaseQuantityDecode['success']);
                // echo $decreaseQuantityDecode['success'];
            }

            // echo $decreaseQuantityDecode['success'];
            // exit();

            if ($decreaseQuantityDecode == true) {
                // echo $decreaseResponseDecode['message'];

                // array update with buyer_id, to store shipping address for buyer
                $shippingDetails['buyer_id'] = $buyer_id;
                $shippingResponse = $this->storeOrUpdateShippingAddress($shippingDetails);
                $shippingResponseArray = json_decode($shippingResponse, true);

                // Check if shipping details were successfully stored or updated
                if ($shippingResponseArray['success'] == true) {

                    // to store shippingAddressId in order table
                    $shippingAdressDetail = $this->getShippingAddressDetails($shippingDetails);
                    $shippingAdress_id = json_decode($shippingAdressDetail, true);

                    $buyer_id = $shippingDetails['buyer_id'];
                    $sql = "INSERT INTO orders (buyer_id, seller_id, product_id, quantity, total_amount,shipping_address_id) VALUES ('$buyer_id', '$seller_id', '$product_id', '$quantity', '$total_amount','$shippingAdress_id')";
                    if ($this->conn->query($sql) === TRUE) {
                        // Order placed successfully
                        $response = [
                            'success' => true,
                            'message' => 'Your order placed successfully ' . $shippingResponseArray['message']
                        ];

                        // 
                        if (!empty($shippingDetails['cart_id'])) {
                            // // Delete the product from the cart
                            $deleteCartSQL = "DELETE FROM carts WHERE buyer_id = '$buyer_id' AND product_id = '$product_id'";
                            $this->conn->query($deleteCartSQL);
                        }
                        header('Location:../buyerOrderList.php');
                        exit();
                    } else {
                        // Failed to place order
                        $response = [
                            'success' => false,
                            'message' => 'Failed to place order'
                        ];
                    }
                } else {
                    // Failed to store or update shipping address
                    $response = [
                        'success' => false,
                        'message' => 'Failed to store or update shipping address: ' . $shippingResponseArray['message']
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Failed to update quantity: ' . $decreaseQuantityDecode['message']
                ];
            }
        } catch (Exception $e) {
            // Exception occurred
            $response = [
                'success' => false,
                'message' => 'Something went wrong while processing your order: ' . $e->getMessage()
            ];
        }

        return json_encode($response);
    }


    public function getShippingAddressDetails($shippingDetails)
    {
        $response = [];
        // Check if all necessary shipping details are provided
        if (empty($shippingDetails['buyer_id'])) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'User Id not found.'
            ];
            return json_encode($response);
        }
        $buyer_id = $shippingDetails['buyer_id'];

        // Example SQL query to check if the user's shipping details 
        $checkStoredAddress = "SELECT * FROM shipping_address WHERE buyer_id = '$buyer_id'";
        $result = $this->conn->query($checkStoredAddress);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = $row['address_id'];
        } else {
            $response = [
                'success' => false,
                'message' => "Shipping address not found : " . $this->conn->error
            ];

        }

        return json_encode($response);
    }

    // Function to store or update shipping address
    public function storeOrUpdateShippingAddress($shippingDetails)
    {
        $response = [];
        // Check if all necessary shipping details are provided
        if (empty($shippingDetails['buyer_id']) || empty($shippingDetails['full_name']) || empty($shippingDetails['district']) || empty($shippingDetails['city'])) {
            $response = [
                'success' => false,
                'message' => 'Missing required shipping details.'
            ];
            return json_encode($response);
        }

        // Escape values to prevent SQL injection
        $buyer_id = $shippingDetails['buyer_id'];
        $full_name = $shippingDetails['full_name'];
        $email = $shippingDetails['email'];
        $phone_number = $shippingDetails['phone_number'];
        $district = $shippingDetails['district'];
        $city = $shippingDetails['city'];
        $street = $shippingDetails['street'];
        $state = $shippingDetails['state'];
        $postal_code = $shippingDetails['postal_code'];
        $cart_id = $shippingDetails['cart_id'];
        $seller_id = $shippingDetails['seller_id'];

        // Example SQL query to check if the user's shipping details already exist
        $checkStoredAddress = "SELECT * FROM shipping_address WHERE buyer_id = '$buyer_id'";
        $result = $this->conn->query($checkStoredAddress);

        if ($result->num_rows > 0) {
            // Shipping details already exist, update them
            $row = $result->fetch_assoc();
            $buyer_id = $row['buyer_id'];

            $updateSql = "UPDATE shipping_address SET full_name='$full_name', email='$email', phone_number='$phone_number', district='$district', city='$city', street='$street', state='$state', postal_code='$postal_code' WHERE buyer_id='$buyer_id'";
            if ($this->conn->query($updateSql) === TRUE) {
                $response = [
                    'success' => true,
                    'message' => "Shipping address is updated!"
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "Error updating shipping address: " . $this->conn->error
                ];
            }
        } else {
            // Shipping details don't exist, insert them
            $sql = "INSERT INTO shipping_address (buyer_id, seller_id, full_name, email, phone_number, district, city, street, state, postal_code) VALUES ('$buyer_id', '$seller_id',  '$full_name', '$email', '$phone_number', '$district', '$city', '$street', '$state', '$postal_code')";
            if ($this->conn->query($sql) === TRUE) {
                $response = [
                    'success' => true,
                    'message' => "Shipping address stored successfully!"
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "Failed to store shipping address: " . $this->conn->error
                ];
            }
        }

        return json_encode($response);
    }


    public function updateDeliveryStatus($order_id, $delivery_status)
    {
        $response = [];

        if (empty($order_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Order not selected'
            ];
            return json_encode($response);

        } else if (empty($delivery_status)) {


            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Delivery status not selected'
            ];
            return json_encode($response);
        } else {
            try {
                $updateSql = "UPDATE orders SET delivery_status='$delivery_status' WHERE order_id = '$order_id'";
                $result = $this->conn->query($updateSql);
                if ($result) {
                    $response = [
                        'success' => true,
                        'message' => "Delivery status update"
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => "Order not update : " . $this->conn->error
                    ];

                }
            } catch (Exception $e) {
                $response = [
                    'success' => false,
                    'message' => "Delivery status not update : " . $this->conn->error

                ];
            }
        }

        return json_encode($response);
    }



}


?>