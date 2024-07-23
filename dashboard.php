<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/FastActive/1.0.1/FastActive.min.js">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/commonStyling.css">

    <title>SHOPYA</title>
    <style>

        /* ------------------------Css for main start---------------------- */
        .main-screen-container {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./image/ecommerce.gif');
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
        }

        .main-screen-sub-container {
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
        }

        .main-screen-sub-container .logo {
            width: 200px;
            border-radius: 50%;
            border-color: var(--prim1-color);
        }

        .main-screen-sub-container .heading {
            font-size: 80px;
            color: var(--primary-color);
        }

        .main-screen-sub-container p {
            color: var(--primary-color);
            font-size: 30px;
            line-gap-override: 7px;
        }

        .main-screen-sub-container a {
            padding-right: 5px;
            text-decoration: none;
            font-weight: 500;
            font-size: 34px;
            text-align: center;
            text-transform: capitalize;
        }

        .main-screen-sub-container button {
            border: none;
            outline: 0;
            padding: 15px 25px;
            display: inline-block;
            margin: 9px;
            color: var(--secondary-color);
            background-color: var(--primary-color);
            cursor: pointer;
        }

        .main-screen-sub-container i,
        button:hover {
            background-color: var(--sec1-color);
            color: white;
        }
    </style>

<body>
    <div class="main-screen-container">
        <div class="main-screen-sub-container">
            <img src="image/logo1.jpg" alt="" class="logo">
            <h1 class="heading">E-commerce</h1>
            <p>Enrich your shopping list wisely</p>
            <button>
                <a href="buyerLogin.php">Login <i class="fas fa-user-alt"></i></a>
            </button>
            <button>
                <a href="explore.php">Explore <i class="fas fa-angle-double-right"></i></a></button>
        </div>
    </div>
</body>

</html>