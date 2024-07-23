<?php
require_once (__DIR__ . '/../config/DatabaseConnection.php');
class CartManager extends DatabaseConnection
{
    public $conn;
    public function __construct()
    {
        parent::__construct(); // Call parent constructor to establish database connection
    }
    public function addCart($buyer_id, $product_id, $quantity, $price)
    {
        $response = array();
        if (empty($product_id)) {
            $response = [
                'success' => false,
                'errorr' => 'Failed to cart',
                'message' => 'Select product'
            ];
            return json_encode($response);
        } else if (empty($buyer_id)) {
            header('Location:../allUserLogin.php');
        }
        try {
            // Get available stock for the product
            $get_stock_sql = "SELECT quantity FROM products WHERE product_id = '$product_id' ";
            $result_get_stock = $this->conn->query($get_stock_sql);

            if ($result_get_stock && $result_get_stock->num_rows > 0) {
                $row_stock = $result_get_stock->fetch_assoc();
                $available_stock = $row_stock['quantity'];
                $cartManager = new CartManager();

                // Check if there is sufficient stock
                if ($available_stock >= $quantity) {

                    //  for checking if carts has any product with product_id 
                    // if product already exist, don't create new cart id

                    $check_product_on_cart = "SELECT cart_id,buyer_id,product_id,cart_quantity,total_amount from carts where product_id='$product_id' AND buyer_id='$buyer_id' ";
                    $check_product_on_cart_result = $this->conn->query($check_product_on_cart);

                    if ($check_product_on_cart_result && $check_product_on_cart_result->num_rows > 0) {
                        $row_cart_stock = $check_product_on_cart_result->fetch_assoc();

                        $cart_id = $row_cart_stock['cart_id'];

                        $oldQuantity = $row_cart_stock['cart_quantity']; // cart quantity from database
                        $newQuantity = $oldQuantity + $quantity;

                        $oldTotalAmount = $row_cart_stock['total_amount'];
                        $newTotalAmount = $oldTotalAmount + ($price * $quantity);

                        $update_cart_sql = "UPDATE carts SET cart_quantity='$newQuantity',total_amount='$newTotalAmount' WHERE cart_id='$cart_id' ";
                        $update_cart_stock = $this->conn->query($update_cart_sql);

                        // Deduct product quantity from the database
                        $functionResponse = $cartManager->updateProductFromCartActivity($available_stock, $quantity, $product_id);

                        if ($update_cart_stock) {
                            $response = [
                                'success' => true,
                                'error' => 'Success',
                                'message' => 'Updated your cart'
                            ];

                        } else {
                            $response = [
                                'success' => true,
                                'error' => 'Failed',
                                'message' => 'Failed to update your cart'
                            ];
                        }
                    } else {

                        // Add product to the cart
                        $total_amount = $quantity * $price;
                        $add_cart_sql = "INSERT INTO carts (buyer_id, product_id, cart_quantity, total_amount) VALUES ('$buyer_id', '$product_id', '$quantity', '$total_amount')";
                        $result_add_cart = $this->conn->query($add_cart_sql);

                        if ($result_add_cart) {
                            // Deduct product quantity from the database
                            $functionResponse = $cartManager->updateProductFromCartActivity($available_stock, $quantity, $product_id);

                            $response = [
                                'success' => true,
                                'error' => 'Success',
                                'message' => 'Added to cart'
                            ];
                        } else {
                            // Commit transaction
                            $response = [
                                'success' => true,
                                'error' => 'Failed',
                                'message' => 'Failed to add cart'
                            ];
                            // $response = [
                            //     'success' => true,
                            //     'message' => 'Error adding to cart'
                            // ];
                            // throw new Exception("Error adding to cart");
                        }
                        // $this->conn->commit();
                    }
                } else {
                    $response = [
                        'success' => false,
                        'error' => 'Failed',
                        'message' => 'Insufficient stock'
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Product not found'
                ];
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();

            $response = [
                'success' => false,
                'error' => 'Failedd',
                'message' => $e->getMessage()
            ];
        }
        return json_encode($response);
    }


    // this function should be on product class
    // for reducing quantity in product table after adding product to cart
    public function updateProductFromCartActivity($available_stock, $quantity, $product_id)
    {
        $response = array();
        if (empty($product_id) && empty($available_stock) && empty($quantity)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => "Function need all arguments value"
            ];
            return $response;
        }
        $new_stock = $available_stock - $quantity;
        $update_stock_sql = "UPDATE products SET quantity = '$new_stock' WHERE product_id = '$product_id'";
        $result_update_stock = $this->conn->query($update_stock_sql);
        if ($result_update_stock) {
            $response = [
                'success' => true,
                'error' => 'Success',
                'message' => "Products available in cart",
            ];
            // return $response;
        } else {
            $response = [
                'success' => true,
                'error' => 'Failed',
                'message' => "Products not available in cart",
            ];
        }
        return $response;
    }




    public function viewCart($buyer_id)
    {
        $response = array();
        if (empty($buyer_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'User not found'
            ];
            return json_encode($response);
        }

        try {
            $sql = "SELECT products.product_id, products.name, products.description, products.price,products.image, carts.cart_quantity, carts.total_amount, carts.cart_id
                FROM carts
                INNER JOIN products ON carts.product_id = products.product_id
                WHERE carts.buyer_id = $buyer_id";

            $result = $this->conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $response['data'][] = $row;
                }
                $response['success'] = true;
                $response['message'] = 'Data found';
            } else {
                $response = [
                    'success' => false,
                    'error' => 'Failed',
                    'message' => 'Cart is empty'
                ];
            }

        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Error ' . $e->getMessage()
            ];
        }

        $this->conn->close();
        return json_encode($response);
    }


    public function deleteCartProduct($cart_ids)
    {
        $response = array();

        // Convert single ID to array if it's not already an array
        if (!is_array($cart_ids)) {
            $cart_ids = array($cart_ids);
        }

        if (empty($cart_ids)) {
            $response = [
                'success' => false,
                'message' => "Select product "
            ];
        }
        try {
            $sql = "DELETE FROM carts WHERE cart_id IN (" . implode(",", $cart_ids) . ")";
            $result = $this->conn->query($sql);

            if ($result) {
                $response = [
                    'success' => true,
                    'message' => "Successfully deleted"
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "Something went wrong"
                ];
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        return json_encode($response);
    }


    //  delete and update 
    public function removeProductsFromCart($cart_ids)
    {
        $response = array();

        try {
            foreach ($cart_ids as $cart_id) {
                // Fetch the product details from the cart
                $get_cart_details_sql = "SELECT * FROM carts WHERE cart_id = '$cart_id'";
                $result_get_cart_details = $this->conn->query($get_cart_details_sql);

                if ($result_get_cart_details->num_rows > 0) {
                    $row = $result_get_cart_details->fetch_assoc();
                    $product_id = $row['product_id'];
                    $quantity = $row['cart_quantity'];

                    // Update the product quantity in the products table
                    $update_product_quantity_sql = "UPDATE products SET quantity = quantity + $quantity WHERE product_id = '$product_id'";
                    $result_update_product_quantity = $this->conn->query($update_product_quantity_sql);

                    if ($result_update_product_quantity) {

                        // Delete the product from the cart
                        $delete_cart_sql = "DELETE FROM carts WHERE cart_id = '$cart_id'";
                        $result_delete_cart = $this->conn->query($delete_cart_sql);

                        if (!$result_delete_cart) {
                            $response[] = [
                                'cart_id' => $cart_id,
                                'success' => false,
                                'message' => 'Error removing product from cart'
                            ];
                        }
                    } else {
                        $response[] = [
                            'cart_id' => $cart_id,
                            'success' => false,
                            'message' => 'Error updating product quantity'
                        ];
                    }
                } else {
                    $response[] = [
                        'cart_id' => $cart_id,
                        'success' => false,
                        'message' => 'Cart ID not found'
                    ];
                }
            }

            // If all operations were successful
            if (empty($response)) {
                $response = [
                    'success' => true,
                    'message' => 'Products removed from cart successfully'
                ];
            }
        } catch (Exception $e) {
            $response[] = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return json_encode($response);
    }

    public function removeExpiredFromCart()
    {
        $response = array();
        try {
            // Get carts that are more than 3 days old
            $three_days_ago = date('Y-m-d H:i:s', strtotime('-3 days'));
            $get_expired_carts = "SELECT * FROM carts WHERE created_at < '$three_days_ago'";
            $result_expired_carts = $this->conn->query($get_expired_carts);

            if ($result_expired_carts->num_rows > 0) {
                // Loop through expired carts
                while ($row = $result_expired_carts->fetch_assoc()) {
                    $cart_id = $row['cart_id'];
                    $product_id = $row['product_id'];
                    $quantity = $row['quantity'];

                    // Undo quantity in the products table
                    $undo_quantity_sql = "UPDATE products SET quantity = quantity + $quantity WHERE product_id = $product_id";
                    $result_undo_quantity = $this->conn->query($undo_quantity_sql);

                    // Remove expired cart from the carts table
                    $remove_cart_sql = "DELETE FROM carts WHERE cart_id = $cart_id";
                    $result_remove_cart = $this->conn->query($remove_cart_sql);

                    if (!$result_undo_quantity || !$result_remove_cart) {
                        $response = [
                            'success' => false,
                            'message' => 'Error removing expired products from cart'
                        ];
                    }
                }
            }
            // Success message if no errors occurred
            $response = [
                'success' => true,
                'message' => 'Expired products removed from cart successfully'
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        return json_encode($response);
    }

}


?>