<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';
    
    $user_id = $_SESSION['user_id'];

    //Lấy danh sách nhãn 
    $sql = "SELECT * FROM tags WHERE user_id = $user_id ORDER BY created_at DESC";
    $result = $conn -> query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset = "utf-8">
    <title> Danh sách nhãn </title>
    <!--Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
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

    <div class = "container mt-5">
        <h2 class = "text-center"> Danh sách các nhãn </h2>
        <a href="create_tag.php" class="btn btn-success mb-4">➕Tạo nhãn mới</a>
        <a href="../3notes/list.php" class="btn btn-secondary mb-4">⬅ Quay lại</a>

        <table class = "table table-hover">
            <thead>
                <tr class = "table-danger">
                    <th>Id nhãn</th>                   
                    <th>Tên nhãn</th>
                    <th>Thời gian được tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result -> num_rows > 0): ?>
                    <?php while($row = $result -> fetch_assoc()): ?>
                        <tr>
                            <td> <?php echo $row['id']; ?> </td>                          
                            <td> <?php echo htmlspecialchars($row['tag_name']); ?> </td>
                            <td> <?php echo $row['created_at']; ?> </td>
                            <td>
                                <a href = "delete_tag.php?id= <?php echo $row['id']; ?>" class = "btn btn-sm btn-danger"
                                onclick = "return confirm('Xóa nhãn này?')"> Xóa </a>
                            </td>
                        </tr>                   
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan = "4" class="text-center"> Không có nhãn nào </td> 
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>