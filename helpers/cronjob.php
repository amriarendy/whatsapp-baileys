<?php
include_once("connection.php");
include_once("function.php");

$count = 0;
$now = strtotime(date("Y-m-d H:i:s"));
$chunk = getSingleValDB("setting", "id", "1", "limited");
// $query = mysqli_query($conn, "SELECT * FROM messages WHERE message_status='waiting' ORDER BY id ASC LIMIT $chunk");
$query = mysqli_query(
    $conn,
    "SELECT m.id AS id_msg, 
            m.message, 
            m.message_categories, 
            m.message_media, 
            m.message_status, 
            m.send_to, 
            m.message_schedule, 
            m.user_id,
            u.id AS id_user,
            u.email,u.api_key 
    FROM messages AS m 
    INNER JOIN users AS u 
    ON m.user_id = u.email 
    WHERE message_status='waiting' 
    ORDER BY id_msg 
    ASC LIMIT $chunk"
);
$i = 0;
while ($data = $query->fetch_assoc()) {
    $schedule = strtotime($data['message_schedule']);
    if ($schedule < $now) {
        $number = $data['send_to'];
        $message = $data['message'];
        $messageCategories = $data['message_categories'];
        $file = $data['message_media'];
        $email = $data['user_id'];
        $id_msg = $data['id_msg'];
        if ($messageCategories === 'send-message' || $messageCategories === 'send-schedule' || $messageCategories === 'api-send-message' || $messageCategories === 'api-send-schedule' || $messageCategories === 'send-template') {
            $sendMSG = sendMSG($number, $message, $file);
            if ($sendMSG['status'] === 200) {
                $i++;
                $this_id = $data['id_msg'];
                $updateQuery = mysqli_query($conn, "UPDATE messages SET message_status = 'success', updated_at = '$timeZone' WHERE id='$this_id'");
            } else {
                $i++;
                $this_id = $data['id_msg'];
                $updateQuery = mysqli_query($conn, "UPDATE messages SET message_status = 'failed', updated_at = '$timeZone' WHERE id='$this_id'");
            }
        }
        if ($messageCategories === 'send-group' || $messageCategories === 'send-group-schedule' || $messageCategories === 'api-send-group' || $messageCategories === 'api-send-group-schedule' || $messageCategories === 'send-group-template') {
            $sendGroup = sendGroup($number, $message, $file);
            if ($sendGroup['status'] === 200) {
                $i++;
                $this_id = $data['id_msg'];
                $updateQuery = mysqli_query($conn, "UPDATE messages SET message_status = 'success', updated_at = '$timeZone' WHERE id='$this_id'");
            } else {
                $i++;
                $this_id = $data['id_msg'];
                $updateQuery = mysqli_query($conn, "UPDATE messages SET message_status = 'failed', updated_at = '$timeZone' WHERE id='$this_id'");
            }
        }
        $send_whatsapp = [
            'number' => $number,
            'message' => $message,
            'file' => $file,
            'category' => $messageCategories,
            'email' => $email,
        ];
        if ($sendMSG) {
            $status = $sendMSG['status'];
            $response_data = array_merge($sendMSG, ['data' => $send_whatsapp]);
            $response = json_encode($response_data);
        } elseif ($sendGroup) {
            $status = $sendGroup['status'];
            $response_data = array_merge($sendGroup, ['data' => $send_whatsapp]);
            $response = json_encode($response_data);
        } else {
            $status = 500;
            $response = "Internal Server Error: Something problems to connected server!";
        }
        $data = [
            "log" => 'send-whatsapp',
            "log_description" => $response,
            "log_status" => $status,
            "route" => $base_url . '/helpers/cronjob.php',
            "user_id" => $email,
            "massage_id" => $this_id
        ];
        $saveLog = saveLog($data);
        sleep(1);
    }
}
echo 'Success Send to ' . $i . ' Numbers';
