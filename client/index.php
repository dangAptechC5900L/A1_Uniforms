<?php
session_start();
include('../function.php');
$conn = initConnection();

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

$categories=getCategoryByID($conn);

?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>A-1 uniforms - home</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.svg">
    <link rel="stylesheet" href="../assets/css/plugins.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>
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
                            <form method="GET" action="searchResults.php">
                                <input type="text" name="searchTerm" placeholder="Enter the product name...">
                                <button type="submit"><i class="ion-ios-search-strong"></i></button>
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
                                    <a href="shop.php">Shop</a>
                                    <ul class="sub-menu">
                                        <li><a href="productByCategory.php?category_id=1">Shirts</a></li>
                                        <li><a href="productByCategory.php?category_id=2">Skirts</a></li>
                                        <li><a href="productByCategory.php?category_id=3">Frocks </a></li>
                                        <li><a href="productByCategory.php?category_id=4"> P.T. T-shirts</a></li>
                                        <li><a href="productByCategory.php?category_id=5">P.T. shorts</a></li>
                                        <li><a href="productByCategory.php?category_id=6">P.T. track pants</a></li>
                                        <li><a href="productByCategory.php?category_id=7">Belts</a></li>
                                        <li><a href="productByCategory.php?category_id=8">Ties</a></li>
                                        <li><a href="productByCategory.php?category_id=9">Logos</a></li>
                                        <li><a href="productByCategory.php?category_id=10">Socks</a></li>
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
                            <span><a href="mailto:a1uniforms@gmail.com"><i class="fa fa-envelope-o"></i> &nbsp; a1uniforms@gmail.com</a></span>
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

    <header class="header_area">
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
                                                    <?php foreach($categories as $category) :?>
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

    </header>

    <div class="slider_area slider_style owl-carousel">
        <div class="single_slider" data-bgimg="../assets/img/slider/slide3.png">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="slider_content content_three">
                            <img src="../assets/img/slider/content2.png" alt="">
                            <p>the wooboom clothing summer collection is back at half price</p>
                            <a href="shop.php">Discover Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer_widgets">
        <div class="footer_top">
            <div class="container">
                <div class="row">

                    <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>Information</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li><a href="login.php">Login</a></li>
                                    <li><a href="register.php">Register</a></li>
                                    <li><a href="shop.php">Shop</a></li>
                                    <li><a href="about.php">About Us</a></li>
                                    <li><a href="contact.php">Contact Us</a></li>
                                    <li><a href="#">Returns</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>Extras</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li><a href="#">Brands</a></li>
                                    <li><a href="#">Gift Certificates</a></li>
                                    <li><a href="#">Affiliate</a></li>
                                    <li><a href="#">Specials</a></li>
                                    <li><a href="contact.php">Site Map</a></li>
                                    <li><a href="#">My Account</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container">
                            <h3>A-1 Uniforms Company</h3>
                            <div class="footer_menu">
                                <ul>
                                    <li>
                                        <p>Tax code: 010888888</p>
                                    </li>
                                    <li>
                                        <p>Establishment date: 04/03/2016</p>
                                    </li>
                                    <li>
                                        <p>Field: Fashion, Accessories, Uniforms. A-1 Uniforms builds and develops products that bring value to the community.</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                        <div class="widgets_container contact_us">
                            <h3>Contact Us</h3>
                            <div class="footer_contact">
                                <p>Phone: <a href="tel:0(1234) 567 890">0(1234) 567 890</a> </p>
                                <p>Email: <a href="mailto:a1uniforms@gmail.com">a1uniforms@gmail.com</a></p>
                                <p>Address: <a href="#">Address: 285 Doi Can Street, Lieu Giai Ward, Ba Dinh District, Hanoi City</a></p>
                                <ul>
                                    <li><a href="#" title="Twitter"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#" title="google-plus"><i class="fa fa-google-plus"></i></a></li>
                                    <li><a href="#" title="facebook"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#" title="youtube"><i class="fa fa-youtube"></i></a></li>
                                </ul>
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
                            <p> &copy; 2024 <strong> A-1 Uniforms </strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="../assets/js/plugins.js"></script>

    <script src="../assets/js/main.js"></script>



</body>

</html>