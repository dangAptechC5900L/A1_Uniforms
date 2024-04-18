<?php
include '../../../function.php';

$conn = initConnection();

//Change Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback_id'])) {
	$feedback_id = $_POST['feedback_id'];

	// Đảo ngược trạng thái của khách hàng trong cơ sở dữ liệu
	$sql = "UPDATE feedback SET isDeleted = 1 - isDeleted WHERE feedback_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $feedback_id);

	if ($stmt->execute()) {
		// Chuyển hướng lại đến trang hiện tại sau khi cập nhật thành công
		header("Location: " . $_SERVER['PHP_SELF']);
		exit();
	} else {
		echo "Có lỗi xảy ra khi cập nhật trạng thái!";
	}
	$stmt->close();
}

function getTotalFeedback($conn)
{
	$sql = "SELECT COUNT(*) FROM feedback";
	$result = $conn->query($sql);
	return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllFeedback($conn)
{
	// Chuẩn bị câu truy vấn SQL với prepared statement
	$sql = "SELECT f.feedback_id, p.product_name, c.customer_name, f.title, f.description, f.feedbackDate, f.isDeleted, f.star_rating
FROM feedback f
JOIN product p ON f.product_id = p.product_id
JOIN customer c ON f.customer_id = c.customer_id";


	// Tạo prepared statement
	$stmt = $conn->prepare($sql);

	// Kiểm tra và thực thi prepared statement
	if ($stmt === false) {
		die("Lỗi trong quá trình chuẩn bị câu truy vấn: " . $conn->error);
	}

	// Thực thi prepared statement
	$stmt->execute();

	// Lấy kết quả từ prepared statement
	$result = $stmt->get_result();

	$feedbacks = array();

	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$feedbacks[] = $row;
		}
	}
	$stmt->close();

	return $feedbacks;
}
// function getAllFeedback($conn)
// {
// 	$sql = "SELECT * FROM feedback";
// 	$stmt = $conn->prepare($sql);
// 	$stmt->execute();
// 	$result = $stmt->get_result();

// 	$feedbacks = array();
// 	if ($result && $result->num_rows > 0) {
// 		while ($row = $result->fetch_assoc()) {
// 			$feedbacks[] = $row;
// 		}
// 	}
// 	$stmt->close();

// 	return $feedbacks;
// }

function getFeedbackByProductName($conn)
{
	if (isset($_GET['term'])) {
		$searchTerm = '%' . $_GET['term'] . '%';

		// Chuẩn bị câu truy vấn SQL với prepared statement
		$sql = "SELECT f.feedback_id, p.product_name, c.customer_name, f.title, f.description, f.feedbackDate, f.isDeleted, f.star_rating
		FROM feedback f
		JOIN product p ON f.product_id = p.product_id
		JOIN customer c ON f.customer_id = c.customer_id
		WHERE p.product_name LIKE ?";

		// Tạo prepared statement
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $searchTerm);
		$stmt->execute();
		$result = $stmt->get_result();
		$feedbackSearch = $result->fetch_all(MYSQLI_ASSOC);

		return $feedbackSearch;
	} else {
		// Trả về một giá trị mặc định hoặc xử lý khác khi không có 'term' được truyền vào.
		return array(); // hoặc return null; tùy vào yêu cầu của bạn
	}
}


$feedbackSearch = getFeedbackByProductName($conn);
$feedbacks = getAllFeedback($conn);

