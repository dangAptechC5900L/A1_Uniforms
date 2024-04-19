<?php
session_start();
include '../../../function.php';

$conn = initConnection();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['login']) || $_SESSION['login'] !== TRUE) {
    // Nếu không, chuyển hướng người dùng đến trang đăng nhập
    header("Location: admin-login.php");
    exit;
}

//Change Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_id'])) {
	$category_id = $_POST['category_id'];

	// Đảo ngược trạng thái của khách hàng trong cơ sở dữ liệu
	$sql = "UPDATE category SET isDeleted = 1 - isDeleted WHERE category_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $category_id);

	if ($stmt->execute()) {
		// Chuyển hướng lại đến trang hiện tại sau khi cập nhật thành công
		header("Location: " . $_SERVER['PHP_SELF']);
		exit();
	} else {
		echo "Có lỗi xảy ra khi cập nhật trạng thái!";
	}
	$stmt->close();
}

function getTotalCategory($conn)
{
	$sql = "SELECT COUNT(*) FROM category";
	$result = $conn->query($sql);
	return $result->fetch_all(MYSQLI_ASSOC);
}

// function searchCustomerByUserName($conn){
// 	$sql="SELECT * FROM customer where"
// }

function getAllCategory($conn)
{
	$sql = "SELECT * FROM category";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->get_result();

	$category = array();
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$category[] = $row;
		}
	}
	$stmt->close();

	return $category;
}

function getCategoryByName($conn)
{
	if (isset($_GET['term'])) {
		$searchTerm = '%' . $_GET['term'] . '%';

		$sql = "SELECT * FROM category WHERE name LIKE ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $searchTerm);
		$stmt->execute();
		$result = $stmt->get_result();
		$categorySearch = $result->fetch_all(MYSQLI_ASSOC);

		return $categorySearch;
	} else {
		// Trả về một giá trị mặc định hoặc xử lý khác khi không có 'term' được truyền vào.
		return array(); // hoặc return null; tùy vào yêu cầu của bạn
	}
}


$categorySearch = getCategoryByName($conn);
$category = getAllCategory($conn);

