<?php
require_once ('BuyerManager.php');
require_once ('order/Order.php');
session_start();
// echo $_SESSION['buyer_id'];
if (!empty($_SESSION['buyer_id'])) {
    $buyer_id = $_SESSION['buyer_id'];
    $orderManager = new Order();
    $orderResponse = $orderManager->getBuyerOrderList($buyer_id);
    $orderResponseDecode = json_decode($orderResponse, true);
    // $responseDecode = array_reverse($responseDecode);
    // print_r($orderResponseDecode); 
    if ($orderResponseDecode['success']) {
        $productData = $orderResponseDecode;
    } else {
        $error_message = $orderResponseDecode['message'];
    }
} else {
    // header('Location: ../allUserLogin.php');
    // exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="bdashboard.css">

    <style>
        body {
            /* font-family: Arial, sans-serif; */
            margin: 0;
            /* padding: 20px; */
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 20px;   
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .order-list {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .order-list th,
        .order-list td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .order-list th {
            background-color: #f9f9f9;
        }

        .order-list tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .order-list img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        .details-link {
            color: #007BFF;
            text-decoration: none;
        }

        .details-link:hover {
            text-decoration: underline;
        }
        .navbar{
            display: none;
        }
    </style>
</head>

<body>
    <?php require_once ('buyerNavigation.php'); ?>
    <div class="container">
        <h1>Buyer Orders List</h1>
        <table class="order-list">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <!-- <th>Payment Status</th> -->
                    <th>Delivery Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>

                <?php

                if (!empty($orderResponseDecode) && $orderResponseDecode['success'] == true) {
                    foreach ($orderResponseDecode['data'] as $order) {
                        $imagePath = "images/" . $order["image"];
                        $imageSrc = (!empty($order['image']) && file_exists($imagePath) )? $imagePath : "images/defaultProduct.png";
                        
                        ?>
                        <tr>
                            <td><?= isset($order['order_id']) ? $order['order_id'] : ''; ?></td>
                            <td><?= isset($order['created_at']) ? $order['created_at'] : ''; ?></td>
                            <td>
                            <img src="<?= $imageSrc ?>" alt="<?= $order["name"] ?>">

                            <td><?= isset($order['name']) ? $order['name'] : ''; ?></td>
                            <td><?= $order['quantity']; ?></td>
                            <td><?= $order['total_amount']; ?></td>

                         <!-- <td><?= $order['payment_status']; ?></td> -->
                            <td><?= $order['delivery_status']; ?></td>
                            <Pphptd><a class="details-link" href="buyerOrderDetails.php?order_id=<?= $order['order_id']; ?>">View
                                    Details</a></td>
                        </tr>
                    <?php }
                } else { ?>
                    <p><?php echo htmlspecialchars($orderResponseDecode['message']); ?></p>
                <?php } ?>

            </tbody>
        </table>
    </div>
    <script>
        document.getElementById('hamburger').addEventListener('click', function () {
            document.getElementById('mainNav').classList.toggle('show');
        });

    </script>
</body>

</html>