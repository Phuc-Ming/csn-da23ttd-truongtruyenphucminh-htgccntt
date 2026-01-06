<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)){
        $error = "Vui lòng điền đầy đủ nội dung!";
    } else {
        $title = mysqli_real_escape_string($conn, $title);
        $content = mysqli_real_escape_string($conn, $content);

        //Thêm ghi chú mới
        $sql = "INSERT INTO notes (user_id, title, content) VALUES ($user_id, '$title', '$content')";

        if ($conn ->query($sql)){
            $success = "Bạn đã tạo ghi chú thành công!";
            header("refresh:2; url=list.php");
        } else {
            $error = "Đã có lỗi xảy ra. Vui lòng thử lại!";   
        }
    }
}
?>

<!DOCTYPE html>
<html lang = "vi">
<head>
    <meta charset = "utf-8">
    <title> Tạo ghi chú mới </title>
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
                        <h4> ➕Tạo ghi chú mới </h4>
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
                                <input type = "text" name = "title" class = "form-control" value = "<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                            </div>

                            <div class = "mb-4">
                                <label class = "form-label"> Nội dung: </label>
                                <textarea name = "content" class = "form-control" required> <?php echo isset ($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?> </textarea>
                            </div>

                            <button type = "submit" class = "btn btn-success"> Tạo mới </button>
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