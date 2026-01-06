<?php
    session_start();  //Bắt đầu session
    session_destroy();  //Hủy session
    header("Location: login.php");  //Đưa về trang đăng nhập
    exit();  
?>