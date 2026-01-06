<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];
    $tag_id = isset($_GET['id']) ? $_GET['id']: 0;

    //Xóa nhãn 
    $sql = "DELETE FROM tags WHERE id = $tag_id AND user_id = $user_id";
    $conn -> query($sql);

    //Xóa liên kết với ghi chú
    $sql = "DELETE FROM note_tags WHERE tag_id = $tag_id";
    $conn -> query($sql);

    header("Location: list_tags.php");
    exit();
?>
