<?php
include('../backend/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['anh'])) {
    $nguoi_tao = $conn->real_escape_string($_POST['nguoi_tao']);
    $ghichu = $conn->real_escape_string($_POST['ghichu']);
    $file = $_FILES['anh'];
    $ten_file = time() . '_' . basename($file['name']);
    $target_dir = __DIR__ . '/../uploads/';
    $target_path = $target_dir . $ten_file;


    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $conn->query("INSERT INTO hoadon_anh (ten_file, ngay_tao, nguoi_tao, ghichu)
                      VALUES ('$ten_file', NOW(), '$nguoi_tao', '$ghichu')");
        header("Location: ../frontend/danhsach_hoadon.php");
    } else {
        echo "Upload error: " . $_FILES['anh']['error'];
    }
}
