<?php
require ('fpdf185/fpdf.php');
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
        // echo $orderDetailsDecode[0]['seller_id'];
        $sellerManager = new SellerManager();
        $sellerDetails = $sellerManager->sellerDetails($orderDetailsDecode[0]['seller_id']);
        $sellerDetailsDecode = json_decode($sellerDetails, true);

        if (!empty($orderDetailsDecode) && !empty($sellerDetailsDecode)) {
            class PDF extends FPDF
            {
                function Header()
                {
                    $this->SetFont('Arial', 'B', 12);
                    $this->Cell(0, 10, 'Order Details', 0, 1, 'C');
                    $this->Ln(10);
                }

                function Footer()
                {
                    $this->SetY(-15);
                    $this->SetFont('Arial', 'I', 8);
                    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
                }

                function OrderDetails($order, $sellerDetailsDecode)
                {
                    $this->SetFont('Arial', '', 12);
                    $this->Cell(0, 10, 'Order Number: ' . $order['order_id'], 0, 1);
                    $this->Cell(0, 10, 'Order Date: ' . date('F j, Y', strtotime($order['created_at'])), 0, 1);
                    $this->Ln(10);
                    $this->Cell(0, 10, 'Shipping Address:', 0, 1);
                    $this->MultiCell(0, 10, $order['full_name'] . "\nPhone: " . $order['phone_number'] . "\nPostal code: " . $order['postal_code'] . "\nState: " . $order['state'] . "\nStreet: " . $order['Street'] . "\nCity: " . $order['city'] . "\nDistrict: " . $order['district']);
                    $this->Ln(10);

                    $this->Cell(0, 10, 'Ordered Items:', 0, 1);
                    $this->Cell(30, 10, 'Product Name', 1);
                    $this->Cell(30, 10, 'Quantity', 1);
                    $this->Cell(30, 10, 'Price', 1);
                    $this->Cell(30, 10, 'Total', 1);
                    $this->Ln();

                    $this->Cell(30, 10, $order['name'], 1);
                    $this->Cell(30, 10, $order['quantity'], 1);
                    $this->Cell(30, 10, $order['price'], 1);
                    $this->Cell(30, 10, $order['total_amount'], 1);
                    $this->Ln(20);

                    $shippingCharge = 60;
                    $totalAmount = $shippingCharge + $order['total_amount'];

                    $this->Cell(0, 10, 'Subtotal: Rs. ' . $order['total_amount'], 0, 1, 'R');
                    $this->Cell(0, 10, 'Shipping: Rs. ' . $shippingCharge, 0, 1, 'R');
                    $this->Cell(0, 10, 'Total: Rs. ' . $totalAmount, 0, 1, 'R');
                    $this->Ln(10);

                    $orderDate = $order['created_at'];
                    $date = new DateTime($orderDate);
                    $date->modify('+3 days');
                    $estimatedDeliveryDate = $date->format('F j, Y');

                    $this->Cell(0, 10, 'Order Status: ' . $order['delivery_status'] . ' (Estimated delivery: ' . $estimatedDeliveryDate . ')', 0, 1);
                    $this->Ln(10);

                    // $sellerEmail = isset($sellerDetailsDecode['email']) ? $sellerDetailsDecode['email'] : 'Email not found';
                    $this->MultiCell(0, 10, 'Thank you for shopping with us! If you have any questions about your order, please contact our support team at ' . $sellerDetailsDecode[0]['email']);
                }
            }

            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->OrderDetails($orderDetailsDecode[0], $sellerDetailsDecode);
            $pdf->Output('D', 'order_' . $orderDetailsDecode[0]['order_id'] . '.pdf');
        } else {
            echo "Order or seller details not found.";
        }
    } else {
        echo "Order not specified.";
    }
} else {
    header('Location: allUserLogin.php');
    exit();
}
?>