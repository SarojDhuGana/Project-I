<?php
require_once 'CategoryManager.php';
session_start();
if (!empty($_SESSION['admin_id'])) {

    $message = isset($_GET['message']) ? $_GET['message'] : null;
    $error = isset($_GET['error']) ? $_GET['error'] : null;

    $categoryManager = new CategoryManager();
    $result = $categoryManager->list();
    $resultDecode = json_decode($result, true);
} else {
    header('Location:../index.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category List</title>

    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background-color: #f2f2f2;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        li:hover {
            background-color: #e0e0e0;
        }

        a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            display: flex;
            align-items: center;
            /* Align icon and text horizontally */
        }

        .delete-icon {
            color: red;
            /* Set color to red */
            margin-left: 10px;
            /* Add some space between icon and text */
        }

        .container ul li {
            display: flex;
            justify-content: space-between;
        }

        .icons {
            display: flex;
            width: 100px;
            justify-content: space-evenly;
        }

        /* Custom confirmation dialog styles */
        .confirmation-dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .confirmation-dialog-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .confirmation-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .confirmation-buttons button {
            margin: 0 10px;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .confirmation-buttons button.confirm {
            background-color: #dc3545;
            color: white;
        }

        .confirmation-buttons button.cancel {
            background-color: #007bff;
            color: white;
        }


        /* styling for add new button */
        .addNew {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .addNew a {
            display: flex;
            justify-content: space-evenly;
            text-decoration: none;
            color: white;
            width: 110px;
            height: 35px;
            background-color: green;
            border: solid 1px grey;
            /* padding: 8px 16px; */
            border-radius: 5px;
            transition: background-color 0.3s ease;

        }

        .addNew a:hover {
            background-color: darkgreen;
            /* Change background color on hover */
        }

        .error {
            /* Add styling for error message container */
        }
    </style>
</head>

<body>
    <!-- Display message or error if present -->
    <?php if ($message): ?>
        <div style="color: green;"><?php echo $message; ?></div>
    <?php elseif ($error): ?>
        <div style="color: red;"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="container">
        <h1>Category List</h1>
        <div class="addNew">
            <a class="addLink" href="addCategory.php">
                <iconify-icon class="addIcon" icon="zondicons:add-outline"></iconify-icon>
                <p>Add New</p>
            </a>
        </div>
        <?php foreach ($resultDecode as $category) { ?>
            <ul>
                <li>
                    <?= $category['name']; ?>
                    <div class="icons">
                        <a href="editCategory.php?cat_id=<?= $category['category_id']; ?>"><iconify-icon class="edit-icon"
                                icon="tabler:edit"></iconify-icon>
                        </a>
                        <a href="#" onclick="showConfirmation(<?= $category['category_id'] ?>); return false;">
                            <iconify-icon class="delete-icon" icon="tdesign:delete"></iconify-icon>
                        </a>

                    </div>
                </li>
            </ul>
        <?php } ?>

        <!-- Custom confirmation dialog -->
        <div class="confirmation-dialog" id="confirmationDialog">
            <div class="confirmation-dialog-content">
                <p>Are you sure you want to delete this category?</p>
                <div class="confirmation-buttons">
                    <button class="confirm" onclick="deleteCategory()">Confirm</button>
                    <button class="cancel" onclick="hideConfirmation()">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showConfirmation(categoryId) {
            // Show the confirmation dialog
            document.getElementById('confirmationDialog').style.display = 'flex';
            // Pass categoryId to deleteCategory function when Confirm button is clicked
            document.getElementById('confirmationDialog').setAttribute('data-category-id', categoryId);
        }

        function hideConfirmation() {
            // Hide the confirmation dialog
            document.getElementById('confirmationDialog').style.display = 'none';
        }

        function deleteCategory() {
            // Get the category ID from the data attribute of the confirmation dialog
            var categoryId = document.getElementById('confirmationDialog').getAttribute('data-category-id');
            // Redirect to deleteCategory.php with category ID as parameter
            window.location = "deleteCategory.php?cat_id=" + categoryId;
        }
    </script>

</body>

</html>