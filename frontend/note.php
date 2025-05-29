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
    <h2 class="mb-4">Note</h2>

    <!-- Form thêm ghi chú -->
    <form method="POST" class="mb-4">
        <div class="form-group">
            <label for="title">Tiêu đề:</label>
            <input name="title" id="title" class="form-control" type="text" required>
        </div>
        <div class="form-group">
            <label for="note">Nội dung:</label>
            <textarea name="note" id="note" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Lưu</button>
    </form>

    <!-- Hiển thị danh sách ghi chú -->
    <h3 class="mt-4">Danh sách Note của bạn</h3>
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
                        <a href="note.php?edit=<?= $note['id'] ?>" class="btn btn-sm btn-warning" data-toggle="modal"
                            data-target="#editModal<?= $note['id'] ?>">Sửa</a>
                        <!-- Nút xóa -->
                        <a href="note.php?delete=<?= $note['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                        <!-- Nút xem chi tiết -->
                        <a href="note.php?view=<?= $note['id'] ?>" class="btn btn-sm btn-info" data-toggle="modal"
                            data-target="#viewModal<?= $note['id'] ?>">Xem chi tiết</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('layout/footer.php'); ?>

<!-- Modal sửa ghi chú -->
<?php foreach ($notes as $note): ?>
<div class="modal fade" id="editModal<?= $note['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Sửa ghi chú</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Tiêu đề:</label>
                        <input name="title" class="form-control" type="text"
                            value="<?= htmlspecialchars($note['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="note">Nội dung:</label>
                        <textarea name="note" class="form-control" rows="4"
                            required><?= htmlspecialchars($note['content']) ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit_note" class="btn btn-primary">Cập nhật</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Modal xem chi tiết ghi chú -->
<?php if (isset($_GET['view'])): ?>
<?php
    $id_view = (int)$_GET['view'];
    $view_note = $conn->query("SELECT * FROM notes WHERE id = $id_view")->fetch_assoc();
    ?>
<div class="modal fade" id="viewModal<?= $view_note['id'] ?>" tabindex="-1" role="dialog"
    aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Chi tiết Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="font-weight-bold"><?= htmlspecialchars($view_note['title']) ?></h5>
                <p><?= nl2br(htmlspecialchars($view_note['content'])) ?></p>
                <small class="text-muted"><?= date('d-m-Y H:i', strtotime($view_note['created_at'])) ?></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Thư viện JS Bootstrap 4 -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Đóng modal khi nhấn vào nút Đóng
    $('[data-dismiss="modal"]').on('click', function() {
        $('.modal').modal('hide');
    });
});
</script>