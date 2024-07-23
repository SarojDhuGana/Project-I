<?php
require_once ('SellerManager.php');

if (!empty($_SESSION['seller_id'])) {
    $seller_id = $_SESSION['seller_id'];
    $sellerManager = new SellerManager();

    $sellerDetails = $sellerManager->sellerDetails($seller_id);
    $sellerDetailsDecode = json_decode($sellerDetails, true);
} else {
    header('Location:../allUserLogin.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .profile-container {
            width: 400px;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

        }

        .profile-field {
            margin-bottom: 15px;
        }

        .profile-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-detail {
            margin-left: 10px;
            color: #333;
        }

        .edit-profile-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none
        }

        .edit-profile-button:hover {
            background-color: #0056b3;
            /* darker blue color on hover */
        }
    </style>
</head>


<body>
    <div class="profile-container">
        <h2>Profile Details</h2>
        <?php
        if (!empty($sellerDetailsDecode)) {
            foreach ($sellerDetailsDecode as $seller) { ?>
                <div class="profile-field">
                    <label for="full-name">Name:</label>
                    <span class="profile-detail"> <?= $seller['full_name'] ?></span>
                </div>
                <div class="profile-field">
                    <label for="email">Email:</label>
                    <span class="profile-detail"><?= $seller['email'] ?></span>
                </div>
                <div class="profile-field">
                    <label for="phone-number">Phone Number:</label>
                    <span class="profile-detail"><?= $seller['phone_number'] ?></span>
                </div>
                <div class="profile-field">
                    <label for="verification-status">Verification Status:</label>
                    <span class="profile-detail"
                        style="color: #28a745;"><?= isset($seller['is_verified']) ? 'Verified' : 'Not verified' ?></span>
                </div>
                <div class="profile-field">
                    <label for="company-name">Company Name:</label>
                    <span class="profile-detail"><?= $seller['company_name'] ?></span>
                </div>
                <div class="profile-field">
                    <label for="company-address">Company Address:</label>
                    <span
                        class="profile-detail"><?= isset($seller['company_address']) ? $seller['company_address'] : '' ?></span>
                </div>
                <button class="edit-profile-button"><a href="sellerProfileEdit.php" class="edit-profile-button">Edit
                        Profile</a></button>
            </div>
        <?php }
        } ?>
</body>

</html>