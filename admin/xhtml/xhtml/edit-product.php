<?php

include '../../../function.php';

$conn = initConnection();

$product_id = $_GET['product_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    if (isset($_FILES["image"])) {
        // Đường dẫn thư mục lưu trữ ảnh
        $targetDir = "uploads/";

        // Tạo đường dẫn đầy đủ của file đã upload
        $targetFilePath = $targetDir . basename($_FILES["image"]["name"]);

        // Kiểm tra xem file đã tồn tại chưa
        if (file_exists($targetFilePath)) {
            echo "File đã tồn tại.";
        } else {
            // Kiểm tra xem file có phải là ảnh hợp lệ không
            $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $allowedExtensions = array("jpg", "jpeg", "png", "gif");

            if (in_array($imageFileType, $allowedExtensions)) {
                // Thực hiện upload file
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    // Lưu đường dẫn của ảnh vào cơ sở dữ liệu
                    $imagePathInDB = $targetFilePath;

                    // In thông báo nếu upload ảnh thành công
                    echo "File được tải lên thành công.";
                } else {
                    echo "Đã xảy ra lỗi khi tải lên file.";
                }
            } else {
                echo "Chỉ cho phép tải lên các file ảnh định dạng JPG, JPEG, PNG, GIF.";
            }
        }
    }
}

function getCategory($conn)
{
    $sql = "SELECT * FROM category";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();

    $categories = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    $stmt->close();

    return $categories;
}



editProduct($conn, $product_id);
$products = getProductById($conn, $product_id);
$categories = getCategory($conn);

function getProductById($conn, $product_id)
{
    $sql = "SELECT * FROM product WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $products;
}

function editProduct($conn, $product_id)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $category = $_POST["category"];
        $product_name = $_POST["name"];
        $price = $_POST["price"];
        $image = $_FILES["image"]["name"];
        $color = $_POST["color"];
        $material = $_POST["material"];
        $description = $_POST["description"];
        $createDate = date('Y-m-d H:i:s');
        $size = implode(", ", $_POST["size"]);
        $quantity = $_POST["quantity"];

        if (empty($category) || empty($product_name) || empty($price) || empty($color) || empty($material) || empty($description) || empty($createDate) || empty($size) || empty($quantity)) {
            echo "Please fill in all information.";
            return;
        }

        $isDeleted = false;

        $sql = "UPDATE product SET category_id=?,product_name=?,price=?,avatar_product=?,arr_color=?,material=?,description=?,isDeleted=?,create_date=?,size=?,quantity=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdssssssssi", $category, $product_name, $price, $image, $color, $material, $description, $isDeleted, $createDate, $size, $quantity, $product_id);
        $stmt->execute();


        if ($stmt->execute()) {
            echo "Edit Product Success";
            header('Location:product-list.php');
        } else {
            echo "Error! " . $stmt->error;
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
    <title>A-1 uniforms - Edit Product</title>

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
                                Update Product
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
                                <h6 class="title text-white">Edit Product</h6>
                            </div>

                            <?php foreach ($products as $product) {
                            ?>
                                <form class="profile-form" action="edit-product.php?product_id=<?php echo $product['product_id']; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateForm();">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Name Product</label>
                                                <input type="text" name="name" class="form-control" placeholder="Enter Name Product..." value="<?php echo $product['product_name']; ?>">
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Category</label>
                                                <select class="form-select" id="category" name="category" style="height: auto; padding: 0.375rem 2.25rem 0.375rem 0.75rem;">
                                                    <?php
                                                    foreach ($categories as $category) {
                                                        $selected = ($category['category_id'] == $product['category_id']) ? 'selected' : ''; // Kiểm tra nếu ID của danh mục trùng khớp với danh mục của sản phẩm
                                                        echo '<option value="' . $category['category_id'] . '" ' . $selected . '>' . $category['name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Price</label>
                                                <input type="number" name="price" class="form-control" value="<?php echo $product['price']; ?>">
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Image</label>
                                                <!-- Hiển thị tên file đã chọn -->
                                                <?php if (isset($_GET['selected_file'])) : ?>
                                                    <p><?php echo $_GET['selected_file']; ?></p>
                                                <?php endif; ?>
                                                <!-- Input để chọn file mới -->
                                                <input type="file" name="image" class="form-control" accept="image/*">
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Color</label>
                                                <input type="text" name="color" class="form-control" placeholder="Enter color..." value="<?php echo $product['arr_color']; ?>">
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Material</label>
                                                <input type="text" name="material" class="form-control" placeholder="Enter material..." value="<?php echo $product['material']; ?>">
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Description</label>
                                                <input type="text" name="description" class="form-control" placeholder="Enter Description..." value="<?php echo $product['description']; ?>">
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Create Date</label>
                                                <input type="text" name="createDate" class="form-control" value="<?php echo $product['create_date']; ?>" readonly>
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Size</label>
                                                <select class="form-select" id="size" name="size[]" multiple style="height: auto; padding: 0.375rem 2.25rem 0.375rem 0.75rem;">
                                                    <?php
                                                    $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                                    foreach ($sizes as $size) {
                                                        if (in_array($size, explode(', ', $product['size']))) {
                                                            echo '<option selected>' . $size . '</option>';
                                                        } else {
                                                            echo '<option>' . $size . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label required">Quantity</label>
                                                <input type="number" name="quantity" class="form-control" value="<?php echo $product['quantity']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer justify-content-end">
                                        <button class="btn btn-primary">Update Product</button>
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




    <script type="text/javascript">
        function handleSuccess(customer_id) {
            $(document).ready(function() {
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