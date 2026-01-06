# Hệ thống Ghi chú Cá nhân Trực tuyến
Đồ án cơ sở ngành - Thiết kế hệ thống ghi chú cá nhân trực tuyến ứng dụng công nghệ web động
Tên: Trương Truyền Phúc Minh
Email: ttphucminh2005@gmail.com
SĐT: 0846011105
## Tính năng

- Đăng ký và đăng nhập người dùng
- Tạo, sửa, xóa ghi chú
- Gán nhãn (tags) cho ghi chú
- Tìm kiếm và lọc ghi chú theo nhãn
- Xem lịch sử chỉnh sửa ghi chú
- Quản lý nhãn

## Cài đặt

1. Tạo database MySQL với tên `notes_db`
2. Import file SQL để tạo các bảng:

```sql
CREATE DATABASE notes_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE notes_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    is_deleted TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tag_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE note_tags (
    note_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (note_id, tag_id),
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE note_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note_id INT NOT NULL,
    old_content TEXT NOT NULL,
    edited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
);
```

3. Cấu hình kết nối database trong `1config/connect.php`
4. Chạy ứng dụng trong XAMPP

## Cấu trúc thư mục
- `1config/` - Cấu hình kết nối database
- `2auth/` - Xác thực người dùng (đăng ký, đăng nhập, đăng xuất)
- `3notes/` - Quản lý ghi chú
- `4tags/` - Quản lý nhãn
- `5assets/` - CSS và JavaScript
- `index.php` - Trang chủ (chuyển hướng)

## Công nghệ sử dụng
- PHP
- MySQL
- Bootstrap 5
- HTML/CSS/JavaScript
