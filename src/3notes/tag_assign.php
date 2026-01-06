<?php
    session_start();
    //Kết nối
    include '../1config/connect.php';
    include '../2auth/require_login.php';

    $user_id = $_SESSION['user_id'];
    $note_id = isset($_GET['id']) ? $_GET['id'] : 0;

    //Kiểm tra ghi chú
    $sql = "SELECT title FROM notes WHERE id = $note_id AND user_id = $user_id";
    $result = $conn -> query($sql);

    if($result -> num_rows == 0){
        header("Location: list.php");
        exit();
    }

    $note = $result -> fetch_assoc();

    // Xử lý khi gửi form
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $selected_tags = isset($_POST['tags']) ? $_POST['tags'] : [];
    
        // Xóa nhãn cũ
        $sql = "DELETE FROM note_tags WHERE note_id = $note_id";
        $conn->query($sql);
    
        // Thêm nhãn mới
        if (!empty($selected_tags)) {
            foreach ($selected_tags as $tag_id) {
                $tag_id = intval($tag_id);
                $sql = "INSERT INTO note_tags (note_id, tag_id) VALUES ($note_id, $tag_id)";
                $conn->query($sql);
            }
        }
    
    header("Location: list.php");
    exit();
    }

    // Lấy tất cả nhãn của user
    $sql = "SELECT * FROM tags WHERE user_id = $user_id ORDER BY tag_name";
    $tags_result = $conn->query($sql);

    // Lấy nhãn hiện tại của ghi chú
    $sql = "SELECT tag_id FROM note_tags WHERE note_id = $note_id";
    $current_tags_result = $conn->query($sql);
    $current_tags = [];
    while($row = $current_tags_result -> fetch_assoc()) {
        $current_tags[] = $row['tag_id'];
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Gán nhãn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../5assets/style.css">
     <style>
        .tag-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .tag-table th {
            background-color: #9a13a7ff;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            padding: 15px;
        }
        
        .tag-table td {
            padding: 15px;
            font-size: 1.1rem;
            border-bottom: 1px solid #00070eff;
        }
        
        .form-check-input {
            width: 1.5em;
            height: 1.5em;
            margin-right: 10px;
        }
        
        .form-check-label {
            font-size: 1.1rem;
            font-weight: 500;
        }   
        
        .page-title {
            background: linear-gradient(135deg, #a600ffff, #b41bf0ff);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .btn-large {
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 500;
        }
    </style>
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
    
    <div class="container mt-5">
        <div class = "page-title">
            <h2>Gán nhãn cho: <?php echo htmlspecialchars($note['title']); ?></h2>
        </div>

        <form method="post">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table mb-0 tag-table">
                        <thead>
                            <tr>
                                <th width="100">Chọn</th>
                                <th>Tên nhãn</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($tags_result->num_rows > 0): ?>
                                <?php while($tag = $tags_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="tags[]" 
                                                       value="<?php echo $tag['id']; ?>" 
                                                       id="tag_<?php echo $tag['id']; ?>"
                                                       <?php echo in_array($tag['id'], $current_tags) ? 'checked' : ''; ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="form-check-label" for="tag_<?php echo $tag['id']; ?>">
                                                <?php echo htmlspecialchars($tag['tag_name']); ?>
                                            </label>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-large me-3">
                    Lưu thay đổi
                </button>
                <a href="list.php" class="btn btn-secondary btn-large">
                    Hủy bỏ
                </a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>










