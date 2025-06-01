<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Đặt múi giờ cho PHP (có thể thay đổi theo nhu cầu)
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Số ghi chú hiển thị mỗi trang
$limit = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// Xử lý thêm ghi chú
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['note'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $note = $conn->real_escape_string($_POST['note']);
    $updated_at = date('Y-m-d H:i:s'); // Ngày giờ hiện tại cho cập nhật

    // Lưu ghi chú vào CSDL
    if (isset($_POST['id_update'])) {
        // Nếu đang chỉnh sửa, cập nhật ghi chú
        $id_update = (int)$_POST['id_update'];
        $sql = "UPDATE notes SET title = '$title', content = '$note', updated_at = '$updated_at' WHERE id = $id_update";
        $conn->query($sql);
        header("Location: note.php");  // Chuyển hướng lại trang sau khi cập nhật
        exit;
    } else {
        // Thêm ghi chú mới
        $sql = "INSERT INTO notes (title, content, created_at, updated_at) 
                VALUES ('$title', '$note', current_timestamp(), '$updated_at')";
        $conn->query($sql);
        header("Location: note.php");  // Chuyển hướng lại trang sau khi lưu
        exit;
    }
}

// Xử lý chỉnh sửa ghi chú
$edit_note = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $edit_note = $conn->query("SELECT * FROM notes WHERE id = $id_edit")->fetch_assoc();
}

// Xử lý xóa ghi chú
if (isset($_GET['delete'])) {
    $id_delete = (int)$_GET['delete'];
    $conn->query("DELETE FROM notes WHERE id = $id_delete");
    header("Location: note.php");  // Chuyển hướng lại trang sau khi xóa
    exit;
}

// Lấy danh sách ghi chú đã lưu (chỉ của bạn)
$notes = [];
$result = $conn->query("SELECT * FROM notes ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}

// Tính tổng số ghi chú để tính số trang
$total_result = $conn->query("SELECT COUNT(*) AS total FROM notes");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Ghi Chú Cá Nhân</h2>

    <!-- Form để thêm hoặc chỉnh sửa ghi chú -->
    <form method="POST" class="mb-4">
        <div class="form-group">
            <label for="title">Tiêu đề ghi chú:</label>
            <input name="title" id="title" class="form-control" type="text" required
                value="<?= htmlspecialchars($edit_note['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="note">Nội dung ghi chú:</label>
            <textarea name="note" id="note" class="form-control" rows="4"
                required><?= htmlspecialchars($edit_note['content'] ?? '') ?></textarea>
        </div>
        <!-- Include the ID of the note to update -->
        <?php if ($edit_note): ?>
            <input type="hidden" name="id_update" value="<?= $edit_note['id'] ?>">
        <?php endif; ?>
        <button type="submit" name="edit_note"
            class="btn btn-primary"><?= isset($edit_note) ? 'Cập nhật' : 'Lưu Ghi Chú' ?></button>
    </form>

    <!-- Hiển thị danh sách ghi chú -->
    <h3 class="mt-4">Danh sách ghi chú của bạn</h3>
    <div class="accordion" id="accordionExample">
        <?php foreach ($notes as $index => $note): ?>
            <div class="card">
                <div class="card-header" id="heading<?= $note['id'] ?>">
                    <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse"
                            data-target="#collapse<?= $note['id'] ?>" aria-expanded="true"
                            aria-controls="collapse<?= $note['id'] ?>">
                            <?= htmlspecialchars($note['title']) ?>
                        </button>
                    </h5>
                </div>

                <div id="collapse<?= $note['id'] ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>"
                    aria-labelledby="heading<?= $note['id'] ?>" data-parent="#accordionExample">
                    <div class="card-body">
                        <p><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                        <p><strong>Ngày tạo:</strong> <?= date('d-m-Y H:i', strtotime($note['created_at'])) ?></p>
                        <p><strong>Ngày cập nhật:</strong> <?= date('d-m-Y H:i', strtotime($note['updated_at'])) ?></p>
                        <a href="note.php?edit=<?= $note['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <a href="note.php?delete=<?= $note['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Phân trang -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Include Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include('layout/footer.php'); ?>