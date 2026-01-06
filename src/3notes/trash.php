<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];

    //Lấy danh sách ghi chú đã xóa
    $sql = "SELECT DISTINCT n.*, GROUP_CONCAT(t.tag_name SEPARATOR ', ') as tags 
            FROM notes n 
            LEFT JOIN note_tags nt ON n.id = nt.note_id
            LEFT JOIN tags t ON nt.tag_id = t.id
            WHERE n.user_id = $user_id AND n.is_deleted = 1
            GROUP BY n.id ORDER BY n.updated_at DESC";

    $result = $conn -> query($sql);
?>
<!DOCTYPE html>
<head>
    <meta charset = "utf-8">
    <title>Thùng rác</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../5assets/style.css">
</head>
</body>
    <nav class = "navbar navbar-expand-lg navbar-dark bg-primary">
        <div class = "container-fluid">
            <a class = "navbar-brand" href = "#"> 📒Ghi chú của tôi </a>
            <div class = "navbar-nav ms-auto">
                <span class = "navbar-text"> Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?> </span>
                <a href = "../2auth/logout.php" class = "btn btn-outline-dark">🚪Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class = "container mt-4">
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'restored'): ?>
                <div class = "alert alert-success alert-dismissible fade show" role = "alert">
                    Khôi phục ghi chú thành công! 
                    <button type = "button" class = "btn-close" data-bs-dismiss = "alert"> </button>
                </div>

            <?php elseif($_GET['msg'] == 'deleted'): ?>
                <div class = "alert alert-success alert-dismissible fade show" role = "alert">
                    Xóa vĩnh viễn thành công! 
                    <button type = "button" class = "btn-close" data-bs-dismiss = "alert"> </button>
                </div>

            <?php elseif($_GET['msg'] == 'error'): ?>
                <div class = "alert alert-success alert-dismissible fade show" role = "alert">
                    Có lỗi xảy ra. Vui lòng thử lại!
                    </button type = "button" class = "btn-close" data-bs-dismiss = "alert"> </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row mb-2">
            <div class="col-md-8">
                <h2>🗑️ Thùng rác</h2>
                <p class="text-muted">Ghi chú đã xóa sẽ được lưu tại đây. Bạn có thể khôi phục hoặc xóa vĩnh viễn.</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="list.php" class="btn btn-secondary"> ← Quay lại danh sách</a>
            </div>
        </div>

         <!-- Danh sách ghi chú đã xóa -->
        <div class="row">
            <?php if($result->num_rows > 0): ?>
                <?php while($note = $result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-warning">
                            <div class="card-body">
                                <h5 class="card-title text-muted">
                                    <del><?php echo htmlspecialchars($note['title']); ?></del>
                                </h5>
                                <p class="card-text text-muted">
                                    <?php echo nl2br(htmlspecialchars(substr($note['content'], 0, 150))); ?>
                                    <?php echo strlen($note['content']) > 150 ? '...' : ''; ?>
                                </p>
                                <?php if($note['tags']): ?>
                                    <p class="text-muted small">
                                        <strong>Nhãn:</strong> <?php echo htmlspecialchars($note['tags']); ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-muted small">Xóa lúc: <?php echo $note['updated_at']; ?></p>
                            </div>
                            <div class="card-footer bg-light">
                                <a href="undelete.php?id=<?php echo $note['id']; ?>" 
                                class="btn btn-sm btn-success"
                                onclick="return confirm('Khôi phục ghi chú này?')">
                                    ↩️ Khôi phục
                                </a>
                                <a href="permanent_delete.php?id=<?php echo $note['id']; ?>" 
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn muốn xóa vĩnh viễn ghi chú này? Sau khi xóa sẽ không thể hoàn tác!')">
                                    🗑️ Xóa vĩnh viễn
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <h4>Thùng rác trống</h4>
                        <p>Không có ghi chú nào trong thùng rác.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../5assets/script.js"></script>
</body>
</html>