<?php
require_once (__DIR__ . '/AdminManager.php');

$adminManager = new AdminManager();
$result = $adminManager->sellerList();
$resultDecode = json_decode($result, true);

// for search 
$searchResult = [];
$searchName = '';
$searchDecode = null;

if (!empty($_GET['name'])) {
    $searchName = $_GET['name'];
    $searchResult = $adminManager->searchSellersName($searchName);
    $searchDecode = json_decode($searchResult, true);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sellers List</title>
    <link rel="stylesheet" href="buyersList.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="container">
        <div class="search-div">
            <button type="submit" style="background-color:green; margin-top:1px; color: white;"
                onclick="window.location.href='sellersList.php'"><i class="fa fa-refresh "
                    style="padding-right:7px"></i>Reset</button>

            <form action="" class="search-form" method="get">
                <input type="text" name="name" placeholder="Search by name"
                    value="<?= htmlspecialchars($searchName); ?>">
                <button type="submit" style="background-color:blue;"><i class="fa fa-search"
                        style="padding-right:7px"></i>Search</button>
            </form>
        </div>
        <div class="wrap">
            <?php
            if (isset($_SESSION['success_message'])) {
                echo "<p id='responseMessage' class='message success'>{$_SESSION['success_message']}</p>";
                unset($_SESSION['success_message']);
            } elseif (isset($_SESSION['error_message'])) {
                echo "<p id='responseMessage' class='message error'>{$_SESSION['error_message']}</p>";
                unset($_SESSION['error_message']);
            } else {
                echo "<p id='responseMessage' class='message hidden'></p>"; // Placeholder to maintain space
            }
            ?>
            <h2>Sellers List</h2>
            <form id="cartForm" action="deleteSellers.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th class="heading">
                                <label><input class="checkBox label-check" type="checkbox" id="selectAll">Select
                                    all</label>
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sellers = !empty($searchDecode) ? $searchDecode : $resultDecode;

                        // if (!empty($sellers) && isset($sellers['data'])) {
                        
                        if (isset($searchDecode['success']) && !$searchDecode['success']) {
                            echo "<tr><td colspan='4' style='text-align:center; color:red;'>" . htmlspecialchars($searchDecode['message']) . "</td></tr>";
                        } else {
                            foreach ($sellers['data'] as $key => $seller) { ?>
                                <tr>
                                    <td class="checkBox">
                                        <input class="checkBox" type="checkbox" name="user_ids[]"
                                            value="<?= htmlspecialchars($seller['seller_id']); ?>">
                                    </td>
                                    <td><?= htmlspecialchars($seller['full_name']); ?></td>
                                    <td><?= htmlspecialchars($seller['email']); ?></td>
                                    <td class="action">
                                        <a href="deleteSellers.php?user_id=<?= htmlspecialchars($seller['seller_id']); ?>"
                                            onclick="return confirm('Are you sure you want to delete this seller?');"><i
                                                style="background:none; padding-right:3px; color:red;"
                                                class="fa fa-trash-o"></i>Delete</a>
                                    </td>
                                </tr>
                            <?php }
                        }
                        ?>
                    </tbody>
                </table>
                <button type="submit"><i class="fa fa-trash-o"
                        style="background:none; padding-right:3px; color:white;"></i>Delete</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('selectAll').addEventListener('change', function () {
            var checkboxes = document.querySelectorAll('.checkBox');
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
            }
        });
    </script>
</body>

</html>