$rows = getTotalFeedback($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Title -->
	<title>A1-Uniforms - Feedback</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Favicon icon -->


	<link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
	<link href="vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
	<link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
	<!-- <link href="vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet"> -->
	<link class="main-css" href="css/style.css" rel="stylesheet">

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
								Feedback List
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
							<li><a href="add-customers.php">Add Customers</a></li>
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

						<div class="col-xl-6">
							<div class="card m-0 ">
								<div class="card-body py-3 py-md-2">
									<div class="row align-items-center">
										<div class="col-md-5 mb-3 mb-md-0">
											<div class="media align-items-center">
												<span class="me-2">
													<svg width="24" height="24" class="user-svg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<g clip-path="url(#clip0)">
															<path d="M21 24H3C2.73478 24 2.48043 23.8946 2.29289 23.7071C2.10536 23.5196 2 23.2652 2 23V22.008C2.00287 20.4622 2.52021 18.9613 3.47044 17.742C4.42066 16.5227 5.74971 15.6544 7.248 15.274C7.46045 15.2219 7.64959 15.1008 7.78571 14.9296C7.92182 14.7583 7.9972 14.5467 8 14.328V13.322L6.883 12.206C6.6032 11.9313 6.38099 11.6036 6.22937 11.2419C6.07776 10.8803 5.99978 10.4921 6 10.1V5.96201C6.01833 4.41693 6.62821 2.93765 7.70414 1.82861C8.78007 0.719572 10.2402 0.0651427 11.784 5.16174e-06C12.5992 -0.00104609 13.4067 0.158488 14.1603 0.469498C14.9139 0.780509 15.5989 1.2369 16.1761 1.81263C16.7533 2.38835 17.2114 3.07213 17.5244 3.82491C17.8373 4.5777 17.999 5.38476 18 6.20001V10.1C17.9997 10.4949 17.9204 10.8857 17.7666 11.2495C17.6129 11.6132 17.388 11.9426 17.105 12.218L16 13.322V14.328C16.0029 14.5469 16.0784 14.7586 16.2147 14.9298C16.351 15.1011 16.5404 15.2221 16.753 15.274C18.251 15.6548 19.5797 16.5232 20.5298 17.7424C21.4798 18.9617 21.997 20.4624 22 22.008V23C22 23.2652 21.8946 23.5196 21.7071 23.7071C21.5196 23.8946 21.2652 24 21 24ZM4 22H20C19.9954 20.8996 19.6249 19.8319 18.9469 18.9651C18.2689 18.0983 17.3219 17.4816 16.255 17.212C15.6125 17.0494 15.0423 16.6779 14.6341 16.1558C14.2259 15.6337 14.0028 14.9907 14 14.328V12.908C14.0001 12.6428 14.1055 12.3885 14.293 12.201L15.703 10.792C15.7965 10.7026 15.8711 10.5952 15.9221 10.4763C15.9731 10.3574 15.9996 10.2294 16 10.1V6.20001C16.0017 5.09492 15.5671 4.03383 14.7907 3.24737C14.0144 2.46092 12.959 2.01265 11.854 2.00001C10.8264 2.04117 9.85379 2.47507 9.1367 3.21225C8.41962 3.94943 8.01275 4.93367 8 5.96201V10.1C7.99979 10.2266 8.0249 10.352 8.07384 10.4688C8.12278 10.5856 8.19458 10.6914 8.285 10.78L9.707 12.2C9.89455 12.3875 9.99994 12.6418 10 12.907V14.327C9.99724 14.9896 9.77432 15.6325 9.3663 16.1545C8.95827 16.6766 8.3883 17.0482 7.746 17.211C6.67872 17.4804 5.73137 18.0972 5.05318 18.9642C4.37498 19.8313 4.00447 20.8993 4 22Z" fill="#222fb9" />
														</g>
														<defs>
															<clipPath id="clip0">
																<rect width="24" height="24" fill="white" />
															</clipPath>
														</defs>
													</svg>
												</span>
												<div class="media-body ms-1">
													<p class="mb-0 fs-14">Total Feedback</p>
													<h3 class="mb-0 text-black font-w600 fs-16"><?php echo $row['COUNT(*)'] ?> Posts</h3>
												</div>

											</div>
										</div>
										<div class="col-md-7 text-md-end">
											<li class="nav-item dropdown notification_dropdown">
												<form action="feedback-list.php" method="GET">
													<div class="input-group search-area" style="margin-left:50px">
														<input type="text" class="form-control" name="term" placeholder="Search feedback here...">
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
													<label class="form-check-label" for="checkAll"></label>
												</div>
											</th>
											<!-- <th>ID</th> -->
											<th>Product_name</th>
											<th>Customer name</th>
											<th>Description</th>
											<th>Feedback_Date</th>
											<th>Star_rating</th>
											<th>Middle_Name</th>

											<th>Function</th>
										</tr>
									</thead>


									<tbody>
										<?php
										// Kiểm tra xem có kết quả tìm kiếm không
										if (!empty($feedbackSearch)) {
											foreach ($feedbackSearch as $feedbackSearch) {
										?>
												<tr>
													<td>
														<div class="form-check custom-checkbox">
															<input type="checkbox" class="form-check-input" id="customCheckBox2" required="">
															<label class="form-check-label" for="customCheckBox2"></label>
														</div>
													</td>
													<!-- <td><?= $customer['customer_id'] ?></td> -->
													<td>
														<p><?php echo $feedbackSearch['product_name'] ?></p>
													</td>
													<td>
														<p><?php echo $feedbackSearch['customer_name'] ?></p>
													</td>
													<td>
														<p><?php echo $feedbackSearch['description'] ?></p>
													</td>
													<td>
														<p><?php echo $feedbackSearch['feedbackDate'] ?></p>
													</td>
													<td>
														<p><?php echo $feedbackSearch['star_rating'] ?></p>
													</td>

													<td>
														<div class="btn-group">
															<form action="feedback-list.php" method="post">
																<button type="submit" name="feedback_id" value="<?php echo $feedbackSearch['feedback_id']; ?>" class="btn <?php echo $feedbackSearch['isDeleted'] == 0 ? 'btn-success' : 'btn-secondary'; ?> rounded">
																	<?php echo $feedbackSearch['isDeleted'] == 0 ? "Active" : "Inactive"; ?>
																</button>
															</form>
														</div>

													</td>
													<td>

														<div class="d-flex">
															<a href="showFeedback.php?feedback_id=<?php echo $feedbackSearch['feedback_id']; ?>" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa-solid fa-eye"></i></a>

														</div>
													</td>
												</tr>
											<?php
											}
										} else {
											// Nếu không có kết quả tìm kiếm, hiển thị danh sách tất cả khách hàng
											foreach ($feedbacks as $feedback) {
											?>
												<tr>
													<td>
														<div class="form-check custom-checkbox">
															<input type="checkbox" class="form-check-input" id="customCheckBox2" required="">
															<label class="form-check-label" for="customCheckBox2"></label>
														</div>
													</td>
													<!-- <td><?= $customer['customer_id'] ?></td> -->
													<td>
														<p><?php echo $feedback['product_name'] ?></p>
													</td>
													<td>
														<p><?php echo $feedback['customer_name'] ?></p>
													</td>
													<td>
														<p><?php echo $feedback['description'] ?></p>
													</td>
													<td>
														<p><?php echo $feedback['feedbackDate'] ?></p>
													</td>
													<td>
														<p><?php echo $feedback['star_rating'] ?></p>
													</td>

													<td>
														<div class="btn-group">
															<form action="feedback-list.php" method="post">
																<button type="submit" name="feedback_id" value="<?php echo $feedback['feedback_id']; ?>" class="btn <?php echo $feedback['isDeleted'] == 0 ? 'btn-success' : 'btn-secondary'; ?> rounded">
																	<?php echo $feedback['isDeleted'] == 0 ? "Active" : "Inactive"; ?>
																</button>
															</form>
														</div>

													</td>
													<td>

														<div class="d-flex">
														<a href="showFeedback.php?feedback_id=<?php echo $feedback['feedback_id']; ?>" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa-solid fa-eye"></i></a>
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


</body>

</html>