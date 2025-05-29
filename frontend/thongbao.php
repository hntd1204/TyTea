<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Xử lý thêm thông báo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_notification'])) {
    $message = $conn->real_escape_string($_POST['message']);
    $sql = "INSERT INTO notifications (message) VALUES ('$message')";
    $conn->query($sql);
    header("Location: thongbao.php");  // Redirect lại sau khi thêm
    exit;
}

// Xử lý xóa thông báo
if (isset($_GET['delete'])) {
    $id_delete = (int)$_GET['delete'];
    $conn->query("DELETE FROM notifications WHERE id = $id_delete");
    header("Location: thongbao.php");
    exit;
}

// Lấy danh sách thông báo
$notifications = [];
$result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
?>

<!-- Nội dung chính -->
<div id="page-content-wrapper" class="p-4">
    <h2 class="text-center text-success font-weight-bold">Quản lý Thông Báo</h2>

    <!-- Form thêm thông báo -->
    <form method="POST" class="mb-4">
        <div class="form-group">
            <label for="message">Nội dung thông báo:</label>
            <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" name="add_notification" class="btn btn-primary">Thêm thông báo</button>
    </form>

    <!-- Danh sách thông báo -->
    <h3 class="mt-4">Danh sách thông báo</h3>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notifications as $notification): ?>
                    <tr>
                        <td><?= htmlspecialchars($notification['message']) ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($notification['created_at'])) ?></td>
                        <td>
                            <!-- Nút xóa -->
                            <a href="thongbao.php?delete=<?= $notification['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn chắc chắn muốn xóa thông báo này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('layout/footer.php'); ?>