<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];

    //Lấy tham số tìm kiếm và lọc ghi chú
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $tag_filter = isset($_GET['tag']) ? intval($_GET['tag']) : 0;

    //Câu truy vấn 
   $sql = "SELECT DISTINCT n.*, GROUP_CONCAT(t.tag_name SEPARATOR ', ') as tags 
        FROM notes n 
        LEFT JOIN note_tags nt ON n.id = nt.note_id 
        LEFT JOIN tags t ON nt.tag_id = t.id 
        WHERE n.user_id = $user_id AND n.is_deleted = 0";

    // Thêm điều kiện tìm kiếm
    if ($search) {
        $search = mysqli_real_escape_string($conn, $search);
        $sql .= " AND (n.title LIKE '%$search%' OR n.content LIKE '%$search%')";
    }

    // Thêm điều kiện lọc theo tag 
    if ($tag_filter) {
        $sql .= " AND n.id IN (SELECT note_id FROM note_tags WHERE tag_id = $tag_filter)";
    }

    $sql .= " GROUP BY n.id ORDER BY n.updated_at DESC";

    $result = $conn -> query($sql);

    //Lấy danh sách tags của users
    $tags_sql = "SELECT * FROM tags WHERE user_id = $user_id ORDER BY tag_name";
    $tags_result = $conn -> query($tags_sql);

    // Thống kê nhanh
    $stats = [];

    // Tổng số ghi chú
    $total_notes_sql = "SELECT COUNT(*) as total FROM notes WHERE user_id = $user_id AND is_deleted = 0";
    $stats['total_notes'] = $conn->query($total_notes_sql)->fetch_assoc()['total'];

    // Ghi chú hôm nay
    $today_notes_sql = "SELECT COUNT(*) as today FROM notes WHERE user_id = $user_id AND is_deleted = 0 AND DATE(created_at) = CURDATE()";
    $stats['today_notes'] = $conn->query($today_notes_sql)->fetch_assoc()['today'];

    // Ghi chú tuần này
    $week_notes_sql = "SELECT COUNT(*) as week FROM notes WHERE user_id = $user_id AND is_deleted = 0 AND YEARWEEK(created_at) = YEARWEEK(NOW())";
    $stats['week_notes'] = $conn->query($week_notes_sql)->fetch_assoc()['week'];

    // Tổng số tags
    $total_tags_sql = "SELECT COUNT(*) as total FROM tags WHERE user_id = $user_id";
    $stats['total_tags'] = $conn->query($total_tags_sql)->fetch_assoc()['total'];

    // Ghi chú trong thùng rác
    $trash_notes_sql = "SELECT COUNT(*) as trash FROM notes WHERE user_id = $user_id AND is_deleted = 1";
    $stats['trash_notes'] = $conn->query($trash_notes_sql)->fetch_assoc()['trash'];
?>

<!DOCTYPE html>
<html lang = "vi">
<head>
    <meta charset = "utf-8">
    <title> Danh sách ghi chú </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../5assets/style.css">
