<?php
    session_start();
    
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];
    $note_id = isset($_GET['id']) ? $_GET['id'] : 0;

    //Xóa tạm thời ghi chú
    $sql = "UPDATE notes SET is_deleted = 1 WHERE id = $note_id AND user_id = $user_id";

    if($conn -> query($sql)){
        header("Location:list.php?msg=deleted");
    } else {
        header("Location:list.php?msg=error");
    }
    exit();
?>