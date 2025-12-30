<?php
    //Kiểm tra
    session_start();

    if (isset($_SESSION['user_id'])) {
        header("Location: cnotes/list.php");
    } else {
        header("Location: bauth/login.php");
    }
    exit();
?>
