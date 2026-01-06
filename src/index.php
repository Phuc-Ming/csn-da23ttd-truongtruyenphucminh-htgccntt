<?php
    //Kiểm tra
    session_start();

    if (isset($_SESSION['user_id'])) {
        header("Location: 3notes/list.php");
    } else {
        header("Location: 2auth/login.php");
    }
    exit();
?>