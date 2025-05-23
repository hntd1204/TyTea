<?php
session_start();
include('../backend/db_connect.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin - TyTea 🍵</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-box shadow-lg">
            <div class="text-center mb-3">
                <img src="../img/powder.png" width="80" alt="matcha">
                <h4 class="text-success mt-2 font-weight-bold">TyTea Admin</h4>
                <p class="text-muted">Hệ thống quản lý trà sữa 🍵</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Tài khoản</label>
                    <input type="text" name="username" class="form-control" placeholder="Nhập tài khoản..." required>
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                </div>
                <button type="submit" class="btn btn-success btn-block">Đăng nhập</button>
            </form>
        </div>
    </div>
</body>

</html>