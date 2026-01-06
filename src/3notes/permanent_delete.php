<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];
    $note_id = isset($_GET['id']) ? intval($_GET['id']): 0;

    //Kiểm tra ghi chú có thuộc user và đã bị xóa không 
    $sql = "SELECT * FROM notes WHERE id = $note_id AND user_id = $user_id AND is_deleted = 1";
    $result = $conn -> query ($sql);

    if($result -> num_rows == 0){
        header ("Location: trash.php?msg=error");
        exit();
    }

    //Xóa các liên kết tags trước 
    $sql = "DELETE FROM note_tags WHERE note_id = $note_id";
    $conn -> query($sql);

    //Xóa lịch sử chỉnh sửa
    $sql = "DELETE FROM note_history WHERE note_id = $note_id";
    $conn -> query($sql);

    //Xóa ghi chú vĩnh viễn
    $sql = "DELETE FROM notes WHERE id = $note_id AND user_id = $user_id";

    if($conn -> query($sql)){
        header("Location: trash.php?msg=deleted");
    } else {
        header("Location: trash.php?msg=error");
    }
    exit();
?>