<?php
include("function.php");

// Khởi tạo kết nối đến cơ sở dữ liệu
$conn = initConnection();

// Lấy giá trị category_id từ tham số truy vấn
$category_id = $_GET['category_id'];
$product_id = $_GET['product_id'];

// Lấy giá trị trang hiện tại từ tham số truy vấn (mặc định là trang 1 nếu không có tham số truy vấn)
$currentpage = isset($_GET['page']) ? $_GET['page'] : 1;

// Số sản phẩm hiển thị trên mỗi trang
$itemsPerPage = 8;

//gán giá trị trả về của hàm getCategoryName() vào biến $categoryName
$categoryName = getCategoryName($conn, $category_id);

$products = getProductById($conn, $product_id);

$feedbacks = getFeedbackProductById($conn, $product_id);

$totalFeedbacks = getTotalFeedback($conn, $product_id);

$avgFeedbacks = getAVGStarFeedback($conn, $product_id);

addFeedbackProduct($conn, $product_id, $category_id);

// Đóng kết nối
mysqli_close($conn);

function getProductById($conn, $product_id)
{
    $sql = "SELECT * FROM product WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $products;
}


function getCategoryName($conn, $category_id)
{
    $sql = "SELECT name from category WHERE category_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $categoryName = "";
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $categoryName = $row['name'];
    }
    $stmt->close();
    return $categoryName;
}

function addFeedbackProduct($conn, $product_id, $category_id)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // $name=$_POST['author'];

        $description = $_POST['comment'];
        $star_rating = $_POST['star_rating']; // Lấy giá trị số sao từ form
        $feedbackDate = date('Y-m-d H:i:s'); // Lấy thời gian hiện tại
        $customer_id = 1;

        if (empty($description) && empty($star_rating)) {
            echo 'Vui lòng nhập nội dung feedback và đánh giá sao!';
        }

        $sql = "INSERT INTO feedback (product_id, customer_id, description, star_rating, feedbackDate) VALUES(?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisis", $product_id, $customer_id, $description, $star_rating, $feedbackDate);
        if ($stmt->execute()) {
            echo 'Thêm feedback thành công';
            header('Location: product-details.php?category_id=' . $category_id . '&product_id=' . $product_id);
            exit;
        } else {
            echo 'Thêm feedback thất bại';
        }
        $stmt->close();
    }
}

function getFeedbackProductById($conn, $product_id)
{
    $sql = "SELECT f.*, c.username AS customer_name 
            FROM feedback f 
            INNER JOIN customer c ON f.customer_id = c.customer_id
            WHERE f.product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    return $feedbacks;
}

function getTotalFeedback($conn, $product_id)
{
    $sql = "SELECT COUNT(*) AS total from feedback WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $totalFeedbacks = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    return $totalFeedbacks;
}

function getAVGStarFeedback($conn, $product_id)
{
    $sql = "SELECT AVG(price) from product WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $avgFeedbacks = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    return $avgFeedbacks;
}

