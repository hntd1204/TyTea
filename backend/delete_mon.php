<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $conn->query("DELETE FROM menu WHERE id = $id");
}

header("Location: ../frontend/menu.php");
exit;