<?php
require_once ('SellerManager.php');

if (!empty($_SESSION['seller_id'])) {
    $seller_id = $_SESSION['seller_id'];
    $sellerManager = new SellerManager();

    $sellerDetails = $sellerManager->sellerDetails($seller_id);
    $sellerDetailsDecode = json_decode($sellerDetails, true);

    // for save profile 

    if (!empty($_POST['seller_id']) && !empty($_POST['full_name'])) {
        $sellerManager = new SellerManager();
        $seller_id = $_POST['seller_id'];
        $full_name = $_POST['full_name'];
        $updateName = $sellerManager->updateSellerName($seller_id, $full_name);
        $responseDecode = json_decode($updateName, true);


    }
    if (!empty($_POST['seller_id']) && !empty($_POST['email'])) {
        $sellerManager = new SellerManager();
        $seller_id = $_POST['seller_id'];
        $email = $_POST['email'];
        $updateEmail = $sellerManager->updateSellerEmail($seller_id, $email);
        $responseDecode = json_decode($updateEmail, true);


    }
    if (!empty($_POST['buyer_id']) && !empty($_POST['phone_number'])) {
        $sellerManager = new BuyerManager();
        $seller_id = $_POST['seller_id'];
        $phone_number = $_POST['phone_number'];
        $updatePhone = $sellerManager->updateBuyerPhone($seller_id, $phone_number);
        $responseDecode = json_decode($updatePhone, true);

    }

} else {
    header('Location:../allUserLogin.php');
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Edit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

        .edit-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 5px 10px;
            margin-left: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-button:hover {
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

        .save-button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .save-button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <?php require_once ('../successMessage.php') ?>

    <div class="profile-container">
        <?php if (!empty($sellerDetailsDecode)): ?>
            <?php foreach ($sellerDetailsDecode as $seller): ?>
                <div class="profile-field">
                    <label for="full-name">Full Name <button class="edit-button"
                            onclick="openModal('full_name', '<?= $seller['full_name'] ?>')">Edit</button> </label>
                    <input type="text" id="full-name" name="full_name" value="<?= $seller['full_name'] ?>"
                        placeholder="Enter your full name" disabled>
                </div>
                <div class="profile-field">
                    <label for="email">Email <button class="edit-button"
                            onclick="openModal('email', '<?= $seller['email'] ?>')">Edit</button>
                    </label>
                    <input type="email" id="email" name="email" value="<?= $seller['email'] ?>" placeholder="Enter your email" disabled>
                </div>
                <div class="profile-field">
                    <label for="phone-number">Phone Number <button class="edit-button"
                            onclick="openModal('phone_number', '<?= isset($seller['phone_number']) ? $seller['phone_number'] : '' ?>')">Edit</button>
                    </label>
                    <input type="tel" id="phone-number" name="phone_number"
                        value="<?= isset($seller['phone_number']) ? $seller['phone_number'] : '' ?>"
                        placeholder="Enter your phone number" disabled>
                </div>
                <div class="profile-field">
                    <label for="verification-status">Verification Status</label>
                    <div class="verification-status <?= isset($seller['is_verified']) ? $seller['is_verified'] : ''; ?>">
                    </div>
                </div>
                <div class="profile-field">
                    <label for="company-name">Company Name
                    </label>
                    <input type="text" id="company_name" name="company_name" readonly
                        value="<?= isset($seller['company_name']) ? $seller['company_name'] : ''; ?>"
                        placeholder="Enter your company name">
                </div>
                <div class="profile-field">
                    <label for="company-address">Company Address
                    </label>
                    <input type="text" id="company_address" name="company_address" readonly
                        value="<?= isset($seller['company_address']) ? $seller['company_address'] : ''; ?>"
                        placeholder="Enter your company address">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form id="editForm" method="POST" action="" onsubmit="return handleSubmit(event)">
                <input type="hidden" id="seller_id" name="seller_id" value="<?= $seller_id ?>">
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
