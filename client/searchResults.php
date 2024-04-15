<?php

include("../function.php");

// Khởi tạo kết nối đến cơ sở dữ liệu
$conn = initConnection();
$searchTerm = $_GET['searchTerm'];

// Gọi hàm searchProductByName để tìm kiếm sản phẩm dựa trên từ khóa tìm kiếm
$products = searchProductByName($conn, $searchTerm);
$categories=getCategoryByID($conn);

// Đóng kết nối
mysqli_close($conn);

function getCategoryByID($conn)
{
    $sql = "SELECT * FROM category";
    $result = $conn->query($sql);

    $categories = []; // Khởi tạo mảng chứa dữ liệu

    if ($result->num_rows > 0) {
        // Duyệt qua từng hàng kết quả và lưu vào mảng
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row; // Thêm dữ liệu của hàng vào mảng categories
        }
    }

    return $categories; // Trả về mảng categories
}



function searchProductByName($conn, $searchTerm)
{
    $sql = "SELECT * FROM product WHERE product_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $searchTerm . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();


    $products = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    $stmt->close();
    return $products;
}

?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>A-1 uniforms - home</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/plugins.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <!-- Main Wrapper Start -->
    <!--Offcanvas menu area start-->
    <div class="offcanvas_menu">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="canvas_open">
                        <a href="javascript:void(0)"><i class="ion-navicon"></i></a>
                    </div>
                    <div class="offcanvas_menu_wrapper">
                        <div class="canvas_close">
                            <a href="javascript:void(0)"><i class="ion-android-close"></i></a>
                        </div>

                        <div class="search_bar">
                            <!-- <form action="#">
                                <input placeholder="Search entire product here..." type="text">
                                <button type="submit"><i class="ion-ios-search-strong"></i></button>
                            </form> -->
                            <form method="GET" action="searchResults.php">
                                <input type="text" name="searchTerm" placeholder="Enter the product name...">
                                <button type="submit">Tìm kiếm</button>
                            </form>
                        </div>
                        <div class="cart_area">
                            <div class="middel_links">
                                <ul>
                                    <?php
                                    if (isset($_SESSION['customer_name'])) {
                                        echo '<li><i class="fa-solid fa-user"></i>  &nbsp;' . $_SESSION['customer_name'] . ' &nbsp; &nbsp;<a href="logout.php">Logout</a></li>';
                                    } else {
                                        echo '<li><a href="login.php">Login</a></li>';
                                        echo '<li>/</li>';
                                        echo '<li><a href="register.php">Register</a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="contact_phone">
                            <p>Call Free Support: <a href="tel:01234567890">01234567890</a></p>
                        </div>
                        <div id="menu" class="text-left ">
                            <ul class="offcanvas_main_menu">
                                <li class="menu-item-has-children active">
                                    <a href="index.php">Home</a>
                                </li>
                                <li class="menu-item-has-children">
                                <li><a>Shop<i class="fa fa-angle-down"></i></a>
                                    <ul class="sub_menu pages">
                                        <?php foreach ($categories as $category) : ?>
                                            <li><a href="productByCategory.php?category_id=<?php echo $category['category_id'] ?>"><?php echo $category['name'] ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="about.php">About Us</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="contact.php"> Contact Us</a>
                                </li>
                            </ul>
                        </div>
                        <div class="offcanvas_footer">
                            <span><a href="#"><i class="fa fa-envelope-o"></i> info@yourdomain.com</a></span>
                            <ul>
                                <li class="facebook"><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li class="twitter"><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li class="pinterest"><a href="#"><i class="fa fa-pinterest-p"></i></a></li>
                                <li class="google-plus"><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                <li class="linkedin"><a href="#"><i class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Offcanvas menu area end-->

    <!--header area start-->
    <header class="header_area header_shop">
        <!--header middel start-->
        <div class="header_middel">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-5">
                        <div class="logo">
                            <a href="index.php"><img src="../assets/img/logo/logo1.svg" alt=""></a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="search_bar">
                            <form method="GET" action="searchResults.php">
                                <input type="text" name="searchTerm" placeholder="Enter the product name...">
                                <button type="submit"><i class="ion-ios-search-strong"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 offset-md-6 offset-lg-0">
                        <div class="cart_area">
                            <div class="middel_links">
                                <ul>
                                    <?php
                                    if (isset($_SESSION['customer_name'])) {
                                        echo '<li><i class="fa-solid fa-user"></i>  &nbsp;' . $_SESSION['customer_name'] . ' &nbsp; &nbsp;<a href="logout.php">Logout</a></li>';
                                    } else {
                                        echo '<li><a href="login.php">Login</a></li>';
                                        echo '<li>/</li>';
                                        echo '<li><a href="register.php">Register</a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--header middel end-->

        <!--header bottom start-->
        <div class="header_bottom sticky-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="header_static">
                            <div class="main_menu_inner">
                                <div class="main_menu">
                                    <nav>
                                        <ul>
                                            <li><a href="index.php">Home</a>
                                            </li>
                                            <li><a>Shop<i class="fa fa-angle-down"></i></a>
                                                <ul class="sub_menu pages">
                                                    <?php foreach ($categories as $category) : ?>
                                                        <li><a href="productByCategory.php?category_id=<?php echo $category['category_id'] ?>"><?php echo $category['name'] ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </li>
                                            <li class="active"><a href="about.php">About us</a></li>
                                            <li><a href="contact.php">Contact Us</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                            <div class="contact_phone">
                                <p>Call Free Support: <a href="tel:01234567890">01234567890</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--header bottom end-->
    </header>
    <!--header area end-->

    <!--breadcrumbs area start-->
    <div class="breadcrumbs_area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="index.php">home</a></li>
                            <li>/</li>

                            <li>Search Results</li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

    <!--shop  area start-->
    <div class="shop_area shop_fullwidth shop_reverse">
        <div class="container">
            <div class="shop_inner_area">
                <div class="row">
                    <div class="col-12">

                        <div class="shop_toolbar_wrapper">
                            <div class="shop_toolbar_btn">

                                <button data-role="grid_3" type="button" class=" btn-grid-3" data-bs-toggle="tooltip" title="3"></button>

                                <button data-role="grid_4" type="button" class="active btn-grid-4" data-bs-toggle="tooltip" title="4"></button>

                                <button data-role="grid_5" type="button" class="btn-grid-5" data-bs-toggle="tooltip" title="5"></button>

                                <button data-role="grid_list" type="button" class="btn-list" data-bs-toggle="tooltip" title="List"></button>
                            </div>
                        </div>

                        <div class="row shop_wrapper">
                            <?php if (!empty($products)) : ?>
                                <?php foreach ($products as $product) : ?>
                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="single_product">
                                            <div class="product_thumb">
                                                <a class="primary_img" href="product-details.php?product_id=<?php echo $product['product_id']; ?>">
                                                    <img src="<?php echo $product['avata_product']; ?>" alt="Product Image">
                                                </a>
                                                <!-- <a class="secondary_img" href="product-details.html"><img src="assets/img/product/product16.jpg" alt=""></a> -->


                                            </div>

                                            <div class="product_content grid_content">
                                                <h3><a href="product-details.php?product_id=<?php echo $product['product_id']; ?> "><?php echo $product['product_name']; ?></a></h3>
                                                <span class="current_price">$<?php echo $product['price']; ?></span>
                                                <!-- <span class="old_price"><?php echo $product['old_price']; ?></span> -->
                                            </div>


                                            <div class="product_content list_content">
                                                <h3><a href="product-details.php"><?php echo $product['product_name']; ?></a></h3>
                                                <div class="product_ratting">
                                                    <ul>
                                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                                    </ul>
                                                </div>
                                                <div class="product_price">
                                                    <span class="current_price"><?php echo $product['price']; ?></span>

                                                </div>
                                                <div class="product_desc">
                                                    <p><?php echo $product['description']; ?></p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <h3 style="color:red; text-align:center; align-items:center; font-size:30px; padding-top: 70px;">No Products Found</h3>
                            <?php endif; ?>
                        </div>

                        <!--shop toolbar end-->
                    </div>
                </div>


            </div>

        </div>
    </div>
    <!--shop  area end-->

    <!--footer area start-->
    <footer class="footer_widgets">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>Information</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li><a href="about.php">About Us</a></li>
                                    <li><a href="#">Delivery Information</a></li>
                                    <li><a href="privacy-policy.html">Privacy Policy</a></li>
                                    <li><a href="#">Terms & Conditions</a></li>
                                    <li><a href="contact.php">Contact Us</a></li>
                                    <li><a href="#">Returns</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>Extras</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li><a href="#">Brands</a></li>
                                    <li><a href="#">Gift Certificates</a></li>
                                    <li><a href="#">Affiliate</a></li>
                                    <li><a href="#">Specials</a></li>
                                    <li><a href="contact.php">Site Map</a></li>
                                    <li><a href="my-account.html">My Account</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="widgets_container contact_us">
                            <h3>Contact Us</h3>
                            <div class="footer_contact">
                                <p>Address:Your address goes here.</p>
                                <p>Phone: <a href="tel:01234567890">01234567890</a> </p>
                                <p>Email: demo@example.com</p>
                                <ul>
                                    <li><a href="#" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#" title="google-plus"><i class="fa fa-google-plus"></i></a></li>
                                    <li><a href="#" title="facebook"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#" title="youtube"><i class="fa fa-youtube"></i></a></li>
                                </ul>

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="widgets_container newsletter">
                            <h3>Join Our Newsletter Now</h3>
                            <div class="newleter-content">
                                <p>Exceptional quality. Ethical factories. Sign up to enjoy free U.S. shipping and
                                    returns on your first order.</p>
                                <div class="subscribe_form">
                                    <form id="mc-form" class="mc-form footer-newsletter">
                                        <input id="mc-email" type="email" autocomplete="off" placeholder="Enter you email address here..." />
                                        <button id="mc-submit">Subscribe !</button>
                                    </form>
                                    <!-- mailchimp-alerts Start -->
                                    <div class="mailchimp-alerts text-centre">
                                        <div class="mailchimp-submitting"></div><!-- mailchimp-submitting end -->
                                        <div class="mailchimp-success"></div><!-- mailchimp-success end -->
                                        <div class="mailchimp-error"></div><!-- mailchimp-error end -->
                                    </div><!-- mailchimp-alerts end -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <div class="container">
               <div class="row">
                    <div class="col-md-12">
                        <div class="copyright_area">
                            <p> &copy; 2024 <strong> A-1 Uniforms</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--footer area end-->


    <!-- JS
============================================ -->

    <!-- Plugins JS -->
    <script src="../assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>



</body>

</html>