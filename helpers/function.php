<?php
include_once("connection.php");
session_start();

function get($param)
{
    global $conn;
    $d = isset($_GET[$param]) ? $_GET[$param] : null;
    $d = mysqli_real_escape_string($conn, $d);
    $d = filter_var($d, FILTER_SANITIZE_STRING);
    return $d;
}

function post($param)
{
    global $conn;
    $d = isset($_POST[$param]) ? $_POST[$param] : null;
    $d = mysqli_real_escape_string($conn, $d);
    $d = filter_var($d, FILTER_SANITIZE_STRING);
    return $d;
}

function login($email, $password)
{
    global $conn;
    $pwsh = sha1($password);
    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pwsh'");

    if (mysqli_num_rows($q)) {
        $_SESSION['login'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = getSingleValDB("users", "email", $email, "level");
        return true;
    } else {
        return false;
    }
}

function cekSession()
{
    $login = isset($_SESSION['login']) ? $_SESSION['login'] : null;
    if ($login == true) {
        return 1;
    } else {
        return 0;
    }
}

function sessios_email()
{
    return $_SESSION['email'];
}

function sessios_level()
{
    return $_SESSION['level'];
}

function getSingleValDB($table, $where, $param, $target)
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$where`='$param'");
    $row = mysqli_fetch_assoc($q);
    return $row[$target];
}

function countDB($table, $where = null, $param = null)
{
    global $conn;
    if ($where == null && $param == null) {
        $q = mysqli_query($conn, "SELECT * FROM `$table`");
    } else {
        $q = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$where`='$param'");
    }
    $row = mysqli_num_rows($q);
    return $row;
}

