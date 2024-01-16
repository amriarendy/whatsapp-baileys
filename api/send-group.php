<?php
include_once("../helpers/connection.php");
include_once("../helpers/function.php");

# data requset from raw
$data = json_decode(file_get_contents('php://input'), true);
$id_group = $data['id_group'];
$message = $data['message'];
$file = $data['url_media'];
$key = $data['api_key'];
header('Content-Type: application/json');

if (!isset($id_group) && !isset($message) && !isset($key)) {
    $ret['status'] = 400;
    $ret['message'] = "Bad request!: invalid request parameter or required";
    echo json_encode($ret, true);
    exit;
}

if (isset($file)) {
    $a = explode('/', $file);
    $filename = $a[count($a) - 1];
    $a2 = explode('.', $filename);
    $namefile = $a2[count($a2) - 2];
    $ext = $a2[count($a2) - 1];
    if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif') {
        $ret['status'] = 415;
        $ret['message'] = "Unsupported Media Type: Ext support: jpg, jpeg, png and gif";
        echo json_encode($ret, true);
        exit;
    }
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

if ($id_group && $message) {
    if ($id_group) {
        $categories = "api-send-group";
        $status = "waiting";
        $schedule = date("Y-m-d H:i:s");
        $query = mysqli_query($conn, "INSERT INTO messages(`message`, `message_categories`, `message_media`, `message_status`, `message_schedule`, `send_to`, `user_id`, `created_at`) 
        VALUES ('$message', '$categories', '$file', '$status', '$schedule', '$id_group', '$sender', '$timeZone')");
        if ($query) {
            $ret['status'] = 200;
            $ret['message'] = "Success: Sent and created message schedule!";
        } else {
            $ret['status'] = 500;
            $ret['message'] = "Internal Server Error: Something went wrong!";
        }
        echo json_encode($ret, true);
        exit;
    }
} else {
    $ret['status'] = 500;
    $ret['message'] = "Internal Server Error: Something went wrong!";
    echo json_encode($ret, true);
    exit;
}
