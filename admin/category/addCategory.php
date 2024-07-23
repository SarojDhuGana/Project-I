<?php

require_once 'CategoryManager.php';
session_start();
$categoryManager = new CategoryManager();
$imageName = ""; // Initialize $imageName variable

if (!empty($_SESSION['admin_id'])) {

    // Check if the form was submitted and the file was uploaded
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['categoryImage']) && $_FILES['categoryImage']['error'] === UPLOAD_ERR_NO_FILE) {
            // No image uploaded, proceed without saving the image
            // Handle other form fields and create product here
        } elseif (isset($_FILES['categoryImage'])) {
            // Image uploaded, proceed with saving the image
            $imageName = $_FILES['categoryImage']['name'];
            $temp = $_FILES['categoryImage']['tmp_name'];
            if (!move_uploaded_file($temp, '../../categoryImage/' . $imageName)) {
                echo json_encode(['success' => false, 'message' => 'Image upload failed']);
                exit; // Stop further execution
            }
            // Continue with processing the rest of the form data and create product
        }

        $name = $_POST['name'];
        $result = $categoryManager->createCategory($name, $imageName);
        $resultDecode = json_decode($result, true);
    }
} else {
    header('Location:../index.php');
    exit; // Stop further execution
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="addCategory.css">
    <style>
        .viewAll {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .viewAll a {
            text-decoration: none;
            color: white;
            background-color: blue;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            border: solid 1px grey;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            width: 110px;
            height: 35px;
        }

        .viewAll a:hover {
            background-color: darkblue;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="viewAll">
            <h2>Add Category</h2>
            <div class="linkAndIcon">
                <a class="viewLink" href="categoryList.php">
                    <iconify-icon class="viewIcon" class="icon" icon="grommet-icons:view"></iconify-icon>
                    <p>View all</p>
                </a>
            </div>
        </div>
        <div class="error">
            <?php if (!empty($resultDecode) && isset($resultDecode['message'])): ?>
                <?php if ($resultDecode['success'] == false): ?>
                    <div style="color: red;">
                        <?= $resultDecode['message'] ?>
                    </div>
                <?php else: ?>
                    <div style="color: green;">
                        <?= $resultDecode['message'] ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <input type="text" name="name" placeholder="Category Name" required>
        <input type="file" name="categoryImage" id="fileToUpload">
        <input type="submit" value="Add Category">
    </form>
</body>

</html>