function countContactDB($table, $where = null, $param = null)
{
    global $conn;
    $email = $_SESSION['email'];
    if ($where == null && $param == null) {
        $q = mysqli_query($conn, "SELECT * FROM `$table` WHERE user_id='$email'");
    } else {
        $q = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$where`='$param' AND user_id='$email'");
    }
    $row = mysqli_num_rows($q);
    return $row;
}

function countAndDB($table, $where = null, $param = null, $where2 = null, $param2 = null)
{
    global $conn;
    if ($where2 == null && $param2 == null) {
        $q = mysqli_query($conn, "SELECT * FROM `$table`");
    } else {
        $q = mysqli_query($conn, "SELECT * FROM `$table` WHERE `$where`='$param' AND `$where2`='$param2'");
    }
    $row = mysqli_num_rows($q);
    return $row;
}

// function countPresentase()
// {
//     $a = countDB("pesan", "status" . "TERKIRIM");
//     $b = countDB("pesan");
//     if ($a > 0) {
//         return (countDB("pesan", "status", "TERKIRIM") / countDB("pesan"));
//     } else {
//         return 0;
//     }
// }

function getCategoryNumber($target_category)
{
    global $conn;
    $email = $_SESSION['email'];
    $q = mysqli_query($conn, "SELECT * FROM `contacts` WHERE `contact_category`='$target_category' AND user_id='$email' AND contact_type='PERSONAL'");
    $arr = [];

    while ($row = mysqli_fetch_assoc($q)) {
        array_push($arr, $row['contact_number']);
    }
    return $arr;
}

function getCategoryGroupID($target_category)
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `contacts` WHERE `contact_category`='$target_category' AND `contact_type`='GROUP'");
    $arr = [];

    while ($row = mysqli_fetch_assoc($q)) {
        array_push($arr, $row['contact_number']);
    }
    return $arr;
}

function getAllNumberandmessage()
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `contacts`");
    $arr = [];
    while ($row = mysqli_fetch_assoc($q)) {
        array_push($arr, $row['contact_number']);
    }
    return $arr;
}

function getLastID($table)
{
    global $conn;
    $q = mysqli_query($conn, "SELECT * FROM `$table` ORDER BY id DESC LIMIT 1");
    $row = mysqli_fetch_assoc($q);
    return $row['id'];
}

function url_wa()
{
    global $base_url;
    $url = getSingleValDB("setting", "id", "1", "url_gateway");
    return $url;
}

function api_key()
{
    global $conn;
    $email = $_SESSION['email'];
    $query = mysqli_query($conn, "SELECT api_key FROM users WHERE email ='$email'");
    $row = mysqli_fetch_assoc($query);
    return $row['api_key'];
}

function token()
{
    return getSingleValDB("setting", "id", "1", "token");
}

function redirect($target)
{
    echo '
    <script>
        window.location = "' . $target . '";
    </script>
    ';
    exit;
}

function toastr_set($status, $msg)
{
    $_SESSION['toastr'] = true;
    $_SESSION['toastr_status'] = $status;
    $_SESSION['toastr_msg'] = $msg;
}

function toastr_show()
{
    $toastr = isset($_SESSION['toastr']) ? $_SESSION['toastr'] : null;
    $toastr_s = isset($_SESSION['toastr_status']) ? $_SESSION['toastr_status'] : null;
    $toastr_m = isset($_SESSION['toastr_msg']) ? $_SESSION['toastr_msg'] : null;
    if ($toastr == true) {
        if ($toastr_s == "success") {
            echo "
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: '" . $toastr_m . "',
              confirmButtonText: 'OK'
            })
            ";
        }

        if ($toastr_s == "error") {
            echo "
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: '" . $toastr_m . "',
              confirmButtonText: 'OK'
            })
            
            ";
        }

        if ($toastr_s == "mixin-success") {
            echo "
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            })
            
            Toast.fire({
              icon: 'success',
              title: '" . $toastr_m . "'
            })
            ";
        }

        if ($toastr_s == "mixin-error") {
            echo "
            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
              didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            })
            
            Toast.fire({
              icon: 'error',
              title: '" . $toastr_m . "'
            })
            ";
        }

        unset($_SESSION['toastr']);
        unset($_SESSION['toastr_status']);
        unset($_SESSION['toastr_msg']);
    }
}

function sendMSG($number, $msg, $file)
{
    $url = url_wa() . 'send-message';
    $data = [
        "token" => token(),
        "number" => $number,
        "message" => $msg,
        "file" => $file
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10000);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function sendGroup($groupID, $message, $file)
{
    $url = url_wa() . 'send-group-message';
    $data = [
        "token" => token(),
        'id_group' => $groupID,
        'message' => $message,
        "file" => $file
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function check_numbers($key, $number)
{
    $url = url_wa() . 'check-numbers?';
    $url .= "token=" . token() . '&api_key=' . $key . '&number=' . $number;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    return json_decode($result, true);
}

// function getContacts($key)
// {
//     $api_key = $key;
//     $url = url_wa() . 'contacts?';
//     $url .= "token=" . token() . '&api_key=' . $api_key;

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
//     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//     $result = curl_exec($ch);
//     curl_close($ch);
//     return json_decode($result, true);
//     // $err = curl_error($ch);
//     // if ($err) {
//     //     echo "cURL Error #:" . $err;
//     // } else {
//     //     echo $result;
//     // }
// }

function getGroups($key)
{
    $api_key = $key;
    $url = url_wa() . 'groups?';
    $url .= "token=" . token() . '&api_key=' . $api_key;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
    // $err = curl_error($ch);
    // if ($err) {
    //     echo "cURL Error #:" . $err;
    // } else {
    //     echo $result;
    // }
}

function logout_session()
{
    $url = url_wa() . 'logout-session';
    $data = [
        "token" => token(),
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function updateStatusMSG($id, $a)
{
    global $conn;
    $q = mysqli_query($conn, "UPDATE pesan SET status='$a' WHERE id='$id'");
}

function base64upload($base64_string, $output_file)
{
    $file = fopen($output_file, "wb");

    $data = explode(',', $base64_string);

    fwrite($file, base64_decode($data[1]));
    fclose($file);

    return $output_file;
}

function sendApiUrl()
{
    global $base_url;
    return $base_url . "api/send.php?key=" . getSingleValDB("setting", "id", "1", "token");
}

function csrf_randomhex()
{
    return bin2hex(random_bytes(35));
}

function csrf_create()
{
    if (isset($_SESSION['_token'])) {
        return $_SESSION['_token'];
    }
    $token = csrf_randomhex();
    $_SESSION['_token'] = $token;
    return $token;
}

function csrf_input()
{
    $token = csrf_create();
    return '<input type="hidden" name="_token" class="form-control" value="' . $token . '">';
}

function csrf_validate()
{
    $_SESSION['token_expired'] = time() + 3600;
    if (!isset($_POST["_token"]) || !isset($_SESSION["_token"])) {
        toastr_set("mixin-error", "Token csrf not set!");
        exit("Token csrf not set!");
    }
    if ($_POST["_token"] == $_SESSION["_token"]) {
        if (time() >= $_SESSION["token_expired"]) {
            toastr_set("mixin-error", "Token csrf expired, reload the form!");
            exit("Token csrf expired, reload the form!");
        }
        unset($_SESSION["_token"]);
        unset($_SESSION["token_expired"]);
    } else {
        toastr_set("mixin-error", "Invalid token csrf!");
        exit("Invalid token csrf!");
    }
}

function getExtention($param)
{
    return strtolower(substr($param, strripos($param, '.') + 1));
}

function saveLog($data)
{
    global $conn, $timeZone, $result;
    $ip_addr = $_SERVER['REMOTE_ADDR'];
    $os = getOS();
    $browser = getBrowser();
    if ($data['message_id'] || $data['user_id']) {
        mysqli_query($conn, "INSERT INTO logs(`log`, `log_description`, `log_status`, `ip_addr`, `route`, `system_operation`, `browser`, `user_id`, `message_id`, `created_at`)
        VALUES
        ('" . $data['log'] . "', '" . $data['log_description'] . "', '" . $data['log_status'] . "', '$ip_addr', '" . $data['route'] . "', '$os', '$browser', '" . $data['user_id'] . "', '" . $data['massage_id'] . "', '$timeZone')");
    } else {
        $public = "public";
        mysqli_query($conn, "INSERT INTO logs(`log`, `log_description`, `log_status`, `ip_addr`, `route`, `system_operation`, `browser`, `user_id`, `message_id`, `created_at`)
            VALUES
            ('" . $data['log'] . "', '" . $data['log_description'] . "', '" . $data['log_status'] . "', '$ip_addr', '" . $data['route'] . "', '$os', '$browser', '$public', null, '$timeZone')");
    }
    // mysql_error_handling($result);
}

function encryptor($action, $string)
{
    $output = false;
    $encrypt_method = "AES-256-CBC";

    $secret_key = '';
    $secret_iv = '';

    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function mysql_error_handling($result)
{
    global $conn;
    if (!$result) {
        die('Query Error : ' . mysqli_errno($conn) .
            ' - ' . mysqli_error($conn));
    }
}

function getOS()
{

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $os_platform =   "Bilinmeyen İşletim Sistemi";
    $os_array =   array(
        '/windows nt 10/i'      =>  'Windows 10',
        '/windows nt 6.3/i'     =>  'Windows 8.1',
        '/windows nt 6.2/i'     =>  'Windows 8',
        '/windows nt 6.1/i'     =>  'Windows 7',
        '/windows nt 6.0/i'     =>  'Windows Vista',
        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
        '/windows nt 5.1/i'     =>  'Windows XP',
        '/windows xp/i'         =>  'Windows XP',
        '/windows nt 5.0/i'     =>  'Windows 2000',
        '/windows me/i'         =>  'Windows ME',
        '/win98/i'              =>  'Windows 98',
        '/win95/i'              =>  'Windows 95',
        '/win16/i'              =>  'Windows 3.11',
        '/macintosh|mac os x/i' =>  'Mac OS X',
        '/mac_powerpc/i'        =>  'Mac OS 9',
        '/linux/i'              =>  'Linux',
        '/ubuntu/i'             =>  'Ubuntu',
        '/iphone/i'             =>  'iPhone',
        '/ipod/i'               =>  'iPod',
        '/ipad/i'               =>  'iPad',
        '/android/i'            =>  'Android',
        '/blackberry/i'         =>  'BlackBerry',
        '/webos/i'              =>  'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }
    return $os_platform;
}

/**
 * Kullanicinin kullandigi internet tarayici bilgisini alir.
 * 
 * @since 2.0
 */
function getBrowser()
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $browser        = "Bilinmeyen Tarayıcı";
    $browser_array  = array(
        '/msie/i'       =>  'Internet Explorer',
        '/firefox/i'    =>  'Firefox',
        '/safari/i'     =>  'Safari',
        '/chrome/i'     =>  'Chrome',
        '/edge/i'       =>  'Edge',
        '/opera/i'      =>  'Opera',
        '/netscape/i'   =>  'Netscape',
        '/maxthon/i'    =>  'Maxthon',
        '/konqueror/i'  =>  'Konqueror',
        '/mobile/i'     =>  'Handheld Browser'
    );

    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
        }
    }
    return $browser;
}
