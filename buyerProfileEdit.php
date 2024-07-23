<?php
require_once ('BuyerManager.php');
session_start();

if (!empty($_SESSION['buyer_id'])) {
    $buyer_id = $_SESSION['buyer_id'];
    $buyerManager = new BuyerManager();
    $buyerDetails = $buyerManager->getBuyerDetails($buyer_id);
    $buyerDetailsDecode = json_decode($buyerDetails, true);
} else {
    header('Location:allUserLogin.php');
}

// for save profile 
// if (!empty($_POST)) {
//     $buyer_id = $_SESSION['buyer_id'];
//     $full_name = $_POST['full_name'];
//     $email = $_POST['email'];
//     $phone_number = $_POST['phone_number'];
//     $buyerManager = new BuyerManager();
//     $buyerResponse = $buyerManager->updateBuyerDetails($buyer_id, $full_name, $email, $phone_number);
//     $responseDecode = json_decode($buyerResponse, true);
// }

// if (!empty($_POST['full_name']) && $_POST['buyer_id']) {
//     $buyerManager = new BuyerManager();
//     $buyer_id = $_POST['buyer_id'];
//     $full_name = $_POST['full_name'];
//     print_r($_POST);

//     // $buyerResponse = $buyerManager->updateBuyerName($buyer_id, $full_name);
//     // $responseDecode = json_decode($buyerResponse, true);
// }

if (!empty($_POST['buyer_id']) && !empty($_POST['full_name'])) {
    $buyerManager = new BuyerManager();
    $buyer_id = $_POST['buyer_id'];
    $full_name = $_POST['full_name'];
    $updateName = $buyerManager->updateBuyerName($buyer_id, $full_name);
    $responseDecode = json_decode($updateName, true);


}
if (!empty($_POST['buyer_id']) && !empty($_POST['email'])) {
    $buyerManager = new BuyerManager();
    $buyer_id = $_POST['buyer_id'];
    $email = $_POST['email'];
    $updateEmail = $buyerManager->updateBuyerEmail($buyer_id, $email);
    $responseDecode = json_decode($updateEmail, true);


}
if (!empty($_POST['buyer_id']) && !empty($_POST['phone_number'])) {
    $buyerManager = new BuyerManager();
    $buyer_id = $_POST['buyer_id'];
    $phone_number = $_POST['phone_number'];
    $updatePhone = $buyerManager->updateBuyerPhone($buyer_id, $phone_number);
    $responseDecode = json_decode($updatePhone, true);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Profile Edit</title>
    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
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

        .profile-field input[type="text"],
        .profile-field input[type="email"],
        .profile-field input[type="tel"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        .profile-field input[type="text"]:focus,
        .profile-field input[type="email"]:focus,
        .profile-field input[type="tel"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .profile-field .verification-status {
            color: #28a745;
            /* green color */
        }

        .profile-field .verification-status.unverified {
            color: #dc3545;
            /* red color */
        }

        .profile-field .verification-status:before {
            content: "Verified";
        }

        .profile-field .verification-status.unverified:before {
            content: "Unverified";
        }

        .save-button {
            background-color: #007bff;
            /* blue color */
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .save-button:hover {
            background-color: #0056b3;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
        }

        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        /* styling for edit buttons */
        .edit-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php require_once ('successMessage.php') ?>

    <div class="profile-container">
        <?php if (!empty($buyerDetailsDecode)): ?>
            <?php foreach ($buyerDetailsDecode as $buyer): ?>
                <div class="profile-field">
                    <label for="full-name">Full Name
                        <button class="edit-button" onclick="openModal('full_name', '<?= $buyer['full_name'] ?>')">Edit</button>
                    </label>
                    <input type="text" id="full-name" name="full_name" value="<?= $buyer['full_name'] ?>"
                        placeholder="Enter your full name" disabled>
                </div>

                <div class="profile-field">
                    <label for="email">Email
                        <button class="edit-button" onclick="openModal('email', '<?= $buyer['email'] ?>')">Edit</button>
                    </label>
                    <input type="email" id="email" name="email" value="<?= $buyer['email'] ?>" placeholder="Enter your email"
                        disabled>
                </div>

                <div class="profile-field">
                    <label for="phone-number">Phone Number
                        <button class="edit-button"
                            onclick="openModal('phone_number', '<?= isset($buyer['phone_number']) ? $buyer['phone_number'] : '' ?>')">Edit</button>
                    </label>
                    <input type="tel" id="phone-number" name="phone_number"
                        value="<?= isset($buyer['phone_number']) ? $buyer['phone_number'] : '' ?>"
                        placeholder="Enter your phone number" disabled>
                </div>

                <div class="profile-field">
                    <label for="verification-status">Verification Status</label>
                    <div class="verification-status <?= isset($buyer['is_verified']) ? $buyer['is_verified'] : ''; ?>">
                        <!-- <?= isset($buyer['is_verified']) ? 'Verified' : 'Not verified' ?> -->
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form id="editForm" method="POST" action="" onsubmit="return handleSubmit(event)">
                <input type="hidden" id="buyer_id" name="buyer_id" value="<?= $buyer_id ?>">
                <div class="profile-field">
                    <label id="modal-label" for="modal-input"></label>
                    <input type="text" id="modal-input" name="field_value" value="">
                </div>
                <button type="submit" class="save-button">Save</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(fieldName, fieldValue) {
            document.getElementById('modal-label').innerText = `Edit ${fieldName.replace('_', ' ')}`;
            document.getElementById('modal-input').value = fieldValue;
            document.getElementById('modal-input').name = fieldName; // Set the name attribute dynamically
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('editModal')) {
                closeModal();
            }
        }

        function validatePhoneNumber(phoneNumber) {
            const phonePattern = /^\d{10}$/;
            return phonePattern.test(phoneNumber);
        }

        function handleSubmit(event) {
            const inputField = document.getElementById('modal-input');
            if (inputField.name === 'phone_number' && !validatePhoneNumber(inputField.value)) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Phone Number',
                    text: 'Phone number must be exactly 10 digits.',
                });
                return false;
            }
            return true;
        }
    </script>
</body>

</html>