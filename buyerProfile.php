<?php
require_once ('BuyerManager.php');

if (!empty($_SESSION['buyer_id'])) {
    $buyer_id = $_SESSION['buyer_id'];
    $buyerManager = new BuyerManager();
    $buyerDetails = $buyerManager->getBuyerDetails($buyer_id);
    $responseDecode = json_decode($buyerDetails, true);
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
    <title>Buyer Profile</title>
<script src="jsFunctions/hamburger.js"></script>

    <link rel="stylesheet" href="bdashboard.css">
    <style>
        .profile-container {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .profile-container-box {
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
        .navbar{
            display:none;
        }
    </style>
</head>

<body>
<?php require_once ('buyerNavigation.php'); ?>
    <div class="profile-container">
        <div class="profile-container-box">
        <h2>Profile Details</h2>
        <?php
        if (!empty($responseDecode)) {
            foreach ($responseDecode as $buyer) { ?>
                <div class="profile-field">
                    <label for="full-name">Name:</label>
                    <span class="profile-detail"> <?= $buyer['full_name'] ?></span>
                </div>
                <div class="profile-field">
                    <label for="email">Email:</label>
                    <span class="profile-detail"><?= $buyer['email'] ?></span>
                </div>
                <div class="profile-field">
                    <label for="phone-number">Phone Number:</label>
                    <span class="profile-detail"><?= $buyer['phone_number'] ?></span>
                </div>
                <div class="profile-field">
                    <label for="verification-status">Verification Status:</label>
                    <span class="profile-detail"
                        style="color: #28a745;"><?= isset($buyer['is_verified']) ? 'Verified' : 'Not verified' ?></span>
                </div>
                
                <button class="edit-profile-button"><a href="buyerProfileEdit.php" class="edit-profile-button">Edit
                        Profile</a></button>
            </div>
        <?php }
        } else { ?>
        <?= $responseDecode['message']; ?>
    <?php } ?>
    </div>
</body>

</html>