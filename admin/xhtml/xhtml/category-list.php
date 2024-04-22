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
		<a href="index.php" class="brand-logo"><img src="../../../assets/img/logo/a1-uniforms-01.svg" alt=""></a>
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