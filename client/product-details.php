<?php
session_start();
include("../function.php");

// Khởi tạo kết nối đến cơ sở dữ liệu
$conn = initConnection();

// Kiểm tra trạng thái đăng nhập
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;

// Lấy giá trị category_id từ tham số truy vấn
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

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

$categories = getCategoryByID($conn);

// Nếu người dùng chưa đăng nhập và họ cố gắng thêm feedback, chuyển hướng họ đến trang đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST" && $customer_id === null) {
    echo "<script>alert('Bạn cần đăng nhập để thêm feedback.');</script>";
    // header("Location: login.php");
    echo "<script>return false;</script>";
}

// Nếu người dùng đã đăng nhập, thêm feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && $customer_id !== null) {
    addFeedbackProduct($conn, $product_id, $category_id, $customer_id);
}

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

function addFeedbackProduct($conn, $product_id, $category_id, $customer_id)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // $name=$_POST['author'];
        $description = strip_tags($_POST['comment']);
        $star_rating = $_POST['star_rating']; // Lấy giá trị số sao từ form
        $feedbackDate = date('Y-m-d H:i:s'); // Lấy thời gian hiện tại


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
    $sql = "SELECT f.*, c.customer_name AS customer_name
        FROM feedback f
        INNER JOIN customer c ON f.customer_id = c.customer_id
        WHERE f.product_id = ?";

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

function getCategoryByID($conn)
{
    $sql = "SELECT * FROM category WHERE isDeleted=0";
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
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
    <link rel="stylesheet" href="../assets/css/plugins.css">

    <!-- Main Style CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

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
                            <li><a href="index.html">home</a></li>
                            <li>/</li>
                            <li><?php echo $categoryName; ?></li>
                            <li>/</li>
                            <li><a>product_details</a></li>
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
                                    <?php
                                    // Kiểm tra nếu đường dẫn ảnh không phải là URL (không có "http://" hoặc "https://")
                                    if (!filter_var($product['avatar_product'], FILTER_VALIDATE_URL)) {
                                        // Tạo đường dẫn tuyệt đối tới thư mục "uploads"
                                        $uploadImagePath = '../admin/xhtml/xhtml/uploads/' . $product['avatar_product'];
                                        // Kiểm tra nếu tập tin tồn tại trong thư mục "uploads"
                                        if (file_exists($uploadImagePath)) {
                                            // Sử dụng đường dẫn tuyệt đối tới thư mục "uploads"
                                            echo '<img id="zoom1" src="' . $uploadImagePath . '" data-zoom-image="' . $uploadImagePath . '" alt="big-1">';
                                        } else {
                                            // Nếu không tìm thấy tập tin trong thư mục "uploads", sử dụng đường dẫn mặc định
                                            echo '<img id="zoom1" src="' . $product['avatar_product'] . '" data-zoom-image="' . $product['avatar_product'] . '" alt="big-1">';
                                        }
                                    } else {
                                        // Nếu đường dẫn là URL, sử dụng đường dẫn đó như bình thường
                                        echo '<img id="zoom1" src="' . $product['avatar_product'] . '" data-zoom-image="' . $product['avatar_product'] . '" alt="big-1">';
                                    }
                                    ?>
                                </a>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7">
                        <?php foreach ($products as $product) : ?>
                            <div class="product_d_right">
                                <form action="#">

                                    <h1><?php echo $product['product_name']; ?></h1>
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

                                    <!--product variant color start-->
                                    <div class="product_variant color">
                                        <h3>Color</h3>
                                        <select class="niceselect_option" id="color" name="product_color">
                                            <option selected disabled>Choose color</option>
                                            <?php foreach ($products as $product) : ?>
                                                <?php
                                                // Phân tách chuỗi màu sắc thành các phần tử riêng biệt
                                                $colors = explode(', ', $product['arr_color']);
                                                ?>
                                                <?php foreach ($colors as $color) : ?>
                                                    <option value="<?php echo $color; ?>"><?php echo $color; ?></option>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <!--product variant color end-->
                                    <div class="product_variant size">
                                        <h3>size</h3>
                                        <select class="niceselect_option" id="color" name="product_color">
                                            <option selected disabled>Choose size</option>
                                            <?php foreach ($products as $product) : ?>
                                                <?php
                                                $sizes = explode(', ', $product['size']);
                                                ?>
                                                <?php foreach ($sizes as $size) : ?>
                                                    <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="product_variant quantity">
                                        <label>quantity</label>
                                        <input min="1" max="100" value="<?php echo $product['quantity'] ?>" readonly>

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
                            </div>


                            <div class="tab-pane fade  active" id="reviews" role="tabpanel">


                                <div class="product_review_form">
                                    <form action="product-details.php?category_id=<?php echo $category_id ?>&product_id=<?php echo $product_id ?>" method="post" onsubmit="return validateForm()">
                                        <h2>Add a review </h2>
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
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                            <div class="widgets_container">
                                <h3>Extras</h3>
                                <div class="footer_menu">
                                    <ul>
                                        <li><a href="about.php">About Us</a></li>
                                        <li><a href="contact.php">Contact Us</a></li>
                                        <li><a href="#">Returns</a></li>
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
                                                <a href="#"><img src="../assets/img/product/product4.jpg" alt=""></a>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab2" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="../assets/img/product/product6.jpg" alt=""></a>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab3" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="../assets/img/product/product8.jpg" alt=""></a>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab4" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="../assets/img/product/product2.jpg" alt=""></a>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab5" role="tabpanel">
                                            <div class="modal_tab_img">
                                                <a href="#"><img src="../assets/img/product/product12.jpg" alt=""></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal_tab_button">
                                        <ul class="nav product_navactive owl-carousel" role="tablist">
                                            <li>
                                                <a class="nav-link active" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="false"><img src="../assets/img/s-product/product3.jpg" alt=""></a>
                                            </li>
                                            <li>
                                                <a class="nav-link" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false"><img src="../assets/img/s-product/product.jpg" alt=""></a>
                                            </li>
                                            <li>
                                                <a class="nav-link button_three" data-bs-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false"><img src="../assets/img/s-product/product2.jpg" alt=""></a>
                                            </li>
                                            <li>
                                                <a class="nav-link" data-bs-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="false"><img src="../assets/img/s-product/product4.jpg" alt=""></a>
                                            </li>
                                            <li>
                                                <a class="nav-link" data-bs-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="false"><img src="../assets/img/s-product/product5.jpg" alt=""></a>
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
    <script src="../assets/js/plugins.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

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