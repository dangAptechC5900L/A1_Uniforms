<?php


// Kiểm tra nếu đã đăng nhập, chuyển hướng người dùng đến trang khác
if(isset($_SESSION['login']) && $_SESSION['login'] === TRUE) {
    header("location: index.php"); // Thay đổi index.php thành URL của trang bạn muốn chuyển hướng đến
    exit;
}

require_once('../../../function.php');
global $conn;
initConnection();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (empty($email)) {
        $errors[] = "Username must be provided";
    }
    if (empty($password)) {
        $errors[] = "Password must be provided";
    }
    if (count($errors) == 0) {
        $query_string = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($query_string);
        $stmt->bind_param('ss', $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            session_start();
            $_SESSION['login'] = TRUE;
            echo "<script>alert('Login Successful');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
            exit;
        } else {
            $errors[] = "Invalid username or password";
            $error_message = "";
            foreach ($errors as $error) {
                $error_message .= $error . "\\n";
            }
            echo "<script>alert('" . $error_message . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title -->
    <title>A1-Uniforms - Login</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="DexignZone">
    <meta name="robots" content="index, follow">

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

                            <h4 class="text-center mb-4">Sign in your account</h4>

                            <form action="admin-login.php" method="post">
                                <div class="form-group mb-4">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" required>
                                </div>
                                <div class="form-group mb-3 mb-sm-4">
                                    <label class="form-label">Password</label>
                                    <div class="position-relative">
                                        <input type="password" required id="password" name="password" class="form-control" value="Password">
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                                </div>
                            </form>

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