<?php
$host = "localhost";
$username = "root";
$password = "";
$db = "wa_db";
error_reporting(0);
$conn = mysqli_connect($host, $username, $password, $db) or die("Field connection database");

$base_url = "http://127.0.0.1/whatsapp-gateway/";
date_default_timezone_set('Asia/Jakarta');
$timeZone = date("Y-m-d H:i:s");
