<?php
session_start();
// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['login']) || $_SESSION['login'] !== TRUE) {
    // Nếu không, chuyển hướng người dùng đến trang đăng nhập
    header("Location: admin-login.php");
    exit;
}

include '../../../function.php';

$conn = initConnection();

// $customer_id = $_GET['customer_id'];
$customer_id = $_GET['customer_id'] ?? null;

editCustomer($conn, $customer_id);
$customers = getCustomerById($conn, $customer_id);

function getCustomerById($conn, $customer_id)
{
    $sql = "SELECT * FROM customer WHERE customer_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $customers;
}

function editCustomer($conn, $customer_id)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $userName = $_POST["userName"];
        $password = $_POST["customer_password"];
        $firstName = $_POST["firstName"];
        $middleName = $_POST["middleName"];
        $lastName = $_POST["lastName"];
        $email = $_POST["email"];
        $phoneNumber = $_POST["phoneNumber"];
        $address = $_POST["address"];

        if (empty($userName) || empty($password) || empty($firstName) || empty($middleName) || empty($lastName) || empty($email) || empty($phoneNumber) || empty($address)) {
            echo "Vui lòng điền đầy đủ thông tin.";
            return;
        }

        // Kiểm tra các trường first_name, last_name, middle_name chỉ chứa ký tự
        if (!preg_match('/^[a-zA-Z]+$/', $firstName) || !preg_match('/^[a-zA-Z]+$/', $middleName) || !preg_match('/^[a-zA-Z]+$/', $lastName)) {
            echo "The 'first_name','middle_name','last_name' field only allows names, numbers are not allowed.";
            return;
        }

        // Mã hóa mật khẩu sử dụng sha1
        $hashedPassword = sha1($password);
        $isDeleted = false;

        $sql = "UPDATE customer SET password=?,first_name=?,last_name=?,middle_name=?,email=?,phone_number=?,address=?,isDeleted=? WHERE customer_id=?";
      
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii",  $hashedPassword, $firstName, $lastName, $middleName, $email, $phoneNumber, $address, $isDeleted, $customer_id);
       
        $stmt->execute();


        if ($stmt->execute()) {
            echo "Thêm khách hàng thành công";
        } else {
            echo "Lỗi khi sửa thông tin khách hàng: " . $stmt->error;
        }
        // Đóng kết nối tới cơ sở dữ liệu
        $stmt->close();
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title -->
    <title>A-1 uniforms - Edit Customer</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- MOBILE SPECIFIC -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon icon -->
   
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <link href="vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link class="main-css" href="css/style.css" rel="stylesheet">

    <!-- SweetAlert -->
    <!-- <link href="vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet"> -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/sweetalert2.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>

