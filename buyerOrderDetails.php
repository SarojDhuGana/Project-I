<?php
require_once ('BuyerManager.php');
require_once ('order/Order.php');
require_once ('seller/SellerManager.php');

session_start();
if (!empty($_SESSION['buyer_id'])) {
    if (!empty($_GET['order_id'])) {
        $order_id = $_GET['order_id'];
        $buyer_id = $_SESSION['buyer_id'];


        $orderManager = new Order();
        $orderDetails = $orderManager->getBuyerOrderDetails($buyer_id, $order_id);
        $orderDetailsDecode = json_decode($orderDetails, true);
        // print_r($orderDetailsDecode);
        // exit();
        $sellerManager = new SellerManager();
    } else {
        echo "<script>
                alert('Order not found');
                window.location.href = '{$_SERVER['HTTP_REFERER']}';
              </script>";
        exit();
    }
} else {
    header('Location:allUserLogin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

        .order-details,
        .shipping-address,
        .order-items,
        .totals,
        .status {
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

        .produdctDetailsTable .quantity {
            /* text-align: center; */
            position: relative;
            left: 25px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Order Details</h1>
        <a href="OrderDownloadPDF.php?order_id=<?= $order_id; ?>"
            style="text-decoration:none; padding:10px; background-color:blue; color:white; border-radius:16px;"
            class="download-btn">
            <i class="fas fa-download"></i> Download as PDF
        </a>

        <!-- <a href="downloadOrderDetailsPDF.php?order_id=<?= $_GET['order_id']; ?>" class="download-btn">Download Order as -->

        <?php
        if (!empty($orderDetailsDecode)) {
            foreach ($orderDetailsDecode as $order) {
                $imagePath = "images/" . $product["image"];
                $imageSrc = (!empty($product['image']) && file_exists($imagePath) )? $imagePath : "images/defaultProduct.png";
                
                ?>
                <div class="order-summary">
                    <h2>Order Number: <?= isset($order['order_id']) ? $order['order_id'] : ''; ?></h2>
                    <?php
                    $orderDate = isset($order['created_at']) ? $order['created_at'] : ''; // Assuming this is your order date in 'Y-m-d H:i:s' format
                    // Check if $orderDate is set and not empty
                    if (isset($orderDate) && $orderDate !== '') {
                        // Format the order date and display it
                        $formattedOrderDate = date('F j, Y', strtotime($orderDate));
                    } ?>

                    <p>Order Date: <?= isset($formattedOrderDate) ? $formattedOrderDate : '' ?></p>
                </div>
                <div class="shipping-address">
                    <h3>Shipping Address:</h3>
                    <p><?= $order['full_name']; ?><br>

                        Phone: <?= isset($order['phone_number']) ? $order['phone_number'] : ''; ?> <br>
                        Postal code: <?= isset($order['postal_code']) ? $order['postal_code'] : ''; ?> <br>
                        State: <?= isset($order['state']) ? $order['state'] : ''; ?> <br>
                        Street: <?= isset($order['Street']) ? $order['Street'] : ''; ?> <br>
                        City: <?= isset($order['city']) ? $order['city'] : ''; ?> <br>

                        District: <?= $order['district']; ?> </p>
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
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="produdctDetailsTable">
                                <td class="produdctDetailsTable">
                                <img src="<?= $imageSrc ?>" alt="<?= $product["name"] ?>">
                                </td>
                                <td><?= $order['name']; ?></td>
                                <td class="quantity"><?= $order['quantity']; ?></td>
                                <td class="price"><?= $order['price']; ?></td>
                                <td class="total_amount"><?= $order['total_amount']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php $shippingCharge = 60;
                $totalAmount = $shippingCharge + $order['total_amount']; ?>
                <div class="totals">
                    <p><strong>Subtotal:</strong> Rs.<?= $order['total_amount']; ?></p>
                    <p><strong>Shipping:</strong> Rs.<?= $shippingCharge ?> </p>
                    <p><strong>Total:</strong> Rs. <?= $totalAmount ?></p>
                </div>

                <?php
                $orderDate = $order['created_at']; // Assuming this is your order date in 'Y-m-d H:i:s' format
                // Create a DateTime object from the order date
                $date = new DateTime($orderDate);
                // Add 3 days to the order date
                $date->modify('+3 days');

                // Format the new date to display only the date part
                $estimatedDeliveryDate = $date->format('Y-m-d');


                // date('F j, Y', strtotime($order['created_at']));
                // date('F j, Y', strtotime($estimatedDeliveryDate));
                ?>

                <div class="status">
                    <p><strong>Order Status:</strong> <?= $order['delivery_status']; ?> (Estimated delivery: <?= date('F j, Y', strtotime($estimatedDeliveryDate));
                       ?>)</p>
                    <!-- <p><strong>Tracking Number:</strong> 123456789</p> -->
                </div>
                <div class="notes">
                    <?php
                    $sellerDetails = $sellerManager->sellerDetails($order['seller_id']);
                    $sellerDetailsDecode = json_decode($sellerDetails, true);

                    ?>
                    <p>Thank you for shopping with us! If you have any questions about your order, please contact our support
                        team at support <?= $sellerDetailsDecode[0]['email']; ?>
                    </p>
                </div>
            <?php }
        } else { ?>
            <?php echo "order not found"; ?>
        <?php } ?>
    </div>
</body>

</html>