?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Reid - product details</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/favicon.svg">

    <!-- CSS 
    ========================= -->
    <style>
        input.star {
            display: none;
        }



        label.star {

            float: right;

            padding: 10px;

            font-size: 36px;

            color: #4A148C;

            transition: all .2s;

        }



        input.star:checked~label.star:before {

            content: '\f005';

            color: #FD4;

            transition: all .25s;

        }


        input.star-5:checked~label.star:before {

            color: #FE7;

            text-shadow: 0 0 20px #952;

        }



        input.star-1:checked~label.star:before {
            color: #F62;
        }



        label.star:hover {
            transform: rotate(-15deg) scale(1.3);
        }



        label.star:before {

            content: '\f006';

            font-family: FontAwesome;

        }
    </style>

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/css/plugins.css">

    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <!-- Main Wrapper Start -->
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
                       
                        <div class="cart_area">
                            <div class="middel_links">
                                <ul>
                                    <li><a href="login.html">Login</a></li>
                                    <li>/</li>
                                    <li><a href="login.html">Register</a></li>
                                </ul>

                            </div>
                           
                        </div>
                        <div class="contact_phone">
                            <p>Call Free Support: <a href="tel:01234567890">01234567890</a></p>
                        </div>
                        <div id="menu" class="text-left ">
                            <ul class="offcanvas_main_menu">
                                <li class="menu-item-has-children active">
                                    <a href="#">Home</a>

                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">Shop</a>
                                    <ul class="sub-menu">
                                        <li class="menu-item-has-children">
                                            <a href="#">Shop Layouts</a>
                                            <ul class="sub-menu">
                                                <li><a href="shop.html">shop</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=1">Shirts</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=2">Skirts</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=3">Frocks </a></li>
                                                <li><a href="shop-fullwidth.php?category_id=4"> P.T. T-shirts</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=5">P.T. shorts</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=6">P.T. track pants</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=7">Belts</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=8">Ties</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=9">Logos</a></li>
                                                <li><a href="shop-fullwidth.php?category_id=10">Socks</a></li>
                                            </ul>
                                        </li>
                                        <li class="menu-item-has-children">
                                            <a href="#">other Pages</a>
                                            <ul class="sub-menu">
                                                <li><a href="portfolio.html">portfolio</a></li>
                                                <li><a href="portfolio-details.html">portfolio details</a></li>
                                                <li><a href="cart.html">cart</a></li>
                                                <li><a href="checkout.html">Checkout</a></li>
                                                <li><a href="my-account.html">my account</a></li>
                                            </ul>
                                        </li>
                                        <li class="menu-item-has-children">
                                            <a href="#">Product Types</a>
                                            <ul class="sub-menu">
                                                <li><a href="product-details.html">product details</a></li>
                                                <li><a href="product-sidebar.html">product sidebar</a></li>
                                                <li><a href="product-grouped.html">product grouped</a></li>
                                                <li><a href="variable-product.html">product variable</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">blog</a>
                                    <ul class="sub-menu">
                                        <li><a href="blog.html">blog</a></li>
                                        <li><a href="blog-details.html">blog details</a></li>
                                        <li><a href="blog-sidebar.html">blog Sidebar</a></li>
                                        <li><a href="blog-fullwidth.html">blog fullwidth</a></li>
                                    </ul>

                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">pages </a>
                                    <ul class="sub-menu">
                                        <li><a href="about.html">About Us</a></li>
                                        <li><a href="services.html">services</a></li>
                                        <li><a href="faq.html">Frequently Questions</a></li>
                                        <li><a href="contact.html">contact</a></li>
                                        <li><a href="login.html">login</a></li>
                                        <li><a href="wishlist.html">Wishlist</a></li>
                                        <li><a href="404.html">Error 404</a></li>
                                        <li><a href="compare.html">compare</a></li>
                                        <li><a href="privacy-policy.html">privacy policy</a></li>
                                        <li><a href="coming-soon.html">coming soon</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="my-account.html">my account</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="about.html">About Us</a>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="contact.html"> Contact Us</a>
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
                            <a href="index.php"><img src="assets/img/logo/logo1.svg" alt=""></a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="search_bar">
                            <form method="GET" action="searchResults.php">
                                <input type="text" name="searchTerm" placeholder="Nhập tên sản phẩm...">
                                <button type="submit"><i class="ion-ios-search-strong"></i></button>
                            </form>
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
                                            <li><a href="shop-fullwidth.html">Shop<i class="fa fa-angle-down"></i></a>
                                                <ul class="sub_menu pages">
                                                    <li><a href="productByCategory.php?category_id=1">Shirts</a></li>
                                                    <li><a href="productByCategory.php?category_id=2">Skirts</a></li>
                                                    <li><a href="productByCategory.php?category_id=3">Frocks </a></li>
                                                    <li><a href="productByCategory.php?category_id=4">P.T. T-shirts</a></li>
                                                    <li><a href="productByCategory.php?category_id=5">P.T. shorts</a></li>
                                                    <li><a href="productByCategory.php?category_id=6">P.T. track pants</a></li>
                                                    <li><a href="productByCategory.php?category_id=7">Belts</a></li>
                                                    <li><a href="productByCategory.php?category_id=8">Ties</a></li>
                                                    <li><a href="productByCategory.php?category_id=9">Logos</a></li>
                                                    <li><a href="productByCategory.php?category_id=10">Socks</a></li>
                                                </ul>
                                            </li>
                                            <li class="active"><a href="about.html">About us</a></li>
                                            <li><a href="contact.html">Contact Us</a></li>
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
                            <li><a href="index.html">home</a></li>
                            <li>/</li>
                            <li><?php echo $categoryName; ?></li>
                            <li>/</li>
                            <li><a >product_details</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

        <!--product details start-->
        <?php foreach ($products as $product) : ?>
            <div class="product_details">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 col-md-5">
                            <div class="product-details-tab">

                                <div id="img-1" class="zoomWrapper single-zoom">
                                    <a>
                                        <img id="zoom1" src="<?php echo $product['img']; ?>" data-zoom-image="<?php echo $product['img']; ?>" alt="big-1">
                                    </a>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-7 col-md-7">
                            <?php foreach ($products as $product) : ?>
                                <div class="product_d_right">
                                    <form action="#">

                                        <h1><?php echo $product['name']; ?></h1>
                                        <div class=" product_ratting">
                                            <ul>
                                                <?php
                                                // Tính tổng số sao trung bình
                                                $totalStars = 0;
                                                foreach ($feedbacks as $feedback) {
                                                    $totalStars += $feedback['star_rating'];
                                                }
                                                $averageStars = ($totalStars > 0 && count($feedbacks) > 0) ? $totalStars / count($feedbacks) : 0;

                                                // Hiển thị số sao trung bình
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $averageStars) {
                                                        echo '<li><a href="#"><i class="fa fa-star"></i></a></li>';
                                                    } else {
                                                        echo '<li><a href="#"><i class="fa fa-star-o"></i></a></li>';
                                                    }
                                                }
                                                ?>
                                                <?php foreach ($totalFeedbacks as $totalFeedback) : ?>
                                                    <li class="review"><a href="#"> <?php echo $totalFeedback['total']; ?> review </a></li>

                                                <?php endforeach; ?>
                                                <!-- <li class="review"><a href="#"> Write a review </a></li> -->
                                            </ul>
                                        </div>
                                        <div class="product_price">
                                            <span class="current_price">$<?php echo $product['price'] ?></span>
                                        </div>
                                        <div class="product_desc">
                                            <p><?php echo $product['description']; ?> </p>
                                            <!-- <p>More room to move. With 80GB or 160GB of storage and up to 40 hours of battery life, the new iPod classic lets you enjoy up to 40,000 songs or up to 200 hours of video or any combination wherever you go. Cover Flow. Browse through your music collection by flipping through album art. Select an album to turn it over and see the track list. Enhanced interface. Experience a whole new way to browse and view your music and video.
                                         Sleeker design. Beautiful, durable, and sleeker than ever, iPod classic now features an anodized aluminum and polish.. </p> -->
                                        </div>

                                        <div class="product_variant color">
                                            <h3>color</h3>
                                            <select class="niceselect_option" id="color" name="produc_color">
                                                <option selected value="1">choose in option</option>
                                                <option value="2">choose in option2</option>
                                                <option value="3">choose in option3</option>
                                                <option value="4">choose in option4</option>
                                            </select>
                                        </div>
                                        <div class="product_variant size">
                                            <h3>size</h3>
                                            <select class="niceselect_option" id="color1" name="produc_color">
                                                <option selected value="1">size</option>
                                                <option value="2">x</option>
                                                <option value="2">xl</option>
                                                <option value="3">md</option>
                                                <option value="4">xxl</option>
                                                <option value="4">s</option>
                                            </select>
                                        </div>
                                        <div class="product_variant quantity">
                                            <label>quantity</label>
                                            <input min="1" max="100" value="1" type="number">
                                            <button class="button" type="submit">add to cart</button>
                                        </div>


                                    </form>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <!--product details end-->

        <!--product info start-->
        <div class="product_d_info">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="product_d_inner">
                            <div class="product_info_button">
                                <ul class="nav" role="tablist">

                                    <li>
                                        <a data-bs-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Reviews</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="info" role="tabpanel">
                                    <div class="product_info_content">
                                        <p>Fashion has been creating well-designed collections since 2010. The brand offers feminine designs delivering stylish separates and statement dresses which have since evolved into a full ready-to-wear collection in which every item is a vital part of a woman's wardrobe. The result? Cool, easy, chic looks with youthful elegance and unmistakable signature style. All the beautiful pieces are made in Italy and manufactured with the greatest attention. Now Fashion extends to a range of accessories including shoes, hats, belts and more!</p>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="sheet" role="tabpanel">
                                    <div class="product_d_table">
                                        <form action="#">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="first_child">Compositions</td>
                                                        <td>Polyester</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="first_child">Styles</td>
                                                        <td>Girly</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="first_child">Properties</td>
                                                        <td>Short Dress</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                    <div class="product_info_content">
                                        <p>Fashion has been creating well-designed collections since 2010. The brand offers feminine designs delivering stylish separates and statement dresses which have since evolved into a full ready-to-wear collection in which every item is a vital part of a woman's wardrobe. The result? Cool, easy, chic looks with youthful elegance and unmistakable signature style. All the beautiful pieces are made in Italy and manufactured with the greatest attention. Now Fashion extends to a range of accessories including shoes, hats, belts and more!</p>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="reviews" role="tabpanel">
                                    <div class="product_info_content">
                                        <p>Fashion has been creating well-designed collections since 2010. The brand offers feminine designs delivering stylish separates and statement dresses which have since evolved into a full ready-to-wear collection in which every item is a vital part of a woman's wardrobe. The result? Cool, easy, chic looks with youthful elegance and unmistakable signature style. All the beautiful pieces are made in Italy and manufactured with the greatest attention. Now Fashion extends to a range of accessories including shoes, hats, belts and more!</p>
                                    </div>
                                    <?php foreach ($feedbacks as $feedback) : ?>
                                        <div class="product_info_inner">
                                            <div class="product_ratting mb-10">
                                                <ul>
                                                    <?php
                                                    // Số sao trong feedback
                                                    $star_rating = $feedback['star_rating'];

                                                    // Hiển thị số sao
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $star_rating) {
                                                            echo '<li><a href="#"><i class="fa fa-star"></i></a></li>';
                                                        } else {
                                                            echo '<li><a href="#"><i class="fa fa-star-o"></i></a></li>';
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                                <!-- <p><?php echo $feedback['customer_name'] ?></p> -->
                                                <p><?php echo $feedback['feedbackDate'] ?></p>
                                            </div>

                                            <div class="product_demo">
                                            <p><?php echo $feedback['customer_name'] ?></p>
                                                <p><?php echo $feedback['description'] ?></p>
                                            </div>

                                        </div>
                                    <?php endforeach; ?>

                                    <div class="product_review_form">
                                        <form action="product-details.php?category_id=<?php echo $category_id ?>&product_id=<?php echo $product_id ?>" method="post" onsubmit="return validateForm()">
                                            <h2>Add a review </h2>
                                            <p>Your email address will not be published. Required fields are marked </p>
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="review_comment">Your review </label>
                                                    <textarea name="comment" id="review_comment" required></textarea>
                                                    <label for="star_rating">Rating: </label>
                                                    <div id="star_rating">
                                                        <input class="star star-5" value="5" id="star-5" type="radio" name="star_rating" />

                                                        <label class="star star-5" for="star-5"></label>

                                                        <input class="star star-4" value="4" id="star-4" type="radio" name="star_rating" />

                                                        <label class="star star-4" for="star-4"></label>

                                                        <input class="star star-3" value="3" id="star-3" type="radio" name="star_rating" />

                                                        <label class="star star-3" for="star-3"></label>

                                                        <input class="star star-2" value="2" id="star-2" type="radio" name="star_rating" />

                                                        <label class="star star-2" for="star-2"></label>

                                                        <input class="star star-1" value="1" id="star-1" type="radio" name="star_rating" />

                                                        <label class="star star-1" for="star-1"></label>
                                                    </div>

                                                </div>
                                            </div>
                                            <button type="submit">Submit</button>
                                        </form>
                                        <?php if (!empty($message)) : ?>
                                            <p><?php echo $message; ?></p>
                                        <?php endif; ?>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--product info end-->

        <!--product section area start-->
        <!-- <section class="product_section related_product">
        <div class="container">
            <div class="row">   
                <div class="col-12">
                   <div class="section_title">
                       <h2>Related Products</h2>
                       <p>Contemporary, minimal and modern designs embody the Lavish Alice handwriting</p>
                   </div>
                </div> 
            </div>    
            <div class="product_area"> 
                 <div class="row">
                    <div class="product_carousel product_three_column4 owl-carousel">
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.html"><img src="assets/img/product/product21.jpg" alt=""></a>
                                    <a class="secondary_img" href="product-details.html"><img src="assets/img/product/product22.jpg" alt=""></a>
                                    <div class="product_action">
                                        <div class="hover_action">
                                           <a  href="#"><i class="fa fa-plus"></i></a>
                                            <div class="action_button">
                                                <ul>

                                                    <li><a title="add to cart" href="cart.html"><i class="fa fa-shopping-basket" aria-hidden="true"></i></a></li>
                                                    <li><a href="wishlist.html" title="Add to Wishlist"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>

                                                    <li><a href="compare.html" title="Add to Compare"><i class="fa fa-sliders" aria-hidden="true"></i></a></li>

                                                </ul>
                                            </div>
                                       </div>

                                    </div>
                                    <div class="quick_button">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick_view">+ quick view</a>
                                    </div>

                                    <div class="product_sale">
                                        <span>-7%</span>
                                    </div>
                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.html">Marshall Portable  Bluetooth</a></h3>
                                    <span class="current_price">£60.00</span>
                                    <span class="old_price">£86.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.html"><img src="assets/img/product/product27.jpg" alt=""></a>
                                    <a class="secondary_img" href="product-details.html"><img src="assets/img/product/product28.jpg" alt=""></a>
                                    <div class="product_action">
                                        <div class="hover_action">
                                           <a  href="#"><i class="fa fa-plus"></i></a>
                                            <div class="action_button">
                                                <ul>

                                                    <li><a title="add to cart" href="cart.html"><i class="fa fa-shopping-basket" aria-hidden="true"></i></a></li>
                                                    <li><a href="wishlist.html" title="Add to Wishlist"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>

                                                    <li><a href="compare.html" title="Add to Compare"><i class="fa fa-sliders" aria-hidden="true"></i></a></li>

                                                </ul>
                                            </div>
                                       </div>

                                    </div>
                                    <div class="quick_button">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick_view">+ quick view</a>
                                    </div>
                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.html">Koss KPH7 Portable</a></h3>
                                    <span class="current_price">£60.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.html"><img src="assets/img/product/product6.jpg" alt=""></a>
                                    <a class="secondary_img" href="product-details.html"><img src="assets/img/product/product5.jpg" alt=""></a>
                                    <div class="product_action">
                                        <div class="hover_action">
                                           <a  href="#"><i class="fa fa-plus"></i></a>
                                            <div class="action_button">
                                                <ul>

                                                    <li><a title="add to cart" href="cart.html"><i class="fa fa-shopping-basket" aria-hidden="true"></i></a></li>
                                                    <li><a href="wishlist.html" title="Add to Wishlist"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>

                                                    <li><a href="compare.html" title="Add to Compare"><i class="fa fa-sliders" aria-hidden="true"></i></a></li>

                                                </ul>
                                            </div>
                                       </div>

                                    </div>
                                    <div class="quick_button">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick_view">+ quick view</a>
                                    </div>

                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.html">Beats Solo2 Solo 2</a></h3>
                                    <span class="current_price">£60.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.html"><img src="assets/img/product/product7.jpg" alt=""></a>
                                    <a class="secondary_img" href="product-details.html"><img src="assets/img/product/product8.jpg" alt=""></a>
                                    <div class="product_action">
                                        <div class="hover_action">
                                           <a  href="#"><i class="fa fa-plus"></i></a>
                                            <div class="action_button">
                                                <ul>

                                                    <li><a title="add to cart" href="cart.html"><i class="fa fa-shopping-basket" aria-hidden="true"></i></a></li>
                                                    <li><a href="wishlist.html" title="Add to Wishlist"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>

                                                    <li><a href="compare.html" title="Add to Compare"><i class="fa fa-sliders" aria-hidden="true"></i></a></li>

                                                </ul>
                                            </div>
                                       </div>

                                    </div>
                                    <div class="quick_button">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick_view">+ quick view</a>
                                    </div>

                                    <div class="product_sale">
                                        <span>-7%</span>
                                    </div>
                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.html">Beats EP Wired</a></h3>
                                    <span class="current_price">£60.00</span>
                                    <span class="old_price">£86.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.html"><img src="assets/img/product/product24.jpg" alt=""></a>
                                    <a class="secondary_img" href="product-details.html"><img src="assets/img/product/product25.jpg" alt=""></a>
                                    <div class="product_action">
                                        <div class="hover_action">
                                           <a  href="#"><i class="fa fa-plus"></i></a>
                                            <div class="action_button">
                                                <ul>

                                                    <li><a title="add to cart" href="cart.html"><i class="fa fa-shopping-basket" aria-hidden="true"></i></a></li>
                                                    <li><a href="wishlist.html" title="Add to Wishlist"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>

                                                    <li><a href="compare.html" title="Add to Compare"><i class="fa fa-sliders" aria-hidden="true"></i></a></li>

                                                </ul>
                                            </div>
                                       </div>

                                    </div>
                                    <div class="quick_button">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick_view">+ quick view</a>
                                    </div>
                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.html">Bose SoundLink Bluetooth</a></h3>
                                    <span class="current_price">£60.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.html"><img src="assets/img/product/product10.jpg" alt=""></a>
                                    <a class="secondary_img" href="product-details.html"><img src="assets/img/product/product11.jpg" alt=""></a>
                                    <div class="product_action">
                                        <div class="hover_action">
                                           <a  href="#"><i class="fa fa-plus"></i></a>
                                            <div class="action_button">
                                                <ul>

                                                    <li><a title="add to cart" href="cart.html"><i class="fa fa-shopping-basket" aria-hidden="true"></i></a></li>
                                                    <li><a href="wishlist.html" title="Add to Wishlist"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>

                                                    <li><a href="compare.html" title="Add to Compare"><i class="fa fa-sliders" aria-hidden="true"></i></a></li>

                                                </ul>
                                            </div>
                                       </div>

                                    </div>
                                    <div class="quick_button">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick_view">+ quick view</a>
                                    </div>

                                    <div class="product_sale">
                                        <span>-7%</span>
                                    </div>
                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.html">Apple iPhone SE 16GB</a></h3>
                                    <span class="current_price">£60.00</span>
                                    <span class="old_price">£86.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="single_product">
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.html"><img src="assets/img/product/product23.jpg" alt=""></a>
                                    <a class="secondary_img" href="product-details.html"><img src="assets/img/product/product24.jpg" alt=""></a>
                                    <div class="product_action">
                                        <div class="hover_action">
                                           <a  href="#"><i class="fa fa-plus"></i></a>
                                            <div class="action_button">
                                                <ul>

                                                    <li><a title="add to cart" href="cart.html"><i class="fa fa-shopping-basket" aria-hidden="true"></i></a></li>
                                                    <li><a href="wishlist.html" title="Add to Wishlist"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>

                                                    <li><a href="compare.html" title="Add to Compare"><i class="fa fa-sliders" aria-hidden="true"></i></a></li>

                                                </ul>
                                            </div>
                                       </div>

                                    </div>
                                    <div class="quick_button">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick_view">+ quick view</a>
                                    </div>
                                </div>
                                <div class="product_content">
                                    <h3><a href="product-details.html">JBL Flip 3 Portable</a></h3>
                                    <span class="current_price">£60.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
               
        </div>
    </section> -->
        <!--product section area end-->



        <!--footer area start-->
        <footer class="footer_widgets product_page">
            <div class="footer_top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-2 col-md-6 col-sm-6 col-6">
                            <div class="widgets_container">
                                <h3>Information</h3>
                                <div class="footer_menu">
                                    <ul>
                                        <li><a href="about.html">About Us</a></li>
                                        <li><a href="#">Delivery Information</a></li>
                                        <li><a href="privacy-policy.html">Privacy Policy</a></li>
                                        <li><a href="#">Terms & Conditions</a></li>
                                        <li><a href="contact.html">Contact Us</a></li>
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
                                        <li><a href="contact.html">Site Map</a></li>
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
                                    <p>Exceptional quality. Ethical factories. Sign up to enjoy free U.S. shipping and returns on your first order.</p>
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
                        <div class="col-lg-6 col-md-6">
                            <div class="copyright_area">
                                <p> &copy; 2022 <strong> Reid </strong> Mede with ❤️ by <a href="https://hasthemes.com/" target="_blank"><strong>HasThemes</strong></a></p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="footer_custom_links">
                                <ul>
                                    <li><a href="#">Order History</a></li>
                                    <li><a href="wishlist.html">Wish List</a></li>
                                    <li><a href="#">Newsletter</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--footer area end-->

        <!-- modal area start-->
        <div class="modal fade" id="modal_box" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="modal_body">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-5 col-md-5 col-sm-12">
                                    <div class="modal_tab">
                                        <div class="tab-content product-details-large">
                                            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                                <div class="modal_tab_img">
                                                    <a href="#"><img src="assets/img/product/product4.jpg" alt=""></a>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab2" role="tabpanel">
                                                <div class="modal_tab_img">
                                                    <a href="#"><img src="assets/img/product/product6.jpg" alt=""></a>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab3" role="tabpanel">
                                                <div class="modal_tab_img">
                                                    <a href="#"><img src="assets/img/product/product8.jpg" alt=""></a>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab4" role="tabpanel">
                                                <div class="modal_tab_img">
                                                    <a href="#"><img src="assets/img/product/product2.jpg" alt=""></a>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab5" role="tabpanel">
                                                <div class="modal_tab_img">
                                                    <a href="#"><img src="assets/img/product/product12.jpg" alt=""></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal_tab_button">
                                            <ul class="nav product_navactive owl-carousel" role="tablist">
                                                <li>
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false"><img src="assets/img/s-product/product3.jpg" alt=""></a>
                                                </li>
                                                <li>
                                                    <a class="nav-link" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false"><img src="assets/img/s-product/product.jpg" alt=""></a>
                                                </li>
                                                <li>
                                                    <a class="nav-link button_three" data-bs-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false"><img src="assets/img/s-product/product2.jpg" alt=""></a>
                                                </li>
                                                <li>
                                                    <a class="nav-link" data-bs-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="false"><img src="assets/img/s-product/product4.jpg" alt=""></a>
                                                </li>
                                                <li>
                                                    <a class="nav-link" data-bs-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="false"><img src="assets/img/s-product/product5.jpg" alt=""></a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7 col-md-7 col-sm-12">
                                    <div class="modal_right">
                                        <div class="modal_title mb-10">
                                            <h2>Handbag feugiat</h2>
                                        </div>
                                        <div class="modal_price mb-10">
                                            <span class="new_price">$64.99</span>
                                            <span class="old_price">$78.99</span>
                                        </div>
                                        <div class="modal_description mb-15">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia iste laborum ad impedit pariatur esse optio tempora sint ullam autem deleniti nam in quos qui nemo ipsum numquam, reiciendis maiores quidem aperiam, rerum vel recusandae </p>
                                        </div>
                                        <div class="variants_selects">
                                            <div class="variants_size">
                                                <h2>size</h2>
                                                <select class="select_option">
                                                    <option selected value="1">s</option>
                                                    <option value="1">m</option>
                                                    <option value="1">l</option>
                                                    <option value="1">xl</option>
                                                    <option value="1">xxl</option>
                                                </select>
                                            </div>
                                            <div class="variants_color">
                                                <h2>color</h2>
                                                <select class="select_option">
                                                    <option selected value="1">purple</option>
                                                    <option value="1">violet</option>
                                                    <option value="1">black</option>
                                                    <option value="1">pink</option>
                                                    <option value="1">orange</option>
                                                </select>
                                            </div>
                                            <div class="modal_add_to_cart">
                                                <form action="#">
                                                    <input min="0" max="100" step="2" value="1" type="number">
                                                    <button type="submit">add to cart</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal_social">
                                            <h2>Share this product</h2>
                                            <ul>
                                                <li class="facebook"><a href="#"><i class="fa fa-facebook"></i></a></li>
                                                <li class="twitter"><a href="#"><i class="fa fa-twitter"></i></a></li>
                                                <li class="pinterest"><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                                <li class="google-plus"><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                                <li class="linkedin"><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal area start-->


        <!-- JS
============================================ -->

        <!-- Plugins JS -->
        <script src="assets/js/plugins.js"></script>

        <!-- Main JS -->
        <script src="assets/js/main.js"></script>

        <script>
            function validateForm() {
                var rating = document.querySelector('input[name="star_rating"]:checked');
                if (!rating) {
                    alert("Vui lòng chọn đánh giá sao.");
                    return false;
                }
                return true;
            }
        </script>




</body>

</html>