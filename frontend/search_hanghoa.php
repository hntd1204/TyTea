<?php
include('../backend/db_connect.php');

$term = $_GET['term'] ?? '';
$term = $conn->real_escape_string($term);

$sql = "SELECT mahang, tenhang FROM hanghoa WHERE mahang LIKE '%$term%' OR tenhang LIKE '%$term%' ORDER BY tenhang LIMIT 20";
$res = $conn->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        'id' => $row['mahang'],
        'text' => $row['mahang'] . ' - ' . $row['tenhang']
    ];
}

header('Content-Type: application/json');
echo json_encode(['results' => $data]);
