<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];
    $error = ""; 
    $success = "";

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $tag_name = trim($_POST['tag_name']);

        if(empty($tag_name)) {
            $error = "Vui lòng nhập tên nhãn!";
        } else {
            //Escape 
            $tag_name = mysqli_real_escape_string($conn,$tag_name);

            //Thêm nhãn mới
            $sql = "INSERT INTO tags (user_id, tag_name) VALUES ($user_id, '$tag_name')";

            if($conn -> query($sql)) {
                header("Location: list_tags.php?msg=created");
                exit();
            } else {
                $error = "Đã có lỗi xảy ra! VUi lòng thử lại.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang = "vi">
<head>
    <meta charset = "utf-8">
    <title> Tạo nhãn mới </title>
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

    <div class = "container mt-4">
        <div class = "row justify-content-center">
            <div class ="col-md-6">
                <div class = "card">
                    <div class = "card-header"> 
                        <h3> ➕Tạo nhãn mới </h3>
                    </div>
                    <div class = "card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method = "post">
                            <div class = "mb-3">
                                <label class = "form-label"> Tên nhãn: </label>
                                <input type = "text" name = "tag_name" class = "form-control" required>
                            </div>
                            <button type = "submit" class = "btn btn-success"> Tạo nhãn </button>
                            <a href = "list_tags.php" class = "btn btn-secondary"> Hủy </a>
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