<?php
require_once('../function.php');
global $conn;
initConnection();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_Password = $_POST['confirm_Password'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    if (empty($email)) {
        $errors[] = "Email must be provided";
    }
    if (empty($username)) {
        $errors[] = "Username must be provided";
    }
    if (empty($confirm_Password)) {
        $errors[] = "confirm_Password must be provided";
    }
    if ($password !== $confirm_Password) {
        $errors[] = "Password and Confirm Password do not match";
    }
    if (empty($password)) {
        $errors[] = "Password must be provided";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[a-zA-Z]/', $password)) {
        $errors[] = "Password must contain at least one letter";
    }
    $query_string = "SELECT * FROM customer WHERE email = ?";
    $stmt = $conn->prepare($query_string);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Email already exists";
    }
    if (count($errors) == 0) {
        $password_hash = sha1($password);
        $query_string = "INSERT INTO customer (customer_name, password, first_name, middle_name, last_name, email, phone_number, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query_string);
        $stmt->bind_param('ssssssds', $username, $password_hash, $first_name, $middle_name, $last_name, $email, $phone_number, $address);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Registration Successful');</script>";
            echo "<script>window.location.href = 'login.php';</script>";
            exit;
        } else {
            echo "<script>alert('Registration failed');</script>";
        }
    } else {
        $error_message = "";
        foreach ($errors as $error) {
            $error_message .= $error . "\\n";
        }
        echo "<script>alert('" . $error_message . "');</script>";
    }
}

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

$categories = getCategoryByID($conn);
?>


<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Register</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.svg">
    <link rel="stylesheet" href="../assets/css/plugins.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            <form action="#">
                                <input placeholder="Search products here..." type="text">
                                <button type="submit"><i class="ion-ios-search-strong"></i></button>
                            </form>
                        </div>

                        <div class="contact_phone">
                            <p>Call Free Support: <a href="tel:01234567890">01234567890</a></p>
                        </div>
                        <div id="menu" class="text-left ">
                            <ul class="offcanvas_main_menu">
                                <li class="menu-item-has-children">
                                <li><a>Shop<i class="fa fa-angle-down"></i></a>
                                    <ul class="sub_menu pages">
                                        <?php foreach ($categories as $category) : ?>
                                            <li><a href="productByCategory.php?category_id=<?php echo $category['category_id'] ?>"><?php echo $category['name'] ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
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

    <header class="header_area header_cart_page">
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
    </header>

    <div class="breadcrumbs_area other_bread">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="index.php">home</a></li>
                            <li>/</li>
                            <li><a href="register.php">register</a></li>
                            <li>/</li>
                            <li><a href="login.php">login</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="customer_login">
        <div class="container">
            <div class="row">
                <div class="col-8">
                    <div class="account_form register">
                        <h2>Register</h2>
                        <form action="register.php" onsubmit=" return validateForm()" method="post">
                            <p>
                                <label>First name <span>*</span></label>
                                <input type="text" name="first_name">
                            </p>
                            <p>
                                <label>Middle name <span>*</span></label>
                                <input type="text" name="middle_name">
                            </p>
                            <p>
                                <label>Last name <span>*</span></label>
                                <input type="text" name="last_name">
                            </p>
                            <p>
                                <label>Phone number <span>*</span></label>
                                <input type="tel" name="phone_number" pattern="0\d{9}" title="Phone number must start with 0 and be followed by 9 digits">
                            </p>
                            <p>
                                <label>Address <span>*</span></label>
                                <input type="text" name="address">
                            </p>
                            <p>
                                <label>User name <span>*</span></label>
                                <input type="text" name="username" required>
                            </p>
                            <p>
                                <label>Email address <span>*</span></label>
                                <input type="email" name="email" required>
                            </p>
                            <p>
                                <label>Passwords <span>*</span></label>
                                <input type="password" id="password" name="password" pattern="(?=.*[A-Za-z]).{8,}" title="Password must be at least 8 characters long and contain at least one letter" required>
                            </p>
                            <p>
                                <label>Confirm Password <span>*</span></label>
                                <input type="password" id="confirm_Password" name="confirm_Password" required>
                            </p>

                            <div class="login_submit">
                                <button type="submit" name="submit">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        function validateForm() {
            var password = document.getElementById('password').value;
            var confirm_password = document.getElementById('confirm_password').value;
            if (password === '' || confirm_password === '') {
                alert('Mật khẩu và xác nhận mật khẩu không được để trống.');
                return false;
            } else if (password !== confirm_password) {
                alert('Mật khẩu và xác nhận mật khẩu phải trùng khớp.');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>