$rows = getTotalCategory($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Title -->
	<title>A-1 uniforms - Category</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Favicon icon -->

	<link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
	<link href="vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
	<link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
	<link href="vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
	<link class="main-css" href="css/style.css" rel="stylesheet">

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
	<div id="main-wrapper">

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
		<!--**********************************
            Nav header end
        ***********************************-->



		<!--**********************************
            Header start
        ***********************************-->
		<div class="header">
			<div class="header-content">
				<nav class="navbar navbar-expand">
					<div class="collapse navbar-collapse justify-content-between">
						<div class="header-left">
							<div class="dashboard_bar">
								Category List
							</div>
						</div>

						<ul class="navbar-nav header-right">
							<!-- <li class="nav-item dropdown notification_dropdown">
								<div class="input-group search-area">
									<input type="text" class="form-control" placeholder="Search here...">
									<span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
								</div>
							</li> -->
							<li class="nav-item dropdown notification_dropdown">
								<a class="nav-link bell  primary dz-theme-mode" href="javascript:void(0);">
									<i id="icon-light" class="fas fa-sun"></i>
									<i id="icon-dark" class="fas fa-moon"></i>

								</a>
							</li>

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
							<li><a href="add-category.php">Add Customers</a></li>
							<li><a href="chat.html">Chat</a></li>
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
							<li><a href="logout.php">Login</a></li>
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
					<p class="op5">© 2024 A-1 Uniforms</p>
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
		<?php foreach ($rows as $row) {
		?>
			<div class="content-body">
				<!-- row -->
				<div class="container-fluid">
					<div class="row mb-5 align-items-center">
						<div class="col-xl-3 mb-4 mb-xl-0">
							<a href="add-category.php" class="btn btn-primary light btn-lg d-block rounded shadow px-2">+ New Category</a>
						</div>
						<div class="col-xl-9">
							<div class="card m-0 ">
								<div class="card-body py-3 py-md-2">
									<div class="row align-items-center">
										<div class="col-md-5 mb-3 mb-md-0">
											<div class="media align-items-center">
												<span class="me-2">
													<svg width="24" height="24" class="category-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" clip-rule="evenodd" d="M20 4H4C2.89543 4 2 4.89543 2 6V18C2 19.1046 2.89543 20 4 20H20C21.1046 20 22 19.1046 22 18V6C22 4.89543 21.1046 4 20 4ZM4 2C2.34315 2 1 3.34315 1 5V19C1 20.6569 2.34315 22 4 22H20C21.6569 22 23 20.6569 23 19V5C23 3.34315 21.6569 2 20 2H4ZM12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" fill="#222fb9" />
													</svg>


												</span>
												<div class="media-body ms-1">
													<p class="mb-0 fs-14">Total Category</p>
													<h3 class="mb-0 text-black font-w600 fs-16"><?php echo $row['COUNT(*)'] ?> </h3>
												</div>

											</div>
										</div>
										<div class="col-md-7 text-md-end">
											<li class="nav-item dropdown notification_dropdown">
												<form action="category-list.php" method="GET">
													<div class="input-group search-area" style="margin-left:200px">
														<input type="text" class="form-control" name="term" placeholder="Search category here...">
														<span class="input-group-text">
															<button type="submit" class="btn btn-primary shadow btn-xs sharp me-1"><i class="flaticon-381-search-2"></i></button>
														</span>
													</div>
												</form>
											</li>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<div class="table-responsive">
								<table id="example5" class=" display mb-4 table-responsive-xl dataTablesCard fs-14">
									<thead>
										<tr>
											<th>
												<div class="form-check custom-checkbox">
													<input type="checkbox" class="form-check-input" id="checkAll" required="">
													<!-- <label class="form-check-label" for="checkAll"></label> -->
												</div>
											</th>
											<!-- <th>ID</th> -->
											<th>Name</th>
											<th>Description</th>
											<th>Status</th>
											<th>Function</th>
										</tr>
									</thead>


									<tbody>
										<?php
										// Kiểm tra xem có kết quả tìm kiếm không
										if (!empty($categorySearch)) {
											foreach ($categorySearch as $categorySearch) {
										?>
												<tr>
													<td>
														<div class="form-check custom-checkbox">
															<input type="checkbox" class="form-check-input" id="customCheckBox2" required="">
															<label class="form-check-label" for="customCheckBox2"></label>
														</div>
													</td>
													<!-- <td><?= $category['category_id'] ?></td> -->
													<td>
														<p><?php echo $categorySearch['name'] ?></p>
													</td>
													<td>
														<p><?php echo $categorySearch['description'] ?></p>
													</td>


													<td>
														<div class="btn-group">
															<form action="category-list.php" method="post">
																<button type="submit" name="category_id" value="<?php echo $categorySearch['category_id']; ?>" class="btn <?php echo $categorySearch['isDeleted'] == 0 ? 'btn-success' : 'btn-secondary'; ?> rounded">
																	<?php echo $categorySearch['isDeleted'] == 0 ? "Active" : "Inactive"; ?>
																</button>
															</form>
														</div>

													</td>
													<td>

														<div class="d-flex">
															<a href="edit-category.php?category_id=<?php echo $categorySearch['category_id']; ?>" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
														</div>
													</td>
												</tr>
											<?php
											}
										} else {
											// Nếu không có kết quả tìm kiếm, hiển thị danh sách tất cả khách hàng
											foreach ($category as $category) {
											?>
												<tr>
													<td>
														<div class="form-check custom-checkbox">
															<input type="checkbox" class="form-check-input" id="customCheckBox2" required="">
															<label class="form-check-label" for="customCheckBox2"></label>
														</div>
													</td>
													<!-- <td><?= $category['category_id'] ?></td> -->
													<td>
														<p><?php echo $category['name'] ?></p>
													</td>
													<td>
														<p><?php echo $category['description'] ?></p>
													</td>


													<td>
														<div class="btn-group">
															<form action="category-list.php" method="post">
																<button type="submit" name="category_id" value="<?php echo $category['category_id']; ?>" class="btn <?php echo $category['isDeleted'] == 0 ? 'btn-success' : 'btn-secondary'; ?> rounded">
																	<?php echo $category['isDeleted'] == 0 ? "Active" : "Inactive"; ?>
																</button>
															</form>
														</div>

													</td>
													<td>
														<div class="d-flex">
															<a href="edit-category.php?category_id=<?php echo $category['category_id']; ?>" class="btn btn-primary shadow btn-xs sharp me-1" style="margin-left: 20px;"><i class="fas fa-pencil-alt"></i></a>
														</div>
													</td>
												</tr>
										<?php
											}
										}
										?>
									</tbody>

								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php }
		?>
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


	<div class="footer">
		<div class="copyright">
			<p>© 2024 A-1 Uniforms</p>
		</div>
	</div>
	<!--**********************************
        Main wrapper end
    ***********************************-->

	<!--**********************************
        Scripts
    ***********************************-->
	<!-- <script>
    function changeStatus(customer_id, new_status) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == "success") {
                    // Nếu thành công, cập nhật giao diện người dùng
                    var btnId = (new_status == 1) ? "activeBtn_" + customer_id : "inactiveBtn_" + customer_id;
                    var btnText = (new_status == 1) ? "Active" : "Inactive";
                    document.getElementById(btnId).innerHTML = btnText;
                    document.getElementById(btnId).classList.toggle("btn-success");
                    document.getElementById(btnId).classList.toggle("btn-secondary");
                } else {
                    alert("Có lỗi xảy ra khi cập nhật trạng thái!");
                }
            }
        };
        xhttp.open("POST", "customer-list.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("customer_id=" + customer_id + "&new_status=" + new_status);
    }
</script> -->

	<!-- Required vendors -->
	<script src="vendor/global/global.min.js"></script>
	<script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>

	<script src="vendor/bootstrap-datetimepicker/js/moment.js"></script>
	<script src="vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>

	<!-- Apex Chart -->
	<script src="vendor/apexchart/apexchart.js"></script>

	<!-- Datatable -->
	<script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
	<script src="js/plugins-init/datatables.init.js"></script>

	<script src="js/custom.min.js"></script>
	<script src="js/deznav-init.js"></script>
	<script src="js/demo.js"></script>
	<script src="js/styleSwitcher.js"></script>

</body>

</html>