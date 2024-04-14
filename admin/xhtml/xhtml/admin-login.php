
<?php
require_once('../../../function.php');
global $conn;
initConnection();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? $_POST['remember'] : '';
    if (empty($email)) {
        $errors[] = "Username must be provided";
    }
    if (empty($password)) {
        $errors[] = "Password must be provided";
    }
    if (count($errors) == 0) {
        $password_hash = sha1($password);
        $query_string = "SELECT * FROM customer WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($query_string);
        $stmt->bind_param('ss', $email, $password_hash);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            session_start();
            $_SESSION['login'] = TRUE;
            $row = $result->fetch_assoc(); // Fetch the row once
            $_SESSION['customer_name'] = $row['customer_name'];
            $_SESSION['customer_id'] = $row['customer_id'];
            if ($remember == 'on') {
                $expire = time() + 3600 * 24 * 30; 
                setcookie('email', $email, $expire);
                setcookie('password', $password, $expire);
            }
            echo "<script>alert('Login Successful');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
            exit;
        } else {
            $errors[] = "Invalid username or password";
        }
    }
}
if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    $email = $_COOKIE['email'];
    $password = $_COOKIE['password'];
    echo "<script>
            document.getElementById('remember').checked = true;
            document.getElementsByName('email')[0].value = '$email';
            document.getElementsByName('password')[0].value = '$password';
          </script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <!-- Title -->
	<title>Tixia - Ticketing Admin Dashboard Bootstrap HTML Template | DexignZone</title>
 
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="DexignZone">
	<meta name="robots" content="index, follow">
   
	<meta name="keywords" content="	admin dashboard, admin template, administration, analytics, bootstrap, bootstrap admin, coupon, deal, modern, responsive admin dashboard, ticket, ticket dashboard, ticket system, admin panel,	Ticketing admin, Dashboard template, Bootstrap HTML, Ticket management, Event ticketing, Responsive design, User-friendly interface, Efficiency, Streamlining operations, Event management, Ticket sales, Customizable template, Stylish design, Modern dashboard">
	<meta name="description" content="Discover Tixia, the ultimate solution for ticketing administration. Our Bootstrap HTML Template empowers you to streamline ticketing tasks, enhancing operational efficiency with style and ease. Simplify your processes and elevate your ticketing management experience today.">
   
	<meta property="og:title" content="Tixia - Ticketing Admin Dashboard Bootstrap HTML Template | DexignZone">
	<meta property="og:description" content="Discover Tixia, the ultimate solution for ticketing administration. Our Bootstrap HTML Template empowers you to streamline ticketing tasks, enhancing operational efficiency with style and ease. Simplify your processes and elevate your ticketing management experience today.">
	<meta property="og:image" content="page-error-404.html">
	<meta name="format-detection" content="telephone=no">
   
	<meta name="twitter:title" content="Tixia - Ticketing Admin Dashboard Bootstrap HTML Template | DexignZone">
	<meta name="twitter:description" content="Discover Tixia, the ultimate solution for ticketing administration. Our Bootstrap HTML Template empowers you to streamline ticketing tasks, enhancing operational efficiency with style and ease. Simplify your processes and elevate your ticketing management experience today.">
	<meta name="twitter:image" content="page-error-404.html">
	<meta name="twitter:card" content="summary_large_image">

	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
   <link class="main-css" href="css/style.css" rel="stylesheet">

</head>

<body>
    <div class="fix-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6">
                    <div class="card mb-0 h-auto">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <!-- <a href="index.html"><img class="logo-auth" src="./images/logo1.svg" alt=""></a> -->
                            </div>
                            <h4 class="text-center mb-4">Sign in your account</h4>
                            <form action="https://tixia.dexignzone.com/xhtml/index.html">
                                <div class="form-group mb-4">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter email">
                                </div>
                               <div class="form-group mb-3 mb-sm-4">
									<label class="form-label">Password</label>
									<div class="position-relative">
										<input type="password" id="dz-password" name="password" class="form-control" value="Password">
										<span class="show-pass eye">
											<!-- <i class="fa fa-eye-slash"></i> -->
											<i class="fa fa-eye"></i>
										</span>
									</div>
								</div>
                                <div class="form-row d-flex flex-wrap justify-content-between align-items-baseline mb-2">
                                    <div class="form-group mb-sm-4 mb-1">
                                        <div class="form-check custom-checkbox ms-1">
                                            <input type="checkbox" class="form-check-input" id="basic_checkbox_1">
                                            <label class="form-check-label" name="remember"  for="basic_checkbox_1">Remember my preference</label>
                                        </div>
											
                                    </div>
                                    <div class="form-group ms-2">
                                        <a href="page-forgot-password.html">Forgot Password?</a>
										
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                                </div>
                            </form>
                            <!-- <div class="new-account mt-3">
                                <p>Don't have an account? <a class="text-primary" href="page-register.html">Sign up</a></p>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <!-- <script src="vendor/global/global.min.js"></script>
	<script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/deznav-init.js"></script>
    <script src="js/demo.js"></script>
    <script src="js/styleSwitcher.js"></script> -->
</body>

</html>