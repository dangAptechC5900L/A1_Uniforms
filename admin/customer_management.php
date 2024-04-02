<?php
include('../function.php');

$conn = initConnection();



// function getAllCustomer($conn)
// {
//     $sql = "SELECT * FROM customer";
//     $result = $conn->query($sql);
//     return $result->fetch_all(MYSQLI_ASSOC);
// }

function getCustomerByUserName($conn)
{
    $showAll = isset($_GET['show_all']);
    if (isset($_GET['search'])) {
        $username = '%' . $_GET['search'] . '%';
        $sql = 'SELECT * FROM customer WHERE username LIKE ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
    } else {
        $sql = "SELECT * FROM customer";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $customers = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
    }
    $stmt->close();

    // Trường hợp người dùng nhấn vào nút "Lấy toàn bộ khách hàng"
    if ($showAll) {
        $sql = "SELECT * FROM customer";
        $result = $conn->query($sql);
        $customers = $result->fetch_all(MYSQLI_ASSOC);
    }
    return $customers;
}

$customers = getCustomerByUserName($conn);
// $rows = getAllCustomer($conn);

?>

<html>

<head>
    <meta charset="UTF-8">

    <!-- Link CSS của Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .my-button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 4px;
        text-decoration: none;
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .input-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-wrapper>div {
        margin: 0 10px;
        /* Khoảng cách giữa input và button */
    }

    .my-button:hover {
        background-color: #0056b3;
    }
</style>
</head>

<body>
    <div class="container">
        <div class="input-wrapper">
            <div>
                <a href="customer_management.php?show_all=true" class="my-button">Lấy toàn bộ khách hàng</a>
            </div>
            <div>
                <form method="GET" action="customer_management.php">
                    <input type="text" name="search" placeholder="Search UserName..." style="text-align:center; align-items:center; justify-content: center;" />
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>FirstName</th>
                <th>Middle_Name</th>
                <th>Last_Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <?php foreach ($customers as $customer) {
        ?>

            <tbody>
                <tr>
                    <td><?= $customer['customer_id'] ?></td>
                    <td>
                        <p><?php echo $customer['username'] ?></p>
                    </td>
                    <td>
                        <p><?php echo $customer['first_name'] ?></p>
                    </td>
                    <td>
                        <p><?php echo $customer['middle_name'] ?></p>
                    </td>
                    <td>
                        <p><?php echo $customer['last_name'] ?></p>
                    </td>
                    <td>
                        <p><?php echo $customer['email'] ?></p>
                    </td>
                    <td>
                        <p><?php echo $customer['phone_number'] ?></p>
                    </td>
                    <td>
                        <p><?php echo $customer['address'] ?></p>
                    </td>
                    <td>
                        <!-- <button><a href="form.php?province_id=<?php echo $row['id'] ?>">Edit</a></button> -->
                        <a class="btn btn-primary" href='add-edit.php?id=<?= $customer['id'] ?>'>Edit</a>
                        <a class="btn btn-danger" href='delete.php?id=<?= $customer['id'] ?>'>Delete</a>

                    </td>
                </tr>

            </tbody>

        <?php }
        ?>

    </table>

</body>

</html>