<?php
require_once (__DIR__ . '/../config/DatabaseConnection.php');
// error_reporting(E_ERROR);
class ProductManager
{
    private $conn;

    // Constructor to establish database connection
    public function __construct()
    {
        // Create a new instance of the DatabaseConnection class to establish a database connection
        $databaseConnection = new DatabaseConnection();
        $this->conn = $databaseConnection->conn;

        // Check if the connection is still open
        if ($this->conn->connect_errno) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }


    public function createProduct($productDetails)
    {
        $response = array(); // Initialize an empty array for the response

        // Check if all required fields are provided
        if (empty($productDetails['name']) || empty($productDetails['price']) || empty($productDetails['category_id']) || empty($productDetails['seller_id']) || empty($productDetails['quantity'])) {
            $response['success'] = false;
            $response['error'] = 'Failed';
            $response['message'] = "All fields are required";
        } else {
            // Prepare the SQL statement with placeholders
            $sql_insert_products = "INSERT INTO products (name, description, price, category_id, seller_id, quantity, image) VALUES (?, ?, ?, ?, ?, ?, ?)";

            // Create a prepared statement
            $stmt = $this->conn->prepare($sql_insert_products);

            // Bind parameters
            $stmt->bind_param("ssdiiis", $productDetails['name'], $productDetails['description'], $productDetails['price'], $productDetails['category_id'], $productDetails['seller_id'], $productDetails['quantity'], $productDetails['image']);

            // Execute the statement
            $result_insert_product = $stmt->execute();

            if ($result_insert_product) {
                $response['success'] = true;
                $response['error'] = 'Success';
                $response['message'] = "Product created successfully";
                header('Location:../seller/index.php');
            } else {
                $response['success'] = false;
                $response['error'] = 'Failed';
                $response['message'] = "Can not added product";
            }

            // Close the statement
            $stmt->close();
        }

        return json_encode($response); // Return the response in JSON format
    }


    public function decreaseProductQuantity($quantity, $product_id)
    {
        $response = array();
        echo $quantity;
        // exit();
        if (empty($quantity)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Quantity is empty'
            ];
            return json_encode($response);
        }
        try {
            $sql = "UPDATE products SET quantity=quantity-'$quantity' WHERE product_id='$product_id'";
            $result = mysqli_query($this->conn, $sql);
            if ($result) {
                if (mysqli_affected_rows($this->conn) > 0) {
                    $response = [
                        'success' => true,
                        'message' => "Quantity updated"
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => "No products found"
                    ];
                }
            } else {
                throw new Exception(mysqli_error($this->conn));
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        // Return the response in JSON format
        return json_encode($response);
    }


    public function viewProductList()
    {
        $response = array();
        try {

            $sql = "SELECT * FROM products";
            $result = mysqli_query($this->conn, $sql);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        if ($result && mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                $response['data'][] = $row; // Add each product to the array
            }
            $response['success'] = true;
            $response['message'] = 'Data found';
        } else {
            $response = [
                'success' => false,
                'message' => "No products found"
            ];
        }

        // Return the response in JSON format
        return json_encode($response);
    }

    public function getProductDetails($product_id)
    {
        $response = array(); // Initialize an empty array for the response

        if (empty($product_id)) {
            $response = [
                'success' => false,
                'message' => 'Product id not found'
            ];
            return json_encode($response);
        }
        // Query to retrieve product details based on the product ID
        $sql = "SELECT * FROM products WHERE product_id = $product_id";
        $result = mysqli_query($this->conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $response['data'][] = $row;
            }
            $response['success'] = true;
            // $response['message'] = "Product found";

        } else {
            $response = [
                'success' => false,
                'message' => 'No product found'
            ];
        }
        return json_encode($response);
    }


    public function displayProductByCategory($categoryId)
    {
        $response = array();
        $sql = "SELECT * FROM products WHERE category_id = $categoryId";
        $result = mysqli_query($this->conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                $response[] = $row; // Add each product to the array
            }
            // $response['success'] = true;
            // $response['products'] = $products;
        } else {
            $response = [
                'success' => false,
                'message' => 'No products found for category ID: ' . $categoryId
            ];
        }

        return json_encode($response);
    }


    // for edit product to display old details
    public function editProduct($product_id)
    {
        $response = array();
        $sql = "SELECT * FROM products WHERE product_id = $product_id";
        $result = mysqli_query($this->conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $response[] = $row;
            }

        } else {
            $response = [
                'success' => false,
                'message' => 'No products found '
            ];
        }
        return json_encode($response);
    }

    public function updateProduct($productDetails)
    {
        $response = array();

        if (empty($productDetails['name']) || empty($productDetails['price']) || empty($productDetails['category_id']) ) {
            $response['success'] = false;
            $response['message'] = "All fields are required";
            return json_encode($response);
        } else {
            // Prepare the SQL statement with placeholders
            $updateSql = "UPDATE products SET name=?, description=?, price=?, category_id=?, quantity=?, image=? WHERE product_id=?";

            // Prepare the statement
            $stmt = $this->conn->prepare($updateSql);
            if (!$stmt) {
                $response['success'] = false;
                $response['message'] = "Failed to prepare statement";
                return json_encode($response);
            }

            // Bind parameters
            $stmt->bind_param("ssdiiss", $productDetails['name'], $productDetails['description'], $productDetails['price'], $productDetails['category_id'], $productDetails['quantity'], $productDetails['image'], $productDetails['product_id']);

            // Execute the statement
            $result = $stmt->execute();
            if ($result) {
                $response['success'] = true;
                $response['message'] = "Product updated successfully";
                header('Location:../seller/productList.php');
            } else {
                $response = [
                    'success' => true,
                    'error' => 'Failed',
                    'message' => "Failed to update product"
                ];
            }
            // Close the statement
            $stmt->close();
        }

        return json_encode($response);
    }

    public function deleteProduct($product_id)
    {
        $response = array();
        if (empty($product_id)) {
            return;
        }
        $delete_sql = "DELETE FROM products WHERE product_id='$product_id'";
        $result = mysqli_query($this->conn, $delete_sql);
        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Deleted successfully'
            ];
            header('Location:../seller/productList.php');
            exit();
        } else {
            $response = [
                'success' => false,
                'message' => 'Something went wrong while deleting'
            ];
        }
        return json_encode($response);
    }

    function searchProduct($name)
    {
        $response = array();
        if (empty($name)) {
            return;
        }
        $sql = "SELECT * FROM products WHERE name LIKE '%$name%'";

        $result = mysqli_query($this->conn, $sql);
        $result = mysqli_query($this->conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                $response[] = $row;
            }
            // $response['success'] = true;
            // $response['products'] = $products;
        } else {
            $response = [
                'success' => false,
                'message' => 'Products not found'
            ];
        }
        return json_encode($response);
    }

}
?>