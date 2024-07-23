<?php
require_once "./category/CategoryManager.php";
session_start();
if (!empty($_SESSION["admin_id"])) {
    $message = isset($_GET["message"]) ? $_GET["message"] : null;
    $error = isset($_GET["error"]) ? $_GET["error"] : null;

    $categoryManager = new CategoryManager();
    $result = $categoryManager->list();
    $resultDecode = json_decode($result, true);
} else {
    header("Location:index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Admin Dashboard</title>
    <link rel="stylesheet" href="admindashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="category/categoryList.css">
    <link rel="stylesheet" href="../bdashboard.css">
    <style>
        .nav-items li {
            /* background-color: transparent; */
            box-shadow: none;
        }

        .addNew {
            display: flex;
            justify-content: end;
            align-items: center;
            margin-bottom: 10px;
            text-align: end;
        }

        .addNew a {
            display: flex;
            justify-content: space-evenly;
            text-decoration: none;
            color: white;
            background-color: green;
            border: solid 1px grey;
            padding: 0px 3px;
            padding-left: 4px;
            gap: 3px;
            height: 35px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
    </style>
</head>

<body>
    <div class="navContainer">
        <div class="hamburger" id="hamburger">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="mainNav" id="mainNav">
            <ul class="nav-items">
                <li class="nav-item active">
                    <a href=""><i class="fas fa-home active"></i> Dashboard</a>
                </li>
                <!-- <li class="nav-item">
                    <a href="productLists.php"><i class="fas fa-cubes"></i>Products</a>
                </li> -->
                <li class="nav-item">
                    <a href="buyersList.php"><i class="fas fa-users"></i> Buyers</a>

                </li>
                <li class="nav-item">
                    <a href="sellersList.php"><i class="fas fa-users"></i> Seller</a>
                </li>
                <!-- <li class="nav-item">
                    <a href=""><i class="fas fa-cog"></i> Settings</a>

                </li> -->
                <li class="nav-item">
                    <a href="logout.php"><i class="fa fa-sign-out"></i>Logout</a>
                </li>

            </ul>
        </div>
    </div>

    <div class="content">
        <!-- Display message or error if present -->
        <?php if ($message): ?>
            <div style="color: green;"><?php echo $message; ?></div>
        <?php elseif ($error): ?>
            <div style="color: red;"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="container">
            <h1>Category List</h1>
            <div class="addNew">
                <a class="addLink" href="category/addCategory.php">
                    <iconify-icon class="addIcon" icon="zondicons:add-outline"></iconify-icon>
                    <p>Add New</p>
                </a>
            </div>
            <?php foreach ($resultDecode as $category) { ?>
                <ul>
                    <li>
                        <?= $category["name"]; ?>
                        <div class="icons">
                            <a href="category/editCategory.php?cat_id=<?= $category["category_id"]; ?>"><iconify-icon
                                    class="edit-icon" icon="tabler:edit"></iconify-icon>
                            </a>
                            <a href="#" onclick="showConfirmation(<?= $category["category_id"] ?>); return false;">
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

    </div>

</body>
<script>
    document.getElementById("hamburger").addEventListener("click", function () {
        document.getElementById("mainNav").classList.toggle("show");
    });
    function showConfirmation(categoryId) {
        // Show the confirmation dialog
        document.getElementById("confirmationDialog").style.display = "flex";
        // Pass categoryId to deleteCategory function when Confirm button is clicked
        document.getElementById("confirmationDialog").setAttribute("data-category-id", categoryId);
    }

    function hideConfirmation() {
        // Hide the confirmation dialog
        document.getElementById("confirmationDialog").style.display = "none";
    }

    function deleteCategory() {
        // Get the category ID from the data attribute of the confirmation dialog
        var categoryId = document.getElementById("confirmationDialog").getAttribute("data-category-id");
        // Redirect to deleteCategory.php with category ID as parameter
        window.location = "category/deleteCategory.php?cat_id=" + categoryId;
    }
</script>

</html>