<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Xử lý thêm ghi chú
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['note'])) {
    $title = $conn->real_escape_string($_POST['title']);  // Tiêu đề ghi chú
    $note = $conn->real_escape_string($_POST['note']);  // Nội dung ghi chú

    // Lưu ghi chú vào CSDL
    $sql = "INSERT INTO notes (title, content) VALUES ('$title', '$note')";
    $conn->query($sql);
    header("Location: note.php");  // Redirect lại trang sau khi lưu
    exit;
}

// Lấy danh sách ghi chú đã lưu (chỉ của bạn)
$notes = [];
$result = $conn->query("SELECT * FROM notes ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}

// Xử lý xóa ghi chú
if (isset($_GET['delete'])) {
    $id_delete = (int)$_GET['delete'];
    $conn->query("DELETE FROM notes WHERE id = $id_delete");
    header("Location: note.php");  // Redirect lại trang sau khi xóa
    exit;
}

// Xử lý sửa ghi chú
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $edit_note = $conn->query("SELECT * FROM notes WHERE id = $id_edit")->fetch_assoc();
    // Nếu có sự sửa đổi, thực hiện cập nhật trong cơ sở dữ liệu
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_note'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $note = $conn->real_escape_string($_POST['note']);

        $sql = "UPDATE notes SET title = '$title', content = '$note' WHERE id = $id_edit";
        $conn->query($sql);
        header("Location: note.php");  // Redirect lại trang sau khi sửa
        exit;
    }
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Ghi Chú Cá Nhân</h2>

    <!-- Form thêm ghi chú -->
    <form method="POST" class="mb-4">
        <div class="form-group">
            <label for="title">Tiêu đề ghi chú:</label>
            <input name="title" id="title" class="form-control" type="text" required>
        </div>
        <div class="form-group">
            <label for="note">Nội dung ghi chú:</label>
            <textarea name="note" id="note" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Lưu Ghi Chú</button>
    </form>

    <!-- Hiển thị danh sách ghi chú -->
    <h3 class="mt-4">Danh sách ghi chú của bạn</h3>
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
                <?php foreach ($notes as $note): ?>
                <tr>
                    <td><?= htmlspecialchars($note['title']) ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($note['created_at'])) ?></td>
                    <td>
                        <!-- Nút sửa -->
                        <a href="note.php?edit=<?= $note['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                        <!-- Nút xóa -->
                        <a href="note.php?delete=<?= $note['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                        <!-- Nút xem chi tiết -->
                        <a href="note.php?view=<?= $note['id'] ?>" class="btn btn-sm btn-info"
                            id="viewDetail<?= $note['id'] ?>">Xem chi tiết</a>
                    </td>
                </tr>
                <tr id="detail<?= $note['id'] ?>" style="display: none;">
                    <td colspan="3">
                        <div class="alert alert-info">
                            <strong>Chi tiết ghi chú:</strong>
                            <p><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('layout/footer.php'); ?>

<!-- Thư viện JS Bootstrap 4 -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Khi nhấn nút "Xem chi tiết", hiển thị nội dung ghi chú
    $('[id^="viewDetail"]').on('click', function() {
        var id = $(this).attr('href').split('=')[1]; // Lấy id ghi chú từ URL
        var detailRow = $('#detail' + id);

        // Kiểm tra xem chi tiết đã được mở chưa, nếu chưa thì mở, nếu đã mở thì đóng
        if (detailRow.is(':visible')) {
            detailRow.hide(); // Ẩn chi tiết
        } else {
            detailRow.show(); // Hiển thị chi tiết
        }
        return false; // Ngăn chặn hành vi mặc định của link
    });
});
</script>