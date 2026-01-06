<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty ($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        //Escape tránh lỗi 
        $username = mysqli_real_escape_string($conn,$username);

        //Tìm user trong database
        $sql = "SELECT id, username, password FROM users WHERE username = '$username'";
        $result = $conn -> query($sql);

        if($result -> num_rows == 1){
            $user = $result -> fetch_assoc();

            //Kiểm tra mật khẩu có đúng ko 
            if(password_verify($password,$user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ../3notes/list.php");
                exit();
            } else {
                $error = "Mật khẩu không đúng! Vui lòng nhập lại!";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại! Vui lòng nhập lại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head> 
    <meta charset = "utf-8">
    <title> Đăng nhập </title>
    <!--Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../5assets/style.css">
</head>
<body>
    <div class = "container">
        <div class = "justify-content-center mt-5">
            <div class = "col-md-8">
                <div class = "card-body">
                    <h2> Đăng nhập </h2>

                    <?php if ($error): ?>
                        <div class = "alert alert-danger">
                            <?php echo $error;?> 
                        </div>
                    <?php endif; ?>

                    <form method = "post" action = "">
                        <div class = "mb-4">
                            <label class="form-label"> Tên đăng nhập: </label>
                            <input type = "text" name = "username" class = "form-control" value = "" required>
                        </div>

                        <div class = "mb-4">
                            <label class="form-label"> Mật khẩu: </label>
                            <input type = "password" name = "password" class = "form-control" value = "" required>
                        </div>

                        <button type = "submit" class = "btn btn-primary"> Đăng nhập </button>
                    </form>
                    <p class = "text-center mt-3"> Bạn chưa có tài khoản? <a href = "register.php"> Đăng ký </a></p>
                </div>
            </div>
        </div>
    </div>
    <!--Bootstrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>