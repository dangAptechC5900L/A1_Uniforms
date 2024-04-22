<?php
include '../../../function.php';

$conn = initConnection();

//Change Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
	$product_id = $_POST['product_id'];

	// Đảo ngược trạng thái của khách hàng trong cơ sở dữ liệu
	$sql = "UPDATE product SET isDeleted = 1 - isDeleted WHERE product_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('i', $product_id);

	if ($stmt->execute()) {
		// Chuyển hướng lại đến trang hiện tại sau khi cập nhật thành công
		header("Location: " . $_SERVER['PHP_SELF']);
		exit();
	} else {
		echo "Có lỗi xảy ra khi cập nhật trạng thái!";
	}
	$stmt->close();
}

function getTotalProduct($conn)
{
	$sql = "SELECT COUNT(*) FROM product";
	$result = $conn->query($sql);
	return $result->fetch_all(MYSQLI_ASSOC);
}

// function searchCustomerByUserName($conn){
// 	$sql="SELECT * FROM customer where"
// }

function getAllProduct($conn)
{
	$sql = "SELECT p.*, c.name AS category_name 
            FROM product p 
            INNER JOIN category c ON p.category_id = c.category_id";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->get_result();

	$products = array();
	if ($result && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$products[] = $row;
		}
	}
	$stmt->close();

	return $products;
}

function getProductByName($conn)
{
    if (isset($_GET['term'])) {
        $searchTerm = '%' . $_GET['term'] . '%';

        $sql = "SELECT p.*, c.name AS category_name 
                FROM product p 
                INNER JOIN category c ON p.category_id = c.category_id
                WHERE p.product_name LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $productSearch = $result->fetch_all(MYSQLI_ASSOC);

        return $productSearch;
    } else {
        // Trả về một giá trị mặc định hoặc xử lý khác khi không có 'term' được truyền vào.
        return array(); // hoặc return null; tùy vào yêu cầu của bạn
    }
}


$productSearch = getProductByName($conn);
$products = getAllProduct($conn);

