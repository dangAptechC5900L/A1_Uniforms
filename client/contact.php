<?php
require_once('../function.php');
global $conn;
initConnection();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    if (empty($email)) {
        $errors[] = "Email must be provided";
    }
    if (empty($username)) {
        $errors[] = "Username must be provided";
    }
    if (!preg_match("/^0\d{9}$/", $phone)) {
        $errors[] = "Phone number must start with 0 and be followed by 9 digits";
    }
    if (count($errors) == 0) {
        $query_string = "INSERT INTO contact (fullname, email, subject, phone_number, message ) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query_string);
        $stmt->bind_param('sssds', $username, $email, $subject, $phone, $message);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Contact Successfully');</script>";
        } else {
            echo "<script>alert('Contact Failed');</script>";
        }
    } else {
        $error_message = "";
        foreach ($errors as $error) {
            $error_message .= $error . "\\n";
        }
        echo "<script>alert('" . $error_message . "');</script>";
    }
}
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>A-1 uniforms - contact us</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.svg">


    <!-- CSS 
    ========================= -->


    <!-- Plugins CSS -->
    <link rel="stylesheet" href="../assets/css/plugins.css">

    <!-- Main Style CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <!--Offcanvas menu area start-->
    <div class="off_canvars_overlay">

    </div>
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
                                <button type="submit">Tìm kiếm</button>
                            </form>
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
                                        <li><a href="shop.html">shop</a></li>
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
    <header class="header_area header_cart_page">
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
                    </div>
                </div>
            </div>
        </div>
        <!--header middel end-->

        <!--header bottom satrt-->
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
                                            <li><a href="shop.php">Shop<i class="fa fa-angle-down"></i></a>
                                                <ul class="sub_menu pages">

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
    <div class="breadcrumbs_area other_bread">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="index.php">home</a></li>
                            <li>/</li>
                            <li>contact us</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->


    <!--contact area start-->
    <div class="contact_area">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="contact_message content">
                        <h3>contact us</h3>
                        <span>Headquarters:</span>
                        <ul>
                            <li><i class="fa fa-fax"></i> Address: <a href="#">285 Doi Can Street, Lieu Giai Ward, Ba Dinh
                                    District, Hanoi City</a></li>
                            <li><i class="fa fa-envelope-o"></i>Email: <a href="mailto:a1uniforms@gmail.com">a1uniforms@gmail.com</a></li>
                            <li><i class="fa fa-phone"></i>Phone: <a href="tel:0(1234) 567 890">0(1234) 567 890</a></li>
                        </ul>
                    </div>
                    <div class="contact_area-detail">
                        <h3>Brands</h3>
                        <div class="branch">
                            <div class="branch-img">
                                <img src="./assets/img/product/product10.jpg" alt="">
                            </div>
                            <div class="branch-body">
                                <div class="branch-label">
                                    <h3 class="branch-label__heading">family A-1 Uniforms new york</h3>
                                    <div class="branch-label__desription">description</div>
                                    <div class="branch-label__desription-detail">
                                        Established in 2016, family A-1 Uniforms new york has been serving the
                                        community.
                                    </div>
                                    <div class="branch-label__phone">
                                        <i class="fa fa-phone"></i>
                                        <a href="tel:0(1234) 567 890">Phone: 0(1234) 567 890</a>
                                    </div>
                                    <div class="branch-label__email">
                                        <i class="fa fa-envelope-o"></i>
                                        <a href="mailto:a1uniforms@gmail.com">Email: a1uniforms@gmail.com</a>
                                    </div>
                                </div>
                                <div class="branch-location">
                                    <button class="branch-location__btn">
                                        location
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="branch">
                            <div class="branch-img">
                                <img src="./assets/img/product/product10.jpg" alt="">
                            </div>
                            <div class="branch-body">
                                <div class="branch-label">
                                    <h3 class="branch-label__heading">new york A-1 Uniforms office</h3>
                                    <div class="branch-label__desription">description</div>
                                    <div class="branch-label__desription-detail">
                                        Established in 2016, family A-1 Uniforms new york has been serving the
                                        community.
                                    </div>
                                    <div class="branch-label__phone">
                                        <i class="fa fa-phone"></i>
                                        <a href="tel:0(1234) 567 890">Phone: 0(1234) 567 890</a>
                                    </div>
                                    <div class="branch-label__email">
                                        <i class="fa fa-envelope-o"></i>
                                        <a href="mailto:a1uniforms@gmail.com">Email: a1uniforms@gmail.com</a>
                                    </div>
                                </div>
                                <div class="branch-location">
                                    <button class="branch-location__btn">
                                        location
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="contact_message form">
                        <h3>Contact A-1 Uniforms</h3>
                        <form action="contact.php" method="post">
                            <p>
                                <label> Your Username (required)</label>
                                <input name="username" placeholder="Username *" type="text" required>
                            </p>
                            <p>
                                <label> Your Email (required)</label>
                                <input name="email" placeholder="Email *" type="email" required>
                            </p>
                            <p>
                                <label> Subject</label>
                                <input name="subject" placeholder="Subject *" type="text">
                            </p>
                            <p>
                                <label> Phone</label>
                                <input name="phone" placeholder="Phone *" type="tel" pattern="0\d{9}" title="Phone number must start with 0 and be followed by 9 digits">
                            </p>
                            <div class="contact_textarea">
                                <label> Your Message</label>
                                <textarea placeholder="Message *" name="message" class="form-control2"></textarea>
                            </div>
                            <button type="submit" name="submit"> Send</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--contact area end-->

    <!--contact map start-->
    <div class="contact_map">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="map-area">
                        <iframe id="googleMap" style="border: none;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2633.2064646661916!2d105.81707328756478!3d21.036034998009033!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab0d6e603741%3A0x208a848932ac2109!2sAptech%20Computer%20Education!5e0!3m2!1sen!2s!4v1711182447002!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--contact map end-->


    <!--footer area start-->
    <footer class="footer_widgets other_widgets">
        <div class="footer_top">
            <div class="container">
                <div class="footer_top_inner">
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
                                            <p>Field: Fashion, Accessories, Uniforms. A-1 Uniforms builds and develops
                                                products that bring value to the community.</p>
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
                                    <p>Address: <a href="#">Address: 285 Doi Can Street, Lieu Giai Ward, Ba Dinh
                                            District, Hanoi City</a></p>
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
    <!--footer area end-->

    <!-- JS
============================================ -->
    <!-- Plugins JS -->
    <!-- <script src="assets/js/plugins.js"></script> -->

    <!-- Main JS -->
    <!-- <script src="assets/js/main.js"></script> -->



</body>

</html>