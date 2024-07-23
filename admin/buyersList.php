<?php
require_once ( __DIR__ . '/AdminManager.php' );

$adminManager = new AdminManager();
$result = $adminManager->buyerList();
$resultDecode = json_decode( $result, true );

// for search
$searchResult = [];
$searchName = '';
$searchDecode = null;

if ( !empty( $_GET[ 'name' ] ) ) {
    $searchName = $_GET[ 'name' ];
    $searchResult = $adminManager->searchBuyersName( $searchName );
    $searchDecode = json_decode( $searchResult, true );
}
?>

<!DOCTYPE html>
<html lang = 'en'>

<head>
<meta charset = 'UTF-8'>
<meta name = 'viewport' content = 'width=device-width, initial-scale=1.0'>
<title>Buyers List</title>
<link rel = 'stylesheet' href = 'buyersList.css'>
<link rel = 'stylesheet' href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
</head>
<style>
.success {
    color: green;
}

.error {
    color: ;
}
</style>

<body>

<div class = 'container'>
<div class = 'search-div'>
<button type = 'submit' style = 'background:green; color:white; '
onclick = "window.location.href='buyersList.php'"><i class = 'fa fa-refresh'
style = 'background:none; padding-right:2px'></i>Reset</button>
<form action = '' class = 'search-form' method = 'get'>
<input type = 'text' name = 'name' placeholder = 'Search by name'
value = "<?= htmlspecialchars($searchName); ?>">
<button type = 'submit' style = 'background-color:blue;'><i class = 'fa fa-search'
style = 'background:none; padding-right:2px'></i>Search</button>
</form>
</div>

<div class = 'wrap'>
<?php
if ( isset( $_SESSION[ 'success_message' ] ) ) {
    echo "<p id='responseMessage' class='message success'>{$_SESSION['success_message']}</p>";
    unset( $_SESSION[ 'success_message' ] );
} elseif ( isset( $_SESSION[ 'error_message' ] ) ) {
    echo "<p id='responseMessage' class='message error'>{$_SESSION['error_message']}</p>";
    unset( $_SESSION[ 'error_message' ] );
} else {
    echo "<p id='responseMessage' class='message hidden'></p>";
    // Placeholder to maintain space
}
?>
<form id = 'userForm' action = 'deleteBuyers.php' method = 'post'>
<h2>Buyers list</h2><br>
<table>
<thead>
<tr>
<th class = 'heading'>
<label><input class = 'checkBox label-check' type = 'checkbox' id = 'selectAll'>Select
all</label>
</th>
<th>Name</th>
<th>Email</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php
$buyers = !empty( $searchDecode ) ? $searchDecode : $resultDecode;

if ( isset( $searchDecode[ 'success' ] ) && !$searchDecode[ 'success' ] ) {
    echo "<tr><td colspan='4' style='text-align:center; color:red;'>" . htmlspecialchars( $searchDecode[ 'message' ] ) . '</td></tr>';
} else {
    foreach ( $buyers as $key => $buyer ) {
        ?>
        <tr>
        <td class = 'checkBox'>
        <input class = 'checkBox' type = 'checkbox' name = 'user_ids[]'
        value = "<?= htmlspecialchars($buyer['buyer_id']); ?>">
        </td>
        <td>< ?= htmlspecialchars( $buyer[ 'full_name' ] );
        ?></td>
        <td>< ?= htmlspecialchars( $buyer[ 'email' ] );
        ?></td>
        <td class = 'action'>
        <a href = "deleteBuyers.php?user_id=<?= htmlspecialchars($buyer['buyer_id']); ?>"
        onclick = "return confirm('Are you sure you want to delete this buyer?');"><i
        style = 'background:none; padding-right:3px; color:red;'

        class = 'fa fa-trash-o'></i>Delete</a>
        </td>
        </tr>
        <?php }
    }
    ?>
    </tbody>
    </table>
    <button type = 'submit'><i class = 'fa fa-trash-o'
    style = 'background:none; padding-right:3px; color:white;'></i>Delete</button>
    </form>
    </div>
    </div>

    <script>
    document.getElementById( 'selectAll' ).addEventListener( 'change', function () {
        var checkboxes = document.querySelectorAll( '.checkBox' );
        for ( var i = 0; i < checkboxes.length; i++ ) {
            checkboxes[ i ].checked = this.checked;
        }
    }
);
</script>

</body>

</html>