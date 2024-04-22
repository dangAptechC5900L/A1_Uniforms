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
$contact_id = $_GET['contact_id'] ?? null;


$contacts = getContactById($conn, $contact_id);

function getContactById($conn, $contact_id)
{
    $sql = "SELECT * FROM contact WHERE contact_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $contact_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $contacts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $contacts;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title -->
    <title>A-1 uniforms - Contact_Us Infomation</title>

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

    <style>
        /* CSS cho nút "X" */
        .btn-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            color: #fff;
        }

        /* CSS để tạo hiệu ứng khi di chuột vào nút */
        .btn-close:hover {
            color: #ccc;
        }
    </style>


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
                                Contact Information
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
                                <h6 class="title text-white">Contact Information</h6>
                                <a href="contact_us-list.php" class="btn-close" aria-label="Close"></a> <!-- Thêm nút "X" để đóng pop-up -->
                            </div>


                            <?php foreach ($contacts as $contact) {
                            ?>

                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-sm-12 mb-3">
                                            <label class="form-label required">Fullname</label>
                                            <input type="text" name="userName" class="form-control" value="<?php echo $contact['fullname']; ?>" readonly>
                                        </div>

                                        <div class="col-sm-12 mb-3">
                                            <label class="form-label required">Email</label>
                                            <input type="text" name="firstName" class="form-control" value="<?php echo $contact['email']; ?>" readonly>
                                        </div>
                                        <div class="col-sm-12 mb-3">
                                            <label class="form-label required">Subject</label>
                                            <input type="text" name="middleName" class="form-control" value="<?php echo $contact['subject']; ?>" readonly>
                                        </div>
                                        <div class="col-sm-12 mb-3">
                                            <label class="form-label required">Phone number</label>
                                            <input type="text" name="lastName" class="form-control" value="<?php echo $contact['phone_number']; ?>" readonly>
                                        </div>
                                        <div class="col-sm-12 mb-3">
                                            <label class="form-label required">Message</label>
                                            <input type="email" name="email" class="form-control" value="<?php echo $contact['message']; ?>" readonly>
                                        </div>
                                        <div class="col-sm-12 mb-3">
                                            <label class="form-label required">Contact Date</label>
                                            <input type="text" name="phoneNumber" class="form-control" value="<?php echo $contact['contact_date']; ?>" readonly>
                                        </div>

                                    </div>
                                </div>


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
    <!-- <script src="js/styleSwitcher.js"></script> -->

</body>

</html>