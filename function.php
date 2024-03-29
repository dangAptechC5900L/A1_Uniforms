<?php
function initConnection(){
    global $conn;
    if(!$conn){
        $db_host = 'localhost';
        $db_user = 'root';
        $db_password = '';
        $db_name = 'a1_uniforms';
        $conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        return $conn;
    }
}

function generateRandomString($length = 40) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}


?>