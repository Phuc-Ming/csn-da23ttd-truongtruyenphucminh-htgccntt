<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    //Điều kiện
    if (empty($username) || empty($password) || empty($confirm)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else if ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không trùng với mật khẩu đã nhập!";
    } else if (strlen($password) < 8){
        $error = "Mật khẩu phải có ít nhất 8 ký tự!";
    } else {
        $username = mysqli_real_escape_string($conn,$username);

        //Kiểm tra username đã tồn tại chưa
        $sql = "SELECT id FROM users WHERE username = '$username'";
        $result = $conn -> query($sql);

        if ($result -> num_rows > 0){
            $error = "Tên đăng nhập đã tồn tại! Vui lòng nhập tên khác!";
        } else {
            //Mã hóa mật khẩu và thêm user mới
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";

            if ($conn -> query($sql)){
                $success = "Đăng ký thành công!";
                header("refresh:3; url=login.php");
            } else {
                $error = "Đã có lỗi! Vui lòng thử lại!";
            }
        }
    } 
}
?>

<!DOCTYPE html>
<html lang="vi">
<head> 
    <meta charset = "utf-8">
    <title> Đăng ký </title>
    <!--Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../5assets/style.css">
</head>
<body>
    <div class = "container">
        <div class = "justify-content-center mt-5">
            <div class = "col-md-8">
                <div class = "card-body">
                    <h2> Đăng ký tài khoản </h2>

                    <?php if ($error): ?>
                        <div class = "alert alert-danger">
                            <?php echo $error;?> 
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class = "alert alert-success">
                            <?php echo $success;?> 
                        </div>
                    <?php endif; ?>

                    <form method = "post" action = "">
                        <div class = "mb-4">
                            <label class = "form-label"> Tên đăng nhập: </label>
                            <input type = "text" name = "username" class = "form-control" value = "" required>
                        </div>

                        <div class = "mb-4">
                            <label class = "form-label"> Mật khẩu: </label>
                            <input type = "password" name = "password" class = "form-control" value = "" required>
                        </div>

                        <div class = "mb-4">
                            <label class = "form-label"> Xác nhận mật khẩu: </label>
                            <input type = "password" name = "confirm" class = "form-control" value = "" required>
                        </div>

                        <button type = "submit" class = "btn btn-primary"> Đăng ký </button>
                    </form>
                    <p class = "text-center mt-3"> Bạn đã có tài khoản? <a href = "login.php"> Đăng nhập </a></p>
                </div>
            </div>
        </div>
    </div>
    <!--Bootstrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


