<?php
function initConnection()
{
    global $conn;
    if (!$conn) {
        $db_host = 'localhost:3308';
        $db_user = 'root';
        $db_password = '';
        $db_name = 'sem1_group2_final';
        $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }
}

function generateRandomString($length = 40)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

// function searchProductByName($conn, $searchTerm)
// {
//     $sql = "SELECT * FROM product WHERE name LIKE ?";
//     $stmt = $conn->prepare($sql);
//     $searchTerm = '%' . $searchTerm . '%';
//     $stmt->bind_param("s", $searchTerm);
//     $stmt->execute();
//     $result = $stmt->get_result();


//     $products = array();
//     if ($result && $result->num_rows > 0) {
//         while ($row = $result->fetch_assoc()) {
//             $products[] = $row;
//         }
//     }

//     $stmt->close();
//     return $products;
// }


