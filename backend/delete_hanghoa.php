<?php
include('db_connect.php');

$id = $_GET['id'] ?? 0;
if ($id) {
    $conn->query("DELETE FROM hanghoa WHERE id = $id");
}
header("Location: ../frontend/hanghoa.php");
exit;
