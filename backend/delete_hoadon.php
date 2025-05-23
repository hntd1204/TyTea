<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Xoá chi tiết trước
    $conn->query("DELETE FROM chitiethoadon WHERE hoadon_id = $id");

    // Rồi xoá hóa đơn
    $conn->query("DELETE FROM hoadon WHERE id = $id");
}

header("Location: ../frontend/hoadon.php");
exit;