$rows = getTotalProduct($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Title -->
	<title>A-1 uniforms - Product</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Favicon icon -->

	<link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
	<link href="vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
	<link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
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
								Product List
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
							<a href="add-product.php" class="btn btn-primary light btn-lg d-block rounded shadow px-2">+ New Product</a>
						</div>
						<div class="col-xl-9">
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
													<p class="mb-0 fs-14">Total Product</p>
													<h3 class="mb-0 text-black font-w600 fs-16"><?php echo $row['COUNT(*)'] ?> </h3>
												</div>

											</div>
										</div>
										<div class="col-md-7 text-md-end">
											<li class="nav-item dropdown notification_dropdown">
												<form action="product-list.php" method="GET">
													<div class="input-group search-area" style="margin-left:200px">
														<input type="text" class="form-control" name="term" placeholder="Search product here...">
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
											<th>Product_Name</th>
											<th>Category_name</th>
											<th>Price</th>
											<th>Image</th>
											<th>Color</th>
											<th>Material</th>
											<th>Description</th>
											<th>Create_Date</th>
											<th>Size</th>
											<th>Quantity</th>
											<th>Status</th>
											<th>Function</th>
										</tr>
									</thead>


									<tbody>
										<?php
										// Kiểm tra xem có kết quả tìm kiếm không
										if (!empty($productSearch)) {
											foreach ($productSearch as $productSearch) {
										?>
												<tr>
													<td>
														<div class="form-check custom-checkbox">
															<input type="checkbox" class="form-check-input" id="customCheckBox2" required="">
															<label class="form-check-label" for="customCheckBox2"></label>
														</div>
													</td>
												
													<td>
														<p><?php echo $productSearch['product_name'] ?></p>
													</td>
													<td>
														<p><?php echo $productSearch['category_name'] ?></p>
													</td>
													<td>
														<p><?php echo $productSearch['price'] ?></p>
													</td>

													<td>
														<?php
														// Kiểm tra nếu đường dẫn ảnh không bắt đầu bằng "http://" hoặc "https://"
														if (!preg_match("~^(?:f|ht)tps?://~i", $productSearch['avatar_product'])) {
															// Nếu không, giả sử đây là đường dẫn đến thư mục uploads
															echo '<img src="uploads/' . $productSearch['avatar_product'] . '" alt="Avatar" width="100">';
														} else {
															// Nếu có, đây là đường dẫn trực tuyến
															echo '<img src="' . $productSearch['avatar_product'] . '" alt="Avatar" width="100">';
														}
														?>
													</td>

													<td>
														<p><?php echo $productSearch['arr_color'] ?></p>
													</td>
													<td>
														<p><?php echo $productSearch['material'] ?></p>
													</td>
													<td>
														<p><?php echo $productSearch['description'] ?></p>
													</td>
													<td>
														<p><?php echo $productSearch['create_date'] ?></p>
													</td>
													<td>
														<p><?php echo $productSearch['size'] ?></p>
													</td>
													<td>
														<p><?php echo $productSearch['quantity'] ?></p>
													</td>

													<td>
														<div class="btn-group">
															<form action="product-list.php" method="post">
																<button type="submit" name="product_id" value="<?php echo $productSearch['product_id']; ?>" class="btn <?php echo $productSearch['isDeleted'] == 0 ? 'btn-success' : 'btn-secondary'; ?> rounded">
																	<?php echo $productSearch['isDeleted'] == 0 ? "Active" : "Inactive"; ?>
																</button>
															</form>
														</div>

													</td>
													<td>

														<div class="d-flex">
															<a href="edit-customer.php?customer_id=<?php echo $customer['customer_id']; ?>" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
														</div>
													</td>
												</tr>
											<?php
											}
										} else {
											// Nếu không có kết quả tìm kiếm, hiển thị danh sách tất cả khách hàng
											foreach ($products as $product) {
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
														<p><?php echo $product['product_name'] ?></p>
													</td>
													<td>
														<p><?php echo $product['category_name'] ?></p>
													</td>
													<td>
														<p><?php echo $product['price'] ?></p>
													</td>
													<!-- <td>
														<img src="<?php echo $product['avatar_product']; ?>" alt="Avatar" width="100">
													</td> -->

													<td>
														<?php
														// Kiểm tra nếu đường dẫn ảnh không bắt đầu bằng "http://" hoặc "https://"
														if (!preg_match("~^(?:f|ht)tps?://~i", $product['avatar_product'])) {
															// Nếu không, giả sử đây là đường dẫn đến thư mục uploads
															echo '<img src="uploads/' . $product['avatar_product'] . '" alt="Avatar" width="100">';
														} else {
															// Nếu có, đây là đường dẫn trực tuyến
															echo '<img src="' . $product['avatar_product'] . '" alt="Avatar" width="100">';
														}
														?>
													</td>

													<td>
														<p><?php echo $product['arr_color'] ?></p>
													</td>
													<td>
														<p><?php echo $product['material'] ?></p>
													</td>
													<td>
														<p><?php echo $product['description'] ?></p>
													</td>
													<td>
														<p><?php echo $product['create_date'] ?></p>
													</td>
													<td>
														<p><?php echo $product['size'] ?></p>
													</td>
													<td>
														<p><?php echo $product['quantity'] ?></p>
													</td>

													<!-- <td>
													<div class="btn-group">
														<?php if ($customer['isDeleted'] == 0) : ?>
															<button id="activeBtn_<?php echo $customer['customer_id']; ?>" class="btn btn-success rounded" onclick="changeStatus(<?php echo $customer['customer_id']; ?>, 1)">Active</button>
														<?php else : ?>
															<button id="inactiveBtn_<?php echo $customer['customer_id']; ?>" class="btn btn-secondary rounded" onclick="changeStatus(<?php echo $customer['customer_id']; ?>, 0)">Inactive</button>
														<?php endif; ?>
													</div>
												</td> -->
													<td>
														<div class="btn-group">
															<form action="product-list.php" method="post">
																<button type="submit" name="product_id" value="<?php echo $product['product_id']; ?>" class="btn <?php echo $product['isDeleted'] == 0 ? 'btn-success' : 'btn-secondary'; ?> rounded">
																	<?php echo $product['isDeleted'] == 0 ? "Active" : "Inactive"; ?>
																</button>
															</form>
														</div>

													</td>
													<td>
														<div class="d-flex">
															<a href="edit-product.php?product_id=<?php echo $product['product_id']; ?>&selected_file=<?php echo $product['avatar_product']; ?>&category_id=<?php echo $product['category_id']; ?>" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
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
	<!-- <script src="js/styleSwitcher.js"></script> -->

</body>

</html>