<body>

    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper" class="show">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
        <a href="index.php" class="brand-logo"><img src="../../../assets/img/logo/a1-uniforms-01.svg" alt=""></a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>

        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">
                                Update Customers
                            </div>
                        </div>

                        <ul class="navbar-nav header-right">

							<li class="nav-item dropdown header-profile">
								<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									<img src="images/profile/pic1.jpg" width="20" alt="">
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									
									<a href="logout.php" class="dropdown-item ai-icon">
										<svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
											<polyline points="16 17 21 12 16 7"></polyline>
											<line x1="21" y1="12" x2="9" y2="12"></line>
										</svg>
										<span class="ms-2">Logout </span>
									</a>
								</div>
							</li>

						</ul>
                    </div>
                </nav>
            </div>
        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="deznav">
			<div class="deznav-scroll">
				<ul class="metismenu" id="menu">
					<li><a class="has-arrow ai-icon" href="index.php" aria-expanded="false">
							<i class="flaticon-381-networking"></i>
							<span class="nav-text">Index</span>
						</a>

					</li>
					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-id-card-4"></i>
							<span class="nav-text">Customers</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="customer-list.php">Customers List</a></li>
							<li><a href="add-customers.php">Add Customer</a></li>

						</ul>
					</li>
					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-archive"></i>
							<span class="nav-text">Category</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="category-list.php">Category List</a></li>
							<li><a href="add-category.php">Add Category</a></li>

						</ul>
					</li>
					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-box"></i>
							<span class="nav-text">Products</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="product-list.php">Products List</a></li>
							<li><a href="add-product.php">Add Product</a></li>

						</ul>
					</li>

					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-compass-2"></i>
							<span class="nav-text">Contacts</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="contact_us-list.php">Contacts List</a></li>
						</ul>
					</li>

					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-bookmark"></i>
							<span class="nav-text">About Us</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="about_us-list.php">About Us List</a></li>
							<li><a href="add-about_us.php">Add About Us</a></li>
						</ul>
					</li>

					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-bookmark-1"></i>
							<span class="nav-text">Feedback</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="feedback-list.php">Feedback List</a></li>
						</ul>
					</li>
				</ul>
				<div class="copyright">
					<p class="op5">© 2024 A-1 Uniforms</p>
				</div>
			</div>
		</div>
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->

        <div class="content-body">
            <div class="container-fluid">
                <!-- row -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card  card-bx m-b30">
                            <div class="card-header bg-primary">
                                <h6 class="title text-white">Edit Customer</h6>
                            </div>

                            <?php foreach ($customers as $customer) {
                            ?>
                                <form class="profile-form" action="edit-customer.php?customer_id=<?php echo $customer['customer_id']; ?>" method="post" onsubmit="return validateForm();">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label required">Username</label>
                                                <input type="text" name="userName" class="form-control" placeholder="Enter Username" value="<?php echo $customer['customer_name']; ?>" readonly>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label required">Password </label>
                                                    <div class="position-relative">
                                                        <input type="password" name="customer_password" id="dz-password" class="form-control" placeholder="Enter Password" value="<?php echo $customer['password']; ?>">
                                                        <span class="show-pass eye">
                                                            <i class="fa fa-eye-slash"></i>
                                                            <i class="fa fa-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label required">First name</label>
                                                <input type="text" name="firstName" class="form-control" placeholder="Enter First name" value="<?php echo $customer['first_name']; ?>">
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label required">Middle name</label>
                                                <input type="text" name="middleName" class="form-control" placeholder="Enter Middle name" value="<?php echo $customer['middle_name']; ?>">
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label required">Last name</label>
                                                <input type="text" name="lastName" class="form-control" placeholder="Enter Last name" value="<?php echo $customer['last_name']; ?>">
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label required">Email</label>
                                                <input type="email" name="email" class="form-control" placeholder="Enter Email" value="<?php echo $customer['email']; ?>">
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label required">Phone Number</label>
                                                <input type="text" name="phoneNumber" class="form-control" placeholder="Enter Phone Number" value="<?php echo $customer['phone_number']; ?>">
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label required">Address</label>
                                                <input type="text" name="address" class="form-control" placeholder="Enter Address" value="<?php echo $customer['address']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer justify-content-end">
                                        <button class="btn btn-primary">Update Customer</button>
                                    </div>
                                </form>
                            <?php }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!--**********************************
            Content body end
        ***********************************-->


        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Event Title</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Event Name</label>
                                    <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="The Story Of Danau Toba">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>




    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="vendor/global/global.min.js"></script>
    <script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <!-- <script src="vendor/bootstrap-datetimepicker/js/moment.js"></script> -->
    <script src="vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/deznav-init.js"></script>
    <script src="js/demo.js"></script>
    <script src="js/styleSwitcher.js"></script>

   

    <script type="text/javascript">
        function handleSuccess(customer_id) {
            $(document).ready(function(){
            swal({
                icon: 'success',
                title: 'Sửa thông tin khách hàng thành công',
                showConfirmButton: false,
                
            }).then(function() {
                window.location.href = 'customer-list.php';
            });
        });

        }
    </script>
    <script>
        function validateForm() {
            var userName = document.getElementsByName("userName")[0].value;
            var password = document.getElementsByName("customer_password")[0].value;
            var firstName = document.getElementsByName("firstName")[0].value;
            var lastName = document.getElementsByName("lastName")[0].value;
            var middleName = document.getElementsByName("middleName")[0].value;
            var email = document.getElementsByName("email")[0].value;
            var phoneNumber = document.getElementsByName("phoneNumber")[0].value;
            var address = document.getElementsByName("address")[0].value;

            // Kiểm tra trường rỗng
            if (userName == "" || password == "" || firstName == "" || lastName == "" || middleName == "" || email == "" || phoneNumber == "" || address == "") {
                swal("Error!", "Please complete all information.", "error");
                return false;
            }

            // Kiểm tra first_name, last_name, middle_name chỉ chứa ký tự
            var nameRegex = /^[a-zA-Z]+$/;
            if (!nameRegex.test(firstName) || !nameRegex.test(lastName) || !nameRegex.test(middleName)) {
                swal("Error!", "The 'first_name','middle_name','last_name' field only allows names, numbers are not allowed.", "error");
                return false;
            }

            // Kiểm tra mật khẩu có ít nhất 8 ký tự và chứa ít nhất một ký tự chữ
            if (password.length < 8 || !/[a-zA-Z]/.test(password)) {
                swal("Error!", "Password must have at least 8 characters and at least one letter character.", "error");
                return false;
            }

            // Kiểm tra số điện thoại theo kiểu Việt Nam
            var phoneRegex = /^(0[1-9])+([0-9]{8})\b$/;
            if (!phoneRegex.test(phoneNumber)) {
                swal("Error!", "Invalid phone number.", "error");
                return false;
            }
            if (!validateEmail(email)) {
                swal("Error!", "Invalid email.", "error");
                return false;
            }

            return true;
        }
    </script>

</body>

</html>