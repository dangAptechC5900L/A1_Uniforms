<?php

include '../../../function.php';

$conn = initConnection();

addCustomers($conn);

function addCustomers($conn)
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

		if(empty($userName)||empty($password)||empty($firstName)||empty($middleName)||empty($lastName)||empty($email)||empty($phoneNumber)||empty($address)){
			echo "Vui lòng điền đầy đủ thông tin.";
			return;
		}

		// Kiểm tra các trường first_name, last_name, middle_name chỉ chứa ký tự
		if (!preg_match('/^[a-zA-Z]+$/', $firstName) || !preg_match('/^[a-zA-Z]+$/', $middleName) || !preg_match('/^[a-zA-Z]+$/', $lastName)) {
			echo "The 'first_name','middle_name','last_name' field only allows names, numbers are not allowed.";
			return;
		}

		// Mã hóa mật khẩu sử dụng bcrypt
		$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
		$isDeleted = false;
		$sql = "INSERT INTO customer (username,password,first_name,last_name,middle_name,email,phone_number,address,isDeleted)
		 VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ssssssssi", $userName, $hashedPassword, $firstName, $middleName, $lastName, $email, $phoneNumber, $address, $isDeleted);

		// Thực hiện truy vấn
		if ($stmt->execute()) {
			echo "Thêm khách hàng thành công";
			header("Location:customer-list.php");
		} else {
			echo "Lỗi khi thêm khách hàng: " . $stmt->error;
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
	<title>A-1 uniforms - home</title>

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
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/sweetalert2.min.css">
	<link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
	<link href="vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
	<link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
	<link class="main-css" href="css/style.css" rel="stylesheet">
	<link href="vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

</head>

<body>

	<!--*******************
        Preloader start
    ********************-->
	<div id="preloader">
		<div class="sk-three-bounce">
			<div class="sk-child sk-bounce1"></div>
			<div class="sk-child sk-bounce2"></div>
			<div class="sk-child sk-bounce3"></div>
		</div>
	</div>
	<!--*******************
        Preloader end
    ********************-->


	<!--**********************************
        Main wrapper start
    ***********************************-->
	<div id="main-wrapper" class="show">

		<!--**********************************
            Nav header start
        ***********************************-->
		<div class="nav-header">
			<a href="index.html" class="brand-logo">
				<svg class="logo-abbr" width="48" height="36" viewBox="0 0 48 36" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path class="svg-logo-path" d="M18.281 14.25C18.281 13.2145 19.1204 12.375 20.156 12.375H35.3438C36.3794 12.375 37.2188 13.2145 37.2188 14.25C37.2188 15.2856 36.3794 16.125 35.3438 16.125H20.156C19.1204 16.125 18.281 15.2856 18.281 14.25ZM44.25 14.25C44.25 15.2839 45.0911 16.125 46.125 16.125C47.1606 16.125 48 16.9645 48 18V26.2461C48 27.2817 47.1606 28.1211 46.125 28.1211H32.2766L25.3258 35.072C24.5935 35.8043 23.4063 35.8041 22.6742 35.072L15.7234 28.1211H1.875C0.839437 28.1211 0 27.2817 0 26.2461V18C0 16.9645 0.839437 16.125 1.875 16.125C2.90887 16.125 3.75 15.2839 3.75 14.25C3.75 13.2162 2.90887 12.375 1.875 12.375C0.839437 12.375 0 11.5356 0 10.5V2.25397C0 1.2184 0.839437 0.378967 1.875 0.378967H46.125C47.1606 0.378967 48 1.2184 48 2.25397V10.5C48 11.5356 47.1606 12.375 46.125 12.375C45.0911 12.375 44.25 13.2162 44.25 14.25ZM11.2498 4.12897H3.75V8.94631C5.93259 9.72022 7.5 11.8055 7.5 14.25C7.5 16.6946 5.93259 18.7798 3.75 19.5537V24.3711H11.2498V4.12897ZM44.25 4.12897H14.9998V24.3711H16.5C16.9972 24.3711 17.4743 24.5686 17.8258 24.9202L24 31.0945L30.1742 24.9203C30.5257 24.5687 31.0028 24.3712 31.5 24.3712H44.25V19.5538C42.0674 18.7799 40.5 16.6947 40.5 14.2501C40.5 11.8056 42.0674 9.72031 44.25 8.9464V4.12897Z" fill="#2130B8" />
				</svg>
				<svg class="brand-title" width="87" height="28" viewBox="0 0 87 28" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path class="svg-logo-title" d="M0.0880001 7.11H6.412V27H11.172V7.11H17.496V3.268H0.0880001V7.11ZM20.969 27H25.729V8.164H20.969V27ZM23.383 5.92C25.049 5.92 26.307 4.696 26.307 3.132C26.307 1.568 25.049 0.343999 23.383 0.343999C21.683 0.343999 20.459 1.568 20.459 3.132C20.459 4.696 21.683 5.92 23.383 5.92ZM40.8359 27H46.2079L39.9519 17.548L46.1399 8.164H41.0399L37.5719 13.978L33.7299 8.164H28.3579L34.5799 17.548L28.4259 27H33.5259L36.9599 21.152L40.8359 27ZM48.7933 27H53.5533V8.164H48.7933V27ZM51.2073 5.92C52.8733 5.92 54.1313 4.696 54.1313 3.132C54.1313 1.568 52.8733 0.343999 51.2073 0.343999C49.5073 0.343999 48.2833 1.568 48.2833 3.132C48.2833 4.696 49.5073 5.92 51.2073 5.92ZM57.0322 17.514C57.0322 23.396 60.8402 27.306 65.6002 27.306C68.5922 27.306 70.7342 25.878 71.8562 24.246V27H76.6502V8.164H71.8562V10.85C70.7342 9.286 68.6602 7.858 65.6342 7.858C60.8402 7.858 57.0322 11.632 57.0322 17.514ZM71.8562 17.582C71.8562 21.152 69.4762 23.124 66.8582 23.124C64.3082 23.124 61.8942 21.084 61.8942 17.514C61.8942 13.944 64.3082 12.04 66.8582 12.04C69.4762 12.04 71.8562 14.012 71.8562 17.582ZM86.2971 24.45C86.2971 22.886 85.0731 21.662 83.4071 21.662C81.6731 21.662 80.4491 22.886 80.4491 24.45C80.4491 26.014 81.6731 27.238 83.4071 27.238C85.0731 27.238 86.2971 26.014 86.2971 24.45Z" fill="#2130B8" />
				</svg>

			</a>

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
								Add Customers
							</div>
						</div>

						<ul class="navbar-nav header-right">

							<li class="nav-item dropdown header-profile">
								<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									<img src="images/profile/pic1.jpg" width="20" alt="">
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									<a href="app-profile.html" class="dropdown-item ai-icon">
										<svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
											<circle cx="12" cy="7" r="4"></circle>
										</svg>
										<span class="ms-2">Profile </span>
									</a>
									<a href="email-inbox.html" class="dropdown-item ai-icon">
										<svg id="icon-inbox" xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
											<polyline points="22,6 12,13 2,6"></polyline>
										</svg>
										<span class="ms-2">Inbox </span>
									</a>
									<a href="page-login.html" class="dropdown-item ai-icon">
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
					<li><a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-networking"></i>
							<span class="nav-text">Dashboard</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="index.html">Dashboard Light</a></li>
							<li><a href="index-2.html">Dashboard Dark</a></li>
							<li><a href="analytics.html">Analytics</a></li>
							<li><a href="events.html">Events</a></li>
							<li><a href="order-list.html">Order List</a></li>
							<li><a href="customer-list.html">Customer List</a></li>
							<li><a href="reviews.html">Reviews</a></li>
						</ul>
					</li>
					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-diploma"></i>
							<span class="nav-text">CMS <span class="badge badge-xs badge-danger ms-2">New</span></span>
						</a>
						<ul aria-expanded="false">
							<li><a href="content.html">Content</a></li>
							<li><a href="content-add.html">Add Content</a></li>
							<li><a href="email-template.html">Email Template</a></li>
							<li><a href="add-email.html">Add Email</a></li>
							<li><a href="menu-1.html">Menu</a></li>
							<li><a href="blog.html">Blog</a></li>
							<li><a href="add-blog.html">Add Blog</a></li>
							<li><a href="blog-category.html">Blog Category</a></li>
						</ul>
					</li>
					<li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
							<i class="flaticon-381-add-1"></i>
							<span class="nav-text">Icons <span class="badge badge-xs badge-danger ms-2">New</span></span>
						</a>
						<ul aria-expanded="false">
							<li><a href="flat-icons.html">Flaticons</a></li>
							<li><a href="svg-icons.html">SVG Icons</a></li>

						</ul>
					</li>
					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-id-card"></i>
							<span class="nav-text">Ticket<span class="badge badge-xs badge-danger ms-2">New</span></span>
						</a>
						<ul aria-expanded="false">
							<li><a href="create-ticket.html">Create Ticket</a></li>
							<li><a href="all-ticket.html">All Ticket</a></li>
						</ul>
					</li>
					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-id-card-4"></i>
							<span class="nav-text">Customers <span class="badge badge-xs badge-danger ms-2">New</span></span>
						</a>
						<ul aria-expanded="false">
							<li><a href="customers-list.html">Customers List</a></li>
							<li><a href="add-customers.html">Add Customers</a></li>
							<li><a href="chat.html">Chat</a></li>
						</ul>
					</li>
					<li>
						<a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-television"></i>
							<span class="nav-text">Apps</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="app-profile.html">Profile</a></li>
							<li><a href="edit-profile.html">Edit Profile <span class="badge badge-xs badge-danger ms-2">New</span></a></li>
							<li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">Email</a>
								<ul aria-expanded="false">
									<li><a href="email-compose.html">Compose</a></li>
									<li><a href="email-inbox.html">Inbox</a></li>
									<li><a href="email-read.html">Read</a></li>
								</ul>
							</li>
							<li><a href="app-calender.html">Calendar</a></li>
							<li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">Shop</a>
								<ul aria-expanded="false">
									<li><a href="ecom-product-grid.html">Product Grid</a></li>
									<li><a href="ecom-product-list.html">Product List</a></li>
									<li><a href="ecom-product-detail.html">Product Details</a></li>
									<li><a href="ecom-product-order.html">Order</a></li>
									<li><a href="ecom-checkout.html">Checkout</a></li>
									<li><a href="ecom-invoice.html">Invoice</a></li>
									<li><a href="ecom-customers.html">Customers</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li><a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-controls-3"></i>
							<span class="nav-text">Charts</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="chart-flot.html">Flot</a></li>
							<li><a href="chart-morris.html">Morris</a></li>
							<li><a href="chart-chartjs.html">Chartjs</a></li>
							<li><a href="chart-chartist.html">Chartist</a></li>
							<li><a href="chart-sparkline.html">Sparkline</a></li>
							<li><a href="chart-peity.html">Peity</a></li>
						</ul>
					</li>
					<li><a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-internet"></i>
							<span class="nav-text">Bootstrap</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="ui-accordion.html">Accordion</a></li>
							<li><a href="ui-alert.html">Alert</a></li>
							<li><a href="ui-badge.html">Badge</a></li>
							<li><a href="ui-button.html">Button</a></li>
							<li><a href="ui-modal.html">Modal</a></li>
							<li><a href="ui-button-group.html">Button Group</a></li>
							<li><a href="ui-list-group.html">List Group</a></li>
							<li><a href="ui-media-object.html">Media Object</a></li>
							<li><a href="ui-card.html">Cards</a></li>
							<li><a href="ui-carousel.html">Carousel</a></li>
							<li><a href="ui-dropdown.html">Dropdown</a></li>
							<li><a href="ui-popover.html">Popover</a></li>
							<li><a href="ui-progressbar.html">Progressbar</a></li>
							<li><a href="ui-tab.html">Tab</a></li>
							<li><a href="ui-typography.html">Typography</a></li>
							<li><a href="ui-pagination.html">Pagination</a></li>
							<li><a href="ui-grid.html">Grid</a></li>

						</ul>
					</li>
					<li><a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-heart"></i>
							<span class="nav-text">Plugins</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="uc-select2.html">Select 2</a></li>
							<li><a href="uc-nestable.html">Nestedable</a></li>
							<li><a href="uc-noui-slider.html">Noui Slider</a></li>
							<li><a href="uc-sweetalert.html">Sweet Alert</a></li>
							<li><a href="uc-toastr.html">Toastr</a></li>
							<li><a href="map-jqvmap.html">Jqv Map</a></li>
							<li><a href="uc-lightgallery.html">Light Gallery</a></li>
						</ul>
					</li>
					<li><a href="widget-basic.html" class="ai-icon" aria-expanded="false">
							<i class="flaticon-381-settings-2"></i>
							<span class="nav-text">Widget</span>
						</a>
					</li>
					<li><a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-notepad"></i>
							<span class="nav-text">Forms </span>
						</a>
						<ul aria-expanded="false">
							<li><a href="form-element.html">Form Elements</a></li>
							<li><a href="form-wizard.html">Wizard</a></li>
							<li><a href="form-ckeditor.html">Form CkEditor </a></li>
							<li><a href="form-pickers.html">Pickers</a></li>
							<li><a href="form-validation-jquery.html">Jquery Validate</a></li>
						</ul>
					</li>
					<li><a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-network"></i>
							<span class="nav-text">Table</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="table-bootstrap-basic.html">Bootstrap</a></li>
							<li><a href="table-datatable-basic.html">Datatable</a></li>
						</ul>
					</li>
					<li><a href="reports.html" class="ai-icon" aria-expanded="false">
							<i class="flaticon-381-list"></i>
							<span class="nav-text">Report <span class="badge badge-xs badge-danger ms-2">New</span></span>
						</a>
					</li>
					<li><a class="has-arrow ai-icon" href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-381-layer-1"></i>
							<span class="nav-text">Pages</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="page-register.html">Register <span class="badge badge-xs badge-danger ms-2">New</span></a></li>
							<li><a href="page-login.html">Login</a></li>
							<li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">Error</a>
								<ul aria-expanded="false">
									<li><a href="page-error-400.html">Error 400</a></li>
									<li><a href="page-error-403.html">Error 403</a></li>
									<li><a href="page-error-404.html">Error 404</a></li>
									<li><a href="page-error-500.html">Error 500</a></li>
									<li><a href="page-error-503.html">Error 503</a></li>
								</ul>
							</li>
							<li><a href="page-lock-screen.html">Lock Screen</a></li>
						</ul>
					</li>
				</ul>
				<div class="copyright">
					<p>Tixia Ticketing Admin Dashboard <br>© <span class="current-year">2024</span> All Rights Reserved</p>

					<p class="op5">Made with <span class="heart"></span> by DexignZone</p>
				</div>
			</div>
		</div>
		<!--**********************************
            Sidebar end
        ***********************************-->

		<!--**********************************
            EventList
        ***********************************-->

		<div class="event-sidebar dz-scroll" id="eventSidebar">
			<div class="card shadow-none rounded-0 bg-transparent h-auto mb-0">
				<div class="card-body text-center event-calender pb-2">
					<input type='text' class="form-control d-none" id='datetimepicker1'>
				</div>
			</div>
			<div class="card shadow-none rounded-0 bg-transparent h-auto">
				<div class="card-header border-0 pb-0">
					<h4 class="text-black">Upcoming Events</h4>
				</div>
				<div class="card-body">
					<div class="media mb-5 align-items-center event-list">
						<div class="p-3 text-center rounded me-3 date-bx bgl-primary">
							<h2 class="mb-0 text-black">3</h2>
							<h5 class="mb-1 text-black">Wed</h5>
						</div>
						<div class="media-body px-0">
							<h6 class="mt-0 mb-3 fs-14"><a class="text-black" href="events.html">Live Concert Choir Charity Event 2020</a></h6>
							<ul class="fs-14 list-inline mb-2 d-flex justify-content-between">
								<li>Ticket Sold</li>
								<li>561/650</li>
							</ul>
							<div class="progress mb-0" style="height:4px; width:100%;">
								<div class="progress-bar bg-warning progress-animated" style="width:85%; height:100%;" role="progressbar">
									<span class="sr-only">60% Complete</span>
								</div>
							</div>
						</div>
					</div>
					<div class="media mb-5 align-items-center event-list">
						<div class="p-3 text-center rounded me-3 date-bx bgl-primary">
							<h2 class="mb-0 text-black">16</h2>
							<h5 class="mb-1 text-black">Tue</h5>
						</div>
						<div class="media-body px-0">
							<h6 class="mt-0 mb-3 fs-14"><a class="text-black" href="events.html">Beautiful Fireworks Show In The New Year Night</a></h6>
							<ul class="fs-14 list-inline mb-2 d-flex justify-content-between">
								<li>Ticket Sold</li>
								<li>431/650</li>
							</ul>
							<div class="progress mb-0" style="height:4px; width:100%;">
								<div class="progress-bar bg-warning progress-animated" style="width:50%; height:100%;" role="progressbar">
									<span class="sr-only">60% Complete</span>
								</div>
							</div>
						</div>
					</div>
					<div class="media mb-0 align-items-center event-list">
						<div class="p-3 text-center rounded me-3 date-bx bgl-success">
							<h2 class="mb-0 text-black">28</h2>
							<h5 class="mb-1 text-black">Fri</h5>
						</div>
						<div class="media-body px-0">
							<h6 class="mt-0 mb-3 fs-14"><a class="text-black" href="events.html">The Story Of Danau Toba (Musical Drama)</a></h6>
							<ul class="fs-14 list-inline mb-2 d-flex justify-content-between">
								<li>Ticket Sold</li>
								<li>650/650</li>
							</ul>
							<div class="progress mb-0" style="height:4px; width:100%;">
								<div class="progress-bar bg-success progress-animated" style="width:100%; height:100%;" role="progressbar">
									<span class="sr-only">60% Complete</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer justify-content-between border-0 d-flex fs-14">
					<span>5 events more</span>
					<a href="events.html" class="text-primary">View more <i class="las la-long-arrow-alt-right scale5 ms-2"></i></a>
				</div>
			</div>
			<div class="card shadow-none rounded-0 bg-transparent h-auto mb-0">
				<div class="card-body text-center event-calender">
					<a href="javascript:void(0);" class="btn btn-primary btn-rounded btn shadow" data-bs-toggle="modal" data-bs-target="#exampleModal">
						+ New Event
					</a>
				</div>
			</div>
		</div>

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
								<h6 class="title text-white">Create Customer</h6>

							</div>
							<form class="profile-form" action="add-customers.php" method="post" onsubmit="return validateForm();">
								<div class="card-body">
									<div class="row">
										<div class="col-sm-6 mb-3">
											<label class=" form-label required">Username</label>
											<input type="text" name="userName" class="form-control" placeholder="Enter Username" required="">
										</div>
										<div class="col-sm-6 mb-3">
											<div class="form-group">
												<label class=" form-label required">Password </label>
												<div class="position-relative">
													<input type="password" name="customer_password" id="dz-password" class="form-control" placeholder="Enter Password">
													<span class="show-pass eye">
														<i class="fa fa-eye-slash"></i>
														<i class="fa fa-eye"></i>
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-6 mb-3">
											<label class=" form-label required">First name</label>
											<input type="text" name="firstName" class="form-control" placeholder="Enter First_name" required>
										</div>
										<div class="col-sm-6 mb-3">
											<label class=" form-label required">Middle name</label>
											<input type="text" name="middleName" class="form-control" placeholder="Enter Middle_name" required>
										</div>
										<div class="col-sm-6 mb-3">
											<label class=" form-label required">Last name</label>
											<input type="text" name="lastName" class="form-control" placeholder="Enter Last_name" required>
										</div>
										<div class="col-sm-6 mb-3">
											<label class=" form-label required">Email</label>
											<input type="email" name="email" class="form-control" placeholder="Enter Email" required>
										</div>
										<div class="col-sm-6 mb-3">
											<label class=" form-label required">Phone Number</label>
											<input type="text" name="phoneNumber" class="form-control" placeholder="Enter Phone_number" required>
										</div>

										<div class="col-sm-6 mb-3">
											<label class=" form-label required">Address</label>
											<input type="text" name="address" class="form-control" placeholder="Enter Address" required>
										</div>
										<!-- <div class="col-sm-6 mb-3">
											<label class=" form-label required">Gender</label>
											<select class="default-select wide form-control">
												<option>Please select</option>
												<option>Male</option>
												<option>Female</option>
											</select>
										</div> -->



									</div>
								</div>
								<div class="card-footer justify-content-end">
									<button class="btn btn-primary">Create Customer</button>
								</div>
							</form>
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
	<script src="vendor/bootstrap-datetimepicker/js/moment.js"></script>
	<script src="vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
	<script src="js/custom.min.js"></script>
	<script src="js/deznav-init.js"></script>
	<script src="js/demo.js"></script>
	<script src="js/styleSwitcher.js"></script>

	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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