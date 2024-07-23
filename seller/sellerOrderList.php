<?php
require_once ('../order/Order.php');
session_start();

if (!empty($_SESSION['seller_id'])) {
    $seller_id = $_SESSION['seller_id'];
    $orderManager = new Order();
    $orderResponse = $orderManager->getSellerOrderList($seller_id);
    $orderResponseDecode = json_decode($orderResponse, true);

} else {
    header('Location: ../allUserLogin.php');
    exit();
}

//  for updating delivery status
if (!empty($_POST)) {
    $order_id = $_POST['order_id'];
    $delivery_status = $_POST['delivery_status'];
    $orderManager = new Order();
    $delivery_status = $orderManager->updateDeliveryStatus($order_id, $delivery_status);
    $delivery_status_decode = json_decode($delivery_status);
    echo "<script>
    window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';
  </script>";
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
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
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

        .order-list {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .order-list th,
        .order-list td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            white-space: nowrap;
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

        .editDeliveryStatus {
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            flex-wrap: wrap;
        }

        tbody,
        tr,
        td {
            height: 58px;
        }

        .edit-button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 5px;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        .dropdown {
            display: none;
            margin-left: 10px;
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .dropdown option {
            padding: 10px;
            background-color: #fff;
            color: #333;
        }

        .dropdown option:hover {
            background-color: #f1f1f1;
        }

        .dropdown.show {
            display: inline-block;
        }

        @media (max-width: 768px) {
            .editDeliveryStatus {
                flex-direction: column;
            }

            .order-list th,
            .order-list td {
                white-space: normal;
            }

            .edit-button {
                margin: 5px 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Orders List</h1>
        <table class="order-list">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Delivery Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($orderResponseDecode['data'])) {
                    foreach ($orderResponseDecode['data'] as $order) { 
                        $imagePath = "../images/" . $order["image"];
                        $imageSrc = (!empty($order['image']) && file_exists($imagePath) )? $imagePath : "../images/defaultProduct.png";
                       
                        ?>
                        <tr>
                            <td><?= isset($order['order_id']) ? $order['order_id'] : ''; ?></td>
                            <?php
                            $orderDate = isset($order['created_at']) ? $order['created_at'] : '';
                            if ($orderDate !== '') {
                                $formattedOrderDate = date('F j, Y', strtotime($orderDate));
                            }
                            ?>
                            <td><?= isset($formattedOrderDate) ? $formattedOrderDate : '' ?></td>
                            <td>                
                                    <img src="<?= $imageSrc ?>" alt="<?= $order["name"] ?>">
                            </td>
                            <td><?= isset($order['name']) ? $order['name'] : ''; ?></td>
                            <td><?= isset($order['quantity']) ? $order['quantity'] : ''; ?></td>
                            <td><?= isset($order['total_amount']) ? $order['total_amount'] : ''; ?></td>
                            <td class="editDeliveryStatus">
                                <?= isset($order['delivery_status']) ? $order['delivery_status'] : ''; ?>
                                <button class="edit-button" onclick="toggleDropdown(this)">Edit</button>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id"
                                        value="<?= isset($order['order_id']) ? $order['order_id'] : ''; ?>">
                                    <select class="dropdown" name="delivery_status" onchange="this.form.submit()">
                                        <option value="">Select</option>
                                        <option value="Pending" <?= isset($order['delivery_status']) && $order['delivery_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Cancelled" <?= isset($order['delivery_status']) && $order['delivery_status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        <option value="Shipping" <?= isset($order['delivery_status']) && $order['delivery_status'] == 'Shipping' ? 'selected' : ''; ?>>Shipping</option>
                                        <option value="Delivered" <?= isset($order['delivery_status']) && $order['delivery_status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    </select>
                                </form>
                            </td>
                            <td><a class="details-link"
                                    href="sellerOrderDetails.php?order_id=<?= isset($order['order_id']) ? $order['order_id'] : ''; ?>">View
                                    Details</a></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="8"><?= $orderResponseDecode['message']; ?> </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        function toggleDropdown(button) {
            var dropdown = button.nextElementSibling.querySelector('.dropdown');
            dropdown.classList.toggle("show");
        }
    </script>
</body>

</html>