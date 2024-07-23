<?php
require_once 'CategoryManager.php';
session_start();
if (!empty($_SESSION['admin_id'])) {

    $categoryManager = new CategoryManager();
    $imageName = ""; // Initialize $imageName variable

    if (!empty($_GET['cat_id'])) {
        $cat_id = $_GET['cat_id'];
        $categoryDetail = $categoryManager->getCategoryDetail($cat_id);
        $categoryDecode = json_decode($categoryDetail, true);
    } else {
        header('Location: category/categoryList.php');
    }
} else {
    header('Location:../index.php');
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="addcategory.css">
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
            border: solid 1px grey;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .viewAll a:hover {
            background-color: darkblue;
            /* Change background color on hover */
        }

        .error {}

        /* Add any additional styles for form elements if needed */
    </style>
</head>

<body>
    <form action="updateCategory.php" method="post" enctype="multipart/form-data">
        <?php foreach ($categoryDecode as $category): ?>
            <div class="viewAll">
                <h2>Edit Category</h2>
                <a class="viewLink" href="categoryList.php">View all</a>
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
            <input type="hidden" name="cat_id" value="<?= $category['category_id']; ?>">
            <input type="text" name="name" value="<?= isset($category['name']) ? $category['name'] : ''; ?>"
                placeholder="Category Name">

            <label for="current_image">Current Image</label>
            <?php if (!empty($category['image'])): ?>
                <div>
                    <img src="../../categoryImage/<?= $category['image']; ?>" alt="<?= $category['name']; ?>"
                        style="max-width: 200px;">
                    <input type="hidden" name="current_image" value="<?= $category['image']; ?>">
                </div>
            <?php else: ?>
                <p>No image available</p>
            <?php endif; ?>

            <label for="image">Replace Image</label> <br>
            <input type="file" name="categoryImage" id="fileToUpload" tabindex="4">

            <!-- <input type="file" name="categoryImage" id="fileToUpload"> -->
        <?php endforeach ?>
        <input type="submit" value="Update Category">
    </form>

</body>

</html>