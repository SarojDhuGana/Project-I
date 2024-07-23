<?php
require_once ('../config/DatabaseConnection.php');
class CommentManager extends DatabaseConnection
{

    function __construct()
    {
        parent::__construct();
    }

    function addComment($product_id, $comment_text, $buyer_id = null, $seller_id = null)
    {
        $response = [];

        // Validate inputs
        if (empty($product_id) || empty($comment_text)) {
            $response = [
                'success' => false,
                'message' => 'Product ID and Comment Text are required fields.'
            ];
            return json_encode($response);
        }

        try {
            // Prepare SQL statement
            $sql = "INSERT INTO product_comments (product_id, buyer_id, seller_id, comment_text) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);

            // Bind parameters
            $stmt->bind_param("iiss", $product_id, $buyer_id, $seller_id, $comment_text);

            // Execute statement
            if ($stmt->execute()) {
                $response = [
                    'success' => true,
                    'message' => 'Comment posted successfully.'
                ];
            } else {
                throw new Exception("Failed to execute SQL statement: " . $stmt->error);
            }

            // Close statement
            $stmt->close();
        } catch (Exception $e) {
            // Handle exceptions
            $response = [
                'success' => false,
                'message' => 'Failed to post comment. ' . $e->getMessage()
            ];
        }

        return json_encode($response);
    }

    public function commentList($product_id)
    {
        $response = [];

        // Validate product_id
        if (empty($product_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Product ID is required'
            ];
            return json_encode($response);
        }

        // Use prepared statement to prevent SQL injection
        $sql = "SELECT pc.*, b.buyer_id, b.full_name AS buyer_name, s.seller_id, s.full_name AS seller_name 
            FROM product_comments pc
            LEFT JOIN buyers b ON pc.buyer_id = b.buyer_id
            LEFT JOIN sellers s ON pc.seller_id = s.seller_id
            WHERE pc.product_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $comments = [];
            while ($row = $result->fetch_assoc()) {
                $comments[] = $row;
            }

            $response = [
                'success' => true,
                'message' => 'Comments found',
                'data' => $comments
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to retrieve comments'
            ];
        }

        $stmt->close();
        return json_encode($response);
    }

    public function updateComment($comment_id, $commentText)
    {
        $response = [];
        if (empty($comment_id) || empty($commentText)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Field is required'
            ];
            return json_encode($response);
        }
        $sql = "UPDATE product_comments SET comment_text = '$commentText' WHERE comment_id = '$comment_id'";
        $result = $this->conn->query($sql);
        if ($result) {
            $response = [
                'success' => true,
                'error' => 'Success',
                'message' => 'Comment updated'
            ];
        } else {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Error updating comment'
            ];
        }
        return json_encode($response);
    }
    public function deleteComment($comment_id)
    {
        $response = [];
        if (empty($comment_id)) {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Comment ID is required'
            ];
            return json_encode($response);
        }
        $sql = "DELETE FROM product_comments WHERE comment_id = '$comment_id'";
        $result = $this->conn->query($sql);
        if ($result) {
            $response = [
                'success' => true,
                'error' => 'Success',
                'message' => 'Comment deleted'
            ];
        } else {
            $response = [
                'success' => false,
                'error' => 'Failed',
                'message' => 'Error deleting comment'
            ];
        }
        return json_encode($response);
    }


    function addReply($replier_id, $product_id)
    {

    }
    function commentReply()
    {

    }
}

?>