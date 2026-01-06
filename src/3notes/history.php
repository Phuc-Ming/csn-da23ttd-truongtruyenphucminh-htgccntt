<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];
    $note_id = isset($_GET['id']) ? $_GET['id'] : 0;

    //Kiểm tra ghi chú có thuộc user ko
    $sql = "SELECT title FROM notes WHERE id = $note_id AND user_id = $user_id";
    $result = $conn -> query($sql);

    if($result -> num_rows == 0) {
        header("Location: list.php");
        exit();
    }

    $note = $result -> fetch_assoc();

    //Lấy lịch sử chỉnh sửa
    $sql = "SELECT * FROM note_history WHERE note_id = $note_id ORDER BY edited_at DESC";
    $history_result = $conn ->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset = "utf-8">
    <!--Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../5assets/style.css">
</head>
<body>
    <nav class = "navbar navbar-expand-lg navbar-dark bg-primary">
        <div class = "container-fluid">
            <a class = "navbar-brand" href = "#"> 📒Ghi chú của tôi </a>
            <div class = "navbar-nav ms-auto">
                <span class = "navbar-text"> Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?> </span>
                <a href = "../2auth/logout.php" class = "btn btn-outline-dark">🚪Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Lịch sử chỉnh sửa: <?php echo htmlspecialchars($note['title']); ?></h2>
        <a href="list.php" class="btn btn-secondary mb-3">⬅ Quay lại</a>
        
        <table class="table table-hover">
            <thead>
                <tr class="table-warning">
                    <th>ID</th>
                    <th>Nội dung cũ</th>
                    <th>Thời gian chỉnh sửa</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if($history_result->num_rows > 0): ?>
                    <?php while($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo nl2br(htmlspecialchars(substr($row['old_content'], 0, 100))); ?>...</td>
                            <td><?php echo $row['edited_at']; ?></td>
                            <td>
                                <a href="restore.php?note_id=<?php echo $note_id; ?>&history_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success"
                                onclick="return confirm('Bạn có muốn khôi phục phiên bản này?')">
                                    Khôi phục
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Chưa có lịch sử chỉnh sửa</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>