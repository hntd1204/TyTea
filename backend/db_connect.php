<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // hoặc '123456' tùy cấu hình máy
$dbname = 'qlbh';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}