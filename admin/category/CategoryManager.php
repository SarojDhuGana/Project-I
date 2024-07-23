<?php
require_once (__DIR__ . '/../../config/DatabaseConnection.php');

class CategoryManager
{
    private $conn;
    // Constructor to establish database connection
    public function __construct()
    {
        $databaseConnection = new DatabaseConnection();
        $this->conn = $databaseConnection->conn;

        // Check if the connection is still open
        if ($this->conn->connect_errno) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    public function createCategory($name, $image)
    {
        $response = array();
        if (empty($name)) {
            $response = [
                'success' => false,
                'message' => 'Field is required'
            ];
            return json_encode($response);
        }
        $sql_check_email = "SELECT * FROM categories WHERE name = '$name'";
        $result_check_email = $this->conn->query($sql_check_email);

        if ($result_check_email->num_rows > 0) {
            $response = [
                'success' => false,
                'message' => "Category name already exists"
            ];
        } else {

            $sql_insert_category = "INSERT INTO categories(name,image) VALUES ('$name','$image')";

            $result_insert_category = $this->conn->query($sql_insert_category);
            if ($result_insert_category) {
                $response = [
                    'success' => true,
                    'message' => "Category created successfully"
                ];

            } else {
                $response = [
                    'success' => false,
                    'message' => "Something went wrong"
                ];
            }

        }
        return json_encode($response);
    }
    public function getCategoryDetail($cat_id)
    {
        $response = array();

        if (empty($cat_id)) {
            $response = [
                'success' => false,
                'message' => 'Select category item'
            ];
            return json_encode($response);
        }
        $sql = "SELECT * FROM categories WHERE category_id='$cat_id' ";
        $result = $this->conn->query($sql);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $response[] = $row;
            }
            //     $response = [
            //         'success' => true,
            //         'message' => 'Category found',
            //         'category' => $row
            // ];

        } else {
            $response = [
                'success' => false,
                'message' => 'Category not found'
            ];
        }
        return json_encode($response);

    }
    public function updateCategory($name, $imageName, $cat_id)
    {
        print_r($imageName);
        $response = array();
        // $updateSql = "UPDATE products SET name='$name',price='$price',quantity='$quantity'  where product_id='$product_id'";
        if (empty($name)) {
            $response['success'] = false;
            $response['message'] = "Enter category name";
        } else {
            $updateSql = "UPDATE categories SET name='$name', image='$imageName' WHERE category_id='$cat_id'";
            $updateSqlResult = $this->conn->query($updateSql);
            if ($updateSqlResult) {
                $response = [
                    'success' => true,
                    'message' => "Category update succesfully"
                ];
                header('Location:categoryList.php');
                exit();
            } else {
                $response = [
                    'success' => true,
                    'message' => "Something went wrong"
                ];
            }
        }
        return json_encode($response);
    }
    public function deleteCategory($cat_id)
    {
        $response = array();

        if (empty($cat_id)) {
            $response = [
                'success' => false,
                'message' => 'Select category item'
            ];
            return json_encode($response);
        }
        $sql = "DELETE FROM categories WHERE category_id='$cat_id' ";
        $result = $this->conn->query($sql);
        if ($result) {

            $response = [
                'success' => true,
                'message' => 'Category deleted'
            ];
            header('Location:categoryList.php');
            exit();
        } else {
            $response = [
                'success' => false,
                'message' => 'Can not delete category'
            ];
        }// Encode the response as URL-safe string
        $encodedResponse = urlencode(json_encode($response));

        // Redirect back to categoryList.php with response message as parameter
        header("Location: categoryList.php?response={$encodedResponse}");
        exit();
    }

    public function list()
    {
        $response = array();

        $sql = "SELECT * FROM categories ";
        $result = $this->conn->query($sql);
        // $resultRes = mysqli_fetch_assoc($result);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }

        }
        return json_encode($response);
    }
}


?>