<?php
require_once ('../order/Order.php');
session_start();
if (!empty($_SESSION['seller_id'])) {
    if (!empty($_GET['order_id'])) {
        $seller_id = $_SESSION['seller_id'];
        $order_id = $_GET['order_id'];
        $orderManager = new Order();
        $orderDetails = $orderManager->getSellerOrderDetails($seller_id, $order_id);
        $orderDetailsDecode = json_decode($orderDetails, true); // Decode JSON string into an associative array
    } else {
        echo "Order id not found";
    }

} else {
    header('Location:../allUserLogin.php');
    exit();
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Order Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .order-summary {
            margin-bottom: 20px;
        }

        .order-summary h2 {
            color: #555;
        }

        .customer-info,
        .order-items,
        .totals,
        .status,
        .actions {
            margin-bottom: 20px;
        }

        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-items th,
        .order-items td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .order-items th {
            background-color: #f9f9f9;
        }

        .order-items img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        .totals {
            text-align: right;
        }

        .totals p {
            margin: 0;
        }

        .notes {
            font-size: 0.9em;
            color: #777;
        }

        .actions label {
            display: block;
            margin-bottom: 5px;
        }

        .actions input[type="checkbox"] {
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php foreach ($orderDetailsDecode as $order) { 
             $imagePath = "../images/" . $product["image"];
             $imageSrc = (!empty($product['image']) && file_exists($imagePath) )? $imagePath : "../images/defaultProduct.png";
             
            ?>
            <h1>New Order Request</h1>
            <div class="order-summary">
                <h2>Order Number: <?= $order['order_id']; ?></h2>
                <?php
                $orderDate = isset($order['created_at']) ? $order['created_at'] : ''; // Assuming this is your order date in 'Y-m-d H:i:s' format
                // Check if $orderDate is set and not empty
                if (isset($orderDate) && $orderDate !== '') {
                    // Format the order date and display it
                    $formattedOrderDate = date('F j, Y', strtotime($orderDate));
                } ?>
                <p>Order Date:<?= isset($formattedOrderDate) ? $formattedOrderDate : '' ?></p>
            </div>
            <div class="customer-info">
                <h3>Customer Information:</h3>
                <p><strong>Name:</strong><?= isset($order['full_name']) ? $order['full_name'] : ''; ?> <br>
                    <strong>Email:</strong> <?= isset($order['email']) ? $order['email'] : ''; ?> <br>
                    <strong>Phone:</strong> <?= isset($order['phone_number']) ? $order['phone_number'] : ''; ?> <br> <br>
                    <strong>Shipping Address:</strong> <br>

                    Postal code: <?= isset($order['postal_code']) ? $order['postal_code'] : ''; ?> <br>
                    State: <?= isset($order['state']) ? $order['state'] : ''; ?> <br>
                    Street: <?= isset($order['Street']) ? $order['Street'] : ''; ?> <br>
                    City: <?= isset($order['city']) ? $order['city'] : ''; ?> <br>
                    District: <?= $order['district']; ?>
                </p>

                </p>
            </div>
            <div class="order-items">
                <h3>Ordered Items:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Delivery Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>                    
                                <img src="<?= $imageSrc ?>" alt="<?= $product["name"] ?>">
                            </td>
                            <td><?= $order['name']; ?></td>
                            <td><?= $order['quantity']; ?></td>
                            <td><?= $order['price']; ?></td>
                            <td><?= $order['delivery_status']; ?></td>
                            <td><?= $order['total_amount']; ?></td>

                        </tr>

                    </tbody>
                </table>
            </div>
            <?php $shippingCharge = 60;
            $totalAmount = $shippingCharge + $order['total_amount']; ?>
            <div class="totals">
                <p><strong>Subtotal:</strong> <?= $order['total_amount']; ?></p>
                <p><strong>Shipping:</strong> <?= $shippingCharge; ?></p>
                <p><strong>Total:</strong> <?= $totalAmount; ?></p>
            </div>
            <div class="status">
                <p><strong>Order Status:</strong><?= $order['delivery_status'] ?></p>
            </div>
            <!-- <div class="actions">
                <h3>Actions Required:</h3>
                <label><input type="checkbox"> Verify Payment</label>
                <label><input type="checkbox"> Pack Items</label>
                <label><input type="checkbox"> Ship Order (Estimated Ship Date: May 19, 2024)</label>
                <label><input type="checkbox"> Update Tracking Information</label>
            </div> -->
            <!-- <div class="notes">
                <p>Preferred shipping method: Standard Shipping<br>
                    Customer requested gift wrapping</p>
            </div> -->
        <?php } ?>
    </div>
</body>

</html>