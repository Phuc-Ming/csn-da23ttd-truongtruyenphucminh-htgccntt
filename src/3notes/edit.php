<?php
    session_start();

    include '../1config/connect.php';
    include '../2auth/require_login.php';
    
    $user_id = $_SESSION['user_id'];
    $note_id = isset($_GET['id']) ? $_GET['id'] : 0;
    $success = "";
    $error = "";

    //Lấy thông tin ghi chú
    $sql = "SELECT * FROM notes WHERE id = $note_id AND user_id = $user_id AND is_deleted = 0";
    $result = $conn -> query ($sql);

    if ($result -> num_rows == 0) {
        header ("Location: list.php");
        exit();
    }

    $note = $result -> fetch_assoc();

    if($_SERVER ['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        
        if(empty($title) || empty($content)) {
            $error = "Vui lòng điền đầy đủ thông tin!";
        } else {
            $title = mysqli_real_escape_string($conn, $title);
            $content = mysqli_real_escape_string($conn, $content);
            $old_content = mysqli_real_escape_string($conn, $note['content']);

            //Lưu lịch sử
            $sql = "INSERT INTO note_history (note_id, old_content) VALUES ($note_id, '$old_content')";
            $conn -> query($sql);

            //Cập nhật ghi chú
            $sql = "UPDATE notes SET title = '$title', content = '$content', updated_at = CURRENT_TIMESTAMP WHERE id = $note_id AND user_id = $user_id";

            if($conn -> query($sql)) {
                $success = "Cập nhật ghi chú thành công!";
                header("refresh:2; url=list.php");
            } else {
                $error = "Có lỗi xảy ra, Vui lòng thử lại!";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang = "vi">
<head>
    <meta charset = "utf-8">
    <title> Sửa ghi chú </title>
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

    <div class = "container mt-3">
        <div class = "justify-content-center">
            <div class = "col-md-8">
                <div class = "card">
                    <div class = "card-header">
                        <h4> Sửa ghi chú </h4>
                    </div>
                    <div class = "card-body">
                        <?php if ($error): ?>
                            <div class = "alert alert-danger"> <?php echo $error; ?> </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class = "alert alert-success"> <?php echo $success; ?> </div>
                        <?php endif; ?>

                        <form method = "post" action = "">
                            <div class = "mb-4">
                                <label class = "form-label"> Tiêu đề: </label>
                                <input type = "text" name = "title" class = "form-control" value = "<?php echo htmlspecialchars($note['title']); ?>" required>
                            </div>

                            <div class = "mb-4">
                                <label class = "form-label"> Nội dung: </label>
                                <textarea name = "content" class = "form-control" required> <?php echo htmlspecialchars($note['content']); ?> </textarea>
                            </div>

                            <button type = "submit" class = "btn btn-warning"> Cập nhật </button>
                            <a href = "list.php" class="btn btn-secondary">Hủy</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Bootstrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>