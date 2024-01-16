<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");

# data requset from raw
$data = json_decode(file_get_contents('php://input'), true);
$key = $data['api_key'];
$number = $data['number'];
header('Content-Type: application/json');

if (!isset($number) && !isset($key)) {
    $ret['status'] = 400;
    $ret['message'] = "Bad request!: invalid request parameter or required";
    echo json_encode($ret, true);
    exit;
}

$check = mysqli_query($conn, "SELECT api_key, email FROM users WHERE api_key = '$key'");
$row = mysqli_fetch_assoc($check);
$sender = $row['email'];
if ($check->num_rows < 1) {
    $ret['status'] = 401;
    $ret['message'] = "Unathorized: Invalid API KEY!";
    echo json_encode($ret, true);
    exit;
}

if ($key && $number) {
    $res = check_numbers($key, $number);
    echo json_encode($res, true);
    exit;
} else {
    $ret['status'] = 500;
    $ret['message'] = "Internal Server Error: Something went wrong!";
    echo json_encode($ret, true);
    exit;
}
