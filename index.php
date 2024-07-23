<?php
require_once ("product/ProductManager.php");
require_once ("admin/category/CategoryManager.php");
$categoryManager = new CategoryManager();
$result = $categoryManager->list();

$resultDecode = json_decode($result, true);

$productManager = new ProductManager();
$productList = $productManager->viewProductList();
$productDecode = json_decode($productList, true);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetShop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/FastActive/1.0.1/FastActive.min.js">
    <link rel="icon" type="image/x-icon" href="sysImage/favicon.ico">
    <!-- <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="bdashboard.css">
    <script src="jsFunctions/hamburger.js"></script>

</head>

<body>

    <!-------------------------------- header section start--------------------------->

    <?php require_once ("indexNavigation.php"); ?>
    <!-------------------------------header section end----------------------------------------->

    <div class="slideshow-container">
        <div class="mySlides fade">
            <img src="https://t3.ftcdn.net/jpg/04/65/46/52/360_F_465465254_1pN9MGrA831idD6zIBL7q8rnZZpUCQTy.jpg">
            <div class="text"><a href="#newArrivals" style="color:white; text-decoration: none;">Shop Now</a></div>

        </div>

        <div class="mySlides fade">
            <img
                src="https://static.vecteezy.com/system/resources/previews/004/564/672/non_2x/flat-big-sale-banner-vector.jpg">
            <div class="text"><a href="#newArrivals" style="color:white; text-decoration: none;">Shop Now</a></div>
        </div>

        <div class="mySlides fade">
            <img
                src="https://s.tmimgcdn.com/scr/1200x627/343000/sale-banner-set-promotion-with-the-yellow-background-and-super-offer-banner-design_343029-original.jpg">
            <div class="text"><a href="#newArrivals" style="color:white; text-decoration: none;">Shop Now</a></div>

        </div>

    </div>

    <div style="text-align:center">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    </div>
    <!-- Hero Section Product -->


    <section id="collection">
        <div class="collection-container">
            <h2>Categories</h2>
        </div>
        <div class="collections container">
            <?php
        // Limiting the number of categories displayed to 8
        $categoriesToShow = min(8, count($resultDecode));
        for ($i = 0; $i < $categoriesToShow; $i++) {
            $category = $resultDecode[$i];
            $categoryImagePath = "categoryImage/" . $category['image'];
            $catImageSrc =(!empty($category['image'])&&  file_exists($categoryImagePath)) ? $categoryImagePath : "categoryImage/defaultCategory.jpg";
        ?>
            <div class="content">
                <img src="<?= $catImageSrc; ?>" alt="<?= $category['name'] ?>" id="" />
                <div class="img-content">
                    <p><?= $category["name"]; ?></p>
                    <button>
                        <a href="product/categoryProduct.php?cat_id=<?= $category["category_id"]; ?>">View Product</a>
                    </button>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>


    <!-- New Arrival section -->
    <div class="seller newArrivals" id="newArrivals">
        <h2>New Arrivals</h2>
        <div class="wrap">
            <?php
            if (!empty($productDecode) && $productDecode["success"] == true) {
                foreach ($productDecode['data'] as $product) {
                    $imagePath = "images/" . $product["image"];
                    $imageSrc = (!empty($product['image']) && file_exists($imagePath) )? $imagePath : "images/defaultProduct.png";
                    ?>

            <div class="best-seller">
                <a href="product/product_details.php?product_id=<?= $product['product_id']; ?>">
                    <img src="<?= $imageSrc ?>" alt="<?= $product["name"] ?>">
                </a>
                <div class="best-p1">
                    <div class="name-of-p">
                        <p><?= $product["name"]; ?></p>
                    </div>
                    <div class="price">NPR. <?= $product["price"]; ?></div>

                    <div class="buttons">
                        <div class="buy-now">
                            <button><a href="order/checkout.php?product_id=<?= $product["product_id"]; ?>">Buy
                                    Now</a></button>
                        </div>
                        <div class="add-cart">
                            <button> <a href="cart/addCart.php?product_id=<?= $product["product_id"]; ?>">Add
                                    cart</a></button>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                }
            }
            ?>
        </div>
    </div>



    <!-- our providings -->
    <section id="news">
        <div class="news-heading">
            <p> Our Providing</p>
            <h2>Services</h2>
        </div>
        <div class="wrapNews">
            <div class="news1">
                <div class="image">
                    <img src="https://t3.ftcdn.net/jpg/05/11/05/96/240_F_511059607_ldslfJnryDDjbahXKm6KqlGadIjm7MEC.jpg"
                        alt="" class="service-img">
                </div>
                <div class="serviceContent">
                    <label for="">
                        <i class="bx bxs-calendar"></i> Cash On Delevery
                        <h4>With in 3 day</h4>
                    </label>
                </div>
            </div>

            <div class="news1">
                <div class="image">
                    <img src="https://t4.ftcdn.net/jpg/01/16/26/43/240_F_116264342_vxOHJAkvTENg57HwofBuRfn8hGpU8Iut.jpg" alt="" class="service-img">
                </div>
                <div class="serviceContent">
                    <label for="">
                        <i class="fa fa-car"></i>Free shipping
                        <h4>With in 3 days</h4>
                    </label>
                </div>
            </div>
            <!-- </div> -->
    </section>
    <!--------------------------------------------service section end -------------------------------------------------------->

    <!-------------------------------- Footer section------------------- -->
    <footer>
        <div class="footer-container">
            <div class="content_1">
                <img src="Images/Netshop.jpg" alt="logo">
                <p>The customer is at the heart of our<br>unique business model, which includes<br>design.</p>
            </div>
            <div class="content_2">
                <h4>SHOPPING</h4>
                <a href="#sellers">Clothing Store</a>
                <a href="#sellers">IT Prodcuts</a>
                <a href="#sellers">Accessories</a>
                <a href="#sellers"> </a>
            </div>
            <div class="content_3">
                <h4>About</h4>
                <a href="#">Contact Us</a>
                <a href="" target="_blank">Payment Method</a>
                <a href="" target="_blank">Delivery</a>
                <!-- <a href="https://codepen.io/sandeshbodake/full/Jexxrv" target="_blank">Return and Exchange</a> -->
            </div>

        </div>
        <div class="f-design">
            <div class="f-design-txt container">
                <p>Copyright &copy; 2024</p>
            </div>
        </div>
    </footer>


    <script>
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        let dots = document.getElementsByClassName("dot");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {
            slideIndex = 1
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " active";
        setTimeout(showSlides, 2000); // Change image every 2 seconds
    }
    </script>

</body>

</html>