</head>
<body>
    <nav class = "navbar navbar-expand-lg navbar-dark bg-primary">
        <div class = "container-fluid">
            <a class = "navbar-brand" href = "#"> 📒Ghi chú của tôi </a>
            <div class = "navbar-nav ms-auto">
                <a href = "create.php" class = "btn btn-success"> ➕Tạo ghi chú mới </a>
                <a href= "../4tags/list_tags.php" class = "btn btn-info"> Quản lý nhãn dán </a>
                <a href= "trash.php" class="btn btn-warning">🗑️Thùng rác</a>
                <span class = "navbar-text"> Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?> </span>
                <a href = "../2auth/logout.php" class = "btn btn-outline-dark">🚪Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class = "container mt-4">
        <?php if(isset($_GET['msg'])): ?>
            <?php if($_GET['msg'] == 'restored'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Khôi phục ghi chú thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif($_GET['msg'] == 'moved_to_trash'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Ghi chú đã được chuyển vào thùng rác. <a href="trash.php" class="alert-link">Xem thùng rác</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class = "row mb-2">
            <div class = "col-md-6">
                <h2>📋Danh sách ghi chú </h2>
            </div>
        </div>

        <!-- Tìm Kiếm Nhãn-->
        <div class = "mt-4">
            <div class = "card-body">
                <form method = "get" action = "" class = "row">
                    <div class = "col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="🔍Đang tìm kiếm ghi chú..." value = "<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class = "col-md-4">
                        <select name = "tag" class = "form-select">
                            <option value = "0"> Tất cả các nhãn </option>
                            <?php while ($tag = $tags_result -> fetch_assoc()): ?>
                                <option value = "<?php echo $tag['id']; ?>" <?php echo $tag_filter == $tag['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tag['tag_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class = "col-md-3">
                        <button type = "submit" class = "btn btn-primary"> Lọc </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách nhãn-->
        <div class = "mt-3">
            <div class = "row">
                <div class = "col-md-8">
                    <?php if($result -> num_rows > 0): ?>
                        <?php while($note = $result -> fetch_assoc()): ?>
                            <div class = "card mt-5">
                                <div class = "card-body">
                                    <h4 class = "card-title"> <?php echo htmlspecialchars($note['title']); ?> </h4>
                                    <p class = "card-text"> <?php echo nl2br(htmlspecialchars(substr($note['content'], 0, 150))); ?> <?php echo strlen($note['content']) > 150 ? '...' : ''; ?>
                                    <?php if($note['tags']): ?>
                                        <p class = "text-muted small">
                                            <strong> Nhãn: </strong> <?php echo htmlspecialchars($note['tags']); ?>    
                                        </p>
                                    <?php endif; ?>
                                    <p> Cập nhật: <?php echo $note['updated_at']; ?> </p>
                                </div>
                                <div class = "card-footer">
                                    <a href="edit.php? id= <?php echo $note['id']; ?>" class="btn btn-sm btn-warning col-md-2">Sửa</a>
                                    <a href="delete.php? id= <?php echo $note['id']; ?>" class="btn btn-sm btn-danger col-md-2" onclick="return confirm('Bạn có chắc xóa ghi chú này?')">Xóa</a>
                                    <a href="history.php? id= <?php echo $note['id']; ?>" class="btn btn-sm btn-info col-md-2">Lịch sử</a>
                                    <a href="tag_assign.php? id= <?php echo $note['id']; ?>" class="btn btn-sm btn-secondary col-md-2">Gán nhãn</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <div class = "col-12">
                        <div class = "alert alert-info text-center">
                            Không có ghi chú nào. <a href = "create.php"> Tạo ghi chú đầu tiên </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                 <!--Thống kê-->
                <div class="col-md-4">
                    <div class="card mb-3 sidebar-card mt-5">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">📊 Thống kê ghi chú</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="stats-box">
                                        <div class="stats-number text-primary"><?php echo $stats['total_notes']; ?></div>
                                        <div class="stats-label">Tổng ghi chú</div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stats-box">
                                        <div class="stats-number text-success"><?php echo $stats['today_notes']; ?></div>
                                        <div class="stats-label">Hôm nay</div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stats-box">
                                        <div class="stats-number text-info"><?php echo $stats['week_notes']; ?></div>
                                        <div class="stats-label">Tuần này</div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stats-box">
                                        <div class="stats-number text-warning"><?php echo $stats['total_tags']; ?></div>
                                        <div class="stats-label">Tổng nhãn</div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if($stats['trash_notes'] > 0): ?>
                            <div class="alert alert-warning py-2 mb-0">
                                <small>🗑️ <strong><?php echo $stats['trash_notes']; ?></strong> ghi chú trong thùng rác</small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>        
    <!--Bootstrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
