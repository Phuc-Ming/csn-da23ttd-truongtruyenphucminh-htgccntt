<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];
    $note_id = isset($_GET['note_id']) ? intval($_GET['note_id']) : 0;
    $history_id = isset($_GET['history_id']) ? intval($_GET['history_id']) : 0;

    //Kiểm tra ghi chú có thuộc user không
    $sql = "SELECT * FROM notes WHERE id = $note_id AND user_id = $user_id AND is_deleted = 0";
    $result = $conn -> query($sql);

    if ($result -> num_rows == 0){
        header("Location: list.php");
        exit();
    }

    $note = $result -> fetch_assoc();

    //Lấy nội dung cũ từ lịch sử 
    $sql = "SELECT old_content FROM note_history WHERE id = $history_id AND note_id = $note_id";
    $history_result = $conn -> query($sql);

    if ($history_result -> num_rows == 0){
        header("Location: history.php?id=$note_id&msg=error");
        exit();
    }

    $history = $history_result -> fetch_assoc();
    $old_content = $history['old_content'];

    //Lưu nội dung hiện tại vào lịch sử (trước khi khôi phục)
    $current_content = mysqli_real_escape_string($conn, $note['content']);
    $sql = "INSERT INTO note_history (note_id, old_content) VALUES ($note_id, '$current_content')";
    $conn -> query($sql);

    //Khôi phục nội dung cũ 
    $old_content = mysqli_real_escape_string($conn, $old_content);
    $sql = "UPDATE notes SET content = '$old_content', updated_at = CURRENT_TIMESTAMP WHERE id = $note_id AND user_id = $user_id"; 

    if ($conn -> query($sql)){
        header("Location: list.php?msg=restored");
    } else {
        header("Location: history.php?id=$note_id&msg=error");
    }
    exit();
?>