-- ============================================================
-- Quan Ly Khach Hang - SQL bootstrap for XAMPP MariaDB/MySQL
-- Import directly in phpMyAdmin or with:
-- mysql --default-character-set=utf8mb4 -uroot < khoi-tao-co-so-du-lieu.sql
--
-- This bootstrap recreates the quanly_khachhang database.
-- Main model: users, customer_types, customers, interactions, follow_up_tasks.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

SET @app_collation = IF(
    EXISTS (
        SELECT 1
        FROM information_schema.COLLATIONS
        WHERE COLLATION_NAME = 'utf8mb4_vietnamese_ci'
          AND CHARACTER_SET_NAME = 'utf8mb4'
    ),
    'utf8mb4_vietnamese_ci',
    'utf8mb4_unicode_ci'
);

DROP DATABASE IF EXISTS `quanly_khachhang`;

SET @create_database_sql = CONCAT(
    'CREATE DATABASE `quanly_khachhang` CHARACTER SET utf8mb4 COLLATE ',
    @app_collation
);
PREPARE create_database_stmt FROM @create_database_sql;
EXECUTE create_database_stmt;
DEALLOCATE PREPARE create_database_stmt;

USE `quanly_khachhang`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. users: tai khoan dang nhap va phan quyen admin/nhan vien
-- ============================================================

CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(120) NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(191) NOT NULL,
    `phone` VARCHAR(32) NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    `status` ENUM('active', 'locked') NOT NULL DEFAULT 'active',
    `last_login_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_role_status` (`role`, `status`),
    KEY `idx_users_status` (`status`),
    KEY `idx_users_last_login_at` (`last_login_at`)
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

-- ============================================================
-- 2. customer_types: phan loai khach hang va diem uu tien
-- ============================================================

CREATE TABLE `customer_types` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(80) NOT NULL,
    `description` VARCHAR(255) NULL,
    `priority_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `color` VARCHAR(20) NOT NULL DEFAULT '#64748b',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_customer_types_name` (`name`),
    KEY `idx_customer_types_active` (`is_active`),
    KEY `idx_customer_types_priority_score` (`priority_score`)
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

-- ============================================================
-- 3. customers: ho so khach hang, phan cong nhan vien, xoa mem
-- Duplicate active phone/email is blocked through generated keys.
-- Soft-deleted rows keep data but do not block phone/email reuse.
-- ============================================================

CREATE TABLE `customers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_type_id` INT UNSIGNED NOT NULL,
    `assigned_user_id` INT UNSIGNED NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `company_name` VARCHAR(150) NULL,
    `gender` ENUM('male', 'female', 'other', 'unknown') NOT NULL DEFAULT 'unknown',
    `date_of_birth` DATE NULL,
    `phone` VARCHAR(32) NULL,
    `phone_normalized` VARCHAR(32) NULL,
    `email` VARCHAR(191) NULL,
    `email_normalized` VARCHAR(191) NULL,
    `address` VARCHAR(255) NULL,
    `city` VARCHAR(80) NULL,
    `source` ENUM('website', 'facebook', 'referral', 'walk_in', 'phone', 'other') NOT NULL DEFAULT 'other',
    `status` ENUM('active', 'potential', 'inactive') NOT NULL DEFAULT 'active',
    `notes` TEXT NULL,
    `deleted_at` DATETIME NULL,
    `active_phone_key` VARCHAR(32)
        GENERATED ALWAYS AS (
            CASE
                WHEN `deleted_at` IS NULL
                 AND `phone_normalized` IS NOT NULL
                 AND `phone_normalized` <> ''
                THEN `phone_normalized`
                ELSE NULL
            END
        ) PERSISTENT,
    `active_email_key` VARCHAR(191)
        GENERATED ALWAYS AS (
            CASE
                WHEN `deleted_at` IS NULL
                 AND `email_normalized` IS NOT NULL
                 AND `email_normalized` <> ''
                THEN `email_normalized`
                ELSE NULL
            END
        ) PERSISTENT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_customers_active_phone` (`active_phone_key`),
    UNIQUE KEY `uq_customers_active_email` (`active_email_key`),
    KEY `idx_customers_name` (`full_name`),
    KEY `idx_customers_company_name` (`company_name`),
    KEY `idx_customers_type_status` (`customer_type_id`, `status`),
    KEY `idx_customers_assigned_status` (`assigned_user_id`, `status`),
    KEY `idx_customers_status_deleted` (`status`, `deleted_at`),
    KEY `idx_customers_deleted_at` (`deleted_at`),
    KEY `idx_customers_phone_normalized` (`phone_normalized`),
    KEY `idx_customers_email_normalized` (`email_normalized`),
    CONSTRAINT `fk_customers_customer_type`
        FOREIGN KEY (`customer_type_id`) REFERENCES `customer_types` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_customers_assigned_user`
        FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

-- ============================================================
-- 4. interactions: lich su cham soc/lien he khach hang
-- ============================================================

CREATE TABLE `interactions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `interaction_type` ENUM('call', 'email', 'meeting', 'note', 'chat', 'zalo', 'other') NOT NULL DEFAULT 'note',
    `title` VARCHAR(150) NOT NULL,
    `content` TEXT NULL,
    `result` VARCHAR(150) NULL,
    `interaction_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_interactions_customer_date` (`customer_id`, `interaction_at`),
    KEY `idx_interactions_user_date` (`user_id`, `interaction_at`),
    KEY `idx_interactions_type_date` (`interaction_type`, `interaction_at`),
    CONSTRAINT `fk_interactions_customer`
        FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_interactions_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

-- ============================================================
-- 5. follow_up_tasks: viec can theo doi, den han, qua han
-- ============================================================

CREATE TABLE `follow_up_tasks` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_id` INT UNSIGNED NOT NULL,
    `assigned_user_id` INT UNSIGNED NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `due_at` DATETIME NOT NULL,
    `status` ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `priority` ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    `completed_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_tasks_assignee_status_due` (`assigned_user_id`, `status`, `due_at`),
    KEY `idx_tasks_customer_status` (`customer_id`, `status`),
    KEY `idx_tasks_due_at` (`due_at`),
    KEY `idx_tasks_priority` (`priority`),
    KEY `idx_tasks_created_by` (`created_by`),
    CONSTRAINT `fk_tasks_customer`
        FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT `fk_tasks_assigned_user`
        FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT `fk_tasks_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

-- Make the chosen collation explicit at table level.
SET @alter_users_sql = CONCAT('ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE ', @app_collation);
SET @alter_customer_types_sql = CONCAT('ALTER TABLE `customer_types` CONVERT TO CHARACTER SET utf8mb4 COLLATE ', @app_collation);
SET @alter_customers_sql = CONCAT('ALTER TABLE `customers` CONVERT TO CHARACTER SET utf8mb4 COLLATE ', @app_collation);
SET @alter_interactions_sql = CONCAT('ALTER TABLE `interactions` CONVERT TO CHARACTER SET utf8mb4 COLLATE ', @app_collation);
SET @alter_tasks_sql = CONCAT('ALTER TABLE `follow_up_tasks` CONVERT TO CHARACTER SET utf8mb4 COLLATE ', @app_collation);

PREPARE alter_users_stmt FROM @alter_users_sql;
EXECUTE alter_users_stmt;
DEALLOCATE PREPARE alter_users_stmt;

PREPARE alter_customer_types_stmt FROM @alter_customer_types_sql;
EXECUTE alter_customer_types_stmt;
DEALLOCATE PREPARE alter_customer_types_stmt;

PREPARE alter_customers_stmt FROM @alter_customers_sql;
EXECUTE alter_customers_stmt;
DEALLOCATE PREPARE alter_customers_stmt;

PREPARE alter_interactions_stmt FROM @alter_interactions_sql;
EXECUTE alter_interactions_stmt;
DEALLOCATE PREPARE alter_interactions_stmt;

PREPARE alter_tasks_stmt FROM @alter_tasks_sql;
EXECUTE alter_tasks_stmt;
DEALLOCATE PREPARE alter_tasks_stmt;

-- ============================================================
-- Demo seed data. Mat khau mac dinh cho moi tai khoan: 123456
-- Dataset duoc thiet ke de dashboard, bao cao va Top 3 co y nghia.
-- Trang thai "done" trong yeu cau demo duoc luu bang gia tri completed.
-- ============================================================

INSERT INTO `users`
    (`id`, `full_name`, `username`, `email`, `phone`, `password_hash`, `role`, `status`)
VALUES
    (1, 'Quản trị viên hệ thống', 'admin', 'admin@quanlykhachhang.demo.vn', '0900 000 001', '$2y$10$KJ7.uqFJcw3r24WLj6rEduHssNR3FcZe/S8UzLMfzzz5euNoPcl5W', 'admin', 'active'),
    (2, 'Nguyễn Minh Anh', 'minhanh', 'minhanh@quanlykhachhang.demo.vn', '0901 111 222', '$2y$10$KJ7.uqFJcw3r24WLj6rEduHssNR3FcZe/S8UzLMfzzz5euNoPcl5W', 'staff', 'active'),
    (3, 'Trần Quốc Bảo', 'quocbao', 'quocbao@quanlykhachhang.demo.vn', '0902 222 333', '$2y$10$KJ7.uqFJcw3r24WLj6rEduHssNR3FcZe/S8UzLMfzzz5euNoPcl5W', 'staff', 'active'),
    (4, 'Lê Thu Trang', 'thutrang', 'thutrang@quanlykhachhang.demo.vn', '0903 333 444', '$2y$10$KJ7.uqFJcw3r24WLj6rEduHssNR3FcZe/S8UzLMfzzz5euNoPcl5W', 'staff', 'active');

INSERT INTO `customer_types`
    (`id`, `name`, `description`, `priority_score`, `color`, `is_active`)
VALUES
    (1, 'VIP', 'Khách hàng giá trị cao, cần ưu tiên chăm sóc và báo cáo riêng.', 96, '#b45309', 1),
    (2, 'Khách trung thành', 'Khách đã hợp tác ổn định, có lịch sử mua hoặc tương tác tốt.', 84, '#059669', 1),
    (3, 'Khách tiềm năng', 'Khách có nhu cầu rõ ràng nhưng chưa chốt quyết định.', 72, '#2563eb', 1),
    (4, 'Khách mới', 'Khách vừa phát sinh liên hệ, cần xác minh nhu cầu ban đầu.', 48, '#7c3aed', 1),
    (5, 'Tạm ngưng', 'Khách chưa có nhu cầu hiện tại nhưng vẫn cần lưu lịch sử.', 20, '#64748b', 1);

INSERT INTO `customers`
    (`id`, `customer_type_id`, `assigned_user_id`, `full_name`, `company_name`, `gender`, `date_of_birth`,
     `phone`, `phone_normalized`, `email`, `email_normalized`, `address`, `city`, `source`, `status`, `notes`, `deleted_at`)
VALUES
    (1, 1, 2, 'Nguyễn Thị Lan Anh', 'Công ty TNHH An Phú', 'female', '1989-04-12',
     '0912 345 678', '0912345678', 'lananh@anphu.demo.vn', 'lananh@anphu.demo.vn',
     '12 Nguyễn Huệ, Quận 1', 'TP. Hồ Chí Minh', 'referral', 'active',
     'Khách VIP quan tâm dashboard, báo cáo và quy trình chăm sóc sau bán hàng.', NULL),
    (2, 2, 3, 'Trần Văn Minh', 'Minh Long Logistics', 'male', '1985-08-20',
     '0938 222 333', '0938222333', 'minh@minhlong.demo.vn', 'minh@minhlong.demo.vn',
     '45 Lê Lợi, Quận Hải Châu', 'Đà Nẵng', 'website', 'active',
     'Khách trung thành, thường phản hồi nhanh và có khả năng mở rộng hợp đồng.', NULL),
    (3, 3, 2, 'Bùi Anh Khoa', 'Khoa Tech', 'male', '1991-12-01',
     '0907 777 888', '0907777888', 'khoa@khoatech.demo.vn', 'khoa@khoatech.demo.vn',
     '60 Cách Mạng Tháng 8', 'Cần Thơ', 'website', 'potential',
     'Đang so sánh giữa hai phương án, cần chứng minh hiệu quả quản lý công việc.', NULL),
    (4, 3, 4, 'Phạm Thu Hà', 'Hà An Studio', 'female', '1994-02-18',
     '0904 444 555', '0904444555', 'ha@haanstudio.demo.vn', 'ha@haanstudio.demo.vn',
     '21 Nguyễn Trãi, Thanh Xuân', 'Hà Nội', 'phone', 'potential',
     'Muốn được gọi sau giờ hành chính, dễ chuyển đổi nếu demo rõ quy trình.', NULL),
    (5, 2, 4, 'Võ Mai Chi', 'Chi Mai Beauty', 'female', '1989-06-30',
     '0906 666 777', '0906666777', 'chi@chimaibeauty.demo.vn', 'chi@chimaibeauty.demo.vn',
     '9 Phan Chu Trinh', 'Huế', 'facebook', 'active',
     'Khách trung thành, cần theo dõi lịch chăm sóc định kỳ theo mùa.', NULL),
    (6, 4, 2, 'Đặng Quốc Huy', 'Huy Foods', 'male', '1993-03-09',
     '0905 555 666', '0905555666', 'huy@huyfoods.demo.vn', 'huy@huyfoods.demo.vn',
     '15 Võ Văn Tần, Quận 3', 'TP. Hồ Chí Minh', 'walk_in', 'active',
     'Khách mới ghé trực tiếp, cần xác nhận nhu cầu quản lý khách mua lẻ.', NULL),
    (7, 3, 3, 'Lê Hoàng Phúc', 'Phúc Gia Mart', 'male', '1992-11-03',
     '0903 333 444', '0903333444', 'phuc@phucgiamart.demo.vn', 'phuc@phucgiamart.demo.vn',
     '88 Trần Phú', 'Nha Trang', 'facebook', 'potential',
     'Có nhu cầu thật nhưng còn chờ duyệt ngân sách.', NULL),
    (8, 4, 3, 'Nguyễn Đức Tài', 'Tài Nam Coffee', 'male', '1988-10-25',
     '0918 888 999', '0918888999', 'tai@tainamcoffee.demo.vn', 'tai@tainamcoffee.demo.vn',
     '32 Hai Bà Trưng', 'Đà Lạt', 'other', 'active',
     'Khách mới cần tư vấn quy trình quản lý khách hàng thân thiết.', NULL),
    (9, 4, 4, 'Hoàng Yến Nhi', 'Nhi Boutique', 'female', '1996-07-14',
     '0981 234 567', '0981234567', 'nhi@nhiboutique.demo.vn', 'nhi@nhiboutique.demo.vn',
     '18 Lý Tự Trọng', 'Hải Phòng', 'facebook', 'potential',
     'Khách mới từ mạng xã hội, cần kiểm tra lại nhu cầu thật.', NULL),
    (10, 2, 2, 'Phan Gia Hân', 'Hân Edu', 'female', '1987-01-22',
     '0977 111 222', '0977111222', 'han@hanedu.demo.vn', 'han@hanedu.demo.vn',
     '5 Nguyễn Văn Cừ', 'Hà Nội', 'referral', 'active',
     'Khách trung thành nhưng mới bắt đầu trao đổi thêm nhu cầu mới.', NULL),
    (11, 5, 3, 'Đỗ Quốc Dũng', 'Dũng Auto', 'male', '1984-09-05',
     '0966 222 333', '0966222333', 'dung@dungauto.demo.vn', 'dung@dungauto.demo.vn',
     '101 Nguyễn Văn Linh', 'Đà Nẵng', 'phone', 'inactive',
     'Tạm ngưng nhu cầu, vẫn cần nhắc lại sau một tháng.', NULL),
    (12, 4, 2, 'Bản ghi cũ An Phú', 'Dữ liệu đã gộp', 'unknown', NULL,
     '0912 345 678', '0912345678', 'lananh@anphu.demo.vn', 'lananh@anphu.demo.vn',
     'Địa chỉ cũ trước khi chuẩn hóa', 'TP. Hồ Chí Minh', 'other', 'inactive',
     'Bản ghi đã xóa mềm để kiểm thử trùng điện thoại/email và khôi phục.', DATE_SUB(NOW(), INTERVAL 35 DAY));

INSERT INTO `interactions`
    (`customer_id`, `user_id`, `interaction_type`, `title`, `content`, `result`, `interaction_at`)
VALUES
    (1, 2, 'meeting', 'Gặp tư vấn quy trình quản lý', 'Khách trình bày nhu cầu quản lý khách doanh nghiệp và muốn xem dashboard tổng quan.', 'Thống nhất chuẩn bị demo theo dữ liệu ngành dịch vụ.', DATE_SUB(NOW(), INTERVAL 16 DAY)),
    (1, 2, 'email', 'Gửi tài liệu giải pháp', 'Đã gửi tài liệu mô tả quy trình phân loại khách VIP và chăm sóc sau bán hàng.', 'Khách phản hồi tích cực.', DATE_SUB(NOW(), INTERVAL 12 DAY)),
    (1, 2, 'call', 'Gọi xác nhận nội dung demo', 'Khách muốn tập trung vào Top 3 khách hàng và việc quá hạn.', 'Chốt lịch demo nội bộ.', DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (1, 1, 'note', 'Ghi chú ưu tiên demo', 'Admin đánh dấu An Phú là khách trọng điểm cho phần trình bày dashboard.', 'Ưu tiên xử lý trước.', DATE_SUB(NOW(), INTERVAL 1 DAY)),

    (2, 3, 'call', 'Gọi rà soát hợp đồng cũ', 'Khách muốn mở rộng số lượng nhân viên sử dụng hệ thống.', 'Cần gửi báo giá nâng cấp.', DATE_SUB(NOW(), INTERVAL 18 DAY)),
    (2, 3, 'email', 'Gửi báo giá nâng cấp', 'Đã gửi báo giá gói mở rộng theo số lượng người dùng.', 'Chờ xác nhận ngân sách.', DATE_SUB(NOW(), INTERVAL 9 DAY)),
    (2, 1, 'meeting', 'Họp đánh giá hiệu quả', 'Khách đánh giá tốt phần báo cáo công việc hoàn thành theo nhân viên.', 'Có khả năng gia hạn.', DATE_SUB(NOW(), INTERVAL 3 DAY)),

    (3, 2, 'chat', 'Trao đổi yêu cầu kỹ thuật', 'Khách hỏi về kiểm tra trùng số điện thoại và email.', 'Đã giải thích cơ chế kiểm tra trùng.', DATE_SUB(NOW(), INTERVAL 13 DAY)),
    (3, 2, 'meeting', 'Demo nhanh dashboard', 'Khách chú ý phần Top 3 và danh sách việc sắp tới.', 'Muốn nhận đề xuất triển khai.', DATE_SUB(NOW(), INTERVAL 7 DAY)),
    (3, 2, 'zalo', 'Gửi ảnh giao diện mẫu', 'Gửi ảnh màn hình danh sách khách hàng và trang chi tiết.', 'Khách yêu cầu thêm báo cáo theo nhân viên.', DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (3, 2, 'call', 'Gọi chốt bước tiếp theo', 'Khách cần bản đề xuất trước cuộc họp quản lý.', 'Tạo công việc gửi đề xuất.', DATE_SUB(NOW(), INTERVAL 1 DAY)),

    (4, 4, 'call', 'Gọi tư vấn ngoài giờ', 'Khách chỉ rảnh sau 19h và cần xem nhanh quy trình nhập khách.', 'Cần gọi lại đúng khung giờ.', DATE_SUB(NOW(), INTERVAL 8 DAY)),
    (4, 4, 'chat', 'Xác nhận nhu cầu studio', 'Khách muốn lưu lịch sử chăm sóc khách đặt lịch chụp.', 'Đợi khách phản hồi sau demo.', DATE_SUB(NOW(), INTERVAL 2 DAY)),

    (5, 4, 'meeting', 'Trao đổi chăm sóc định kỳ', 'Khách cần nhắc lịch chăm sóc khách cũ theo từng đợt khuyến mãi.', 'Tạo lịch theo dõi định kỳ.', DATE_SUB(NOW(), INTERVAL 20 DAY)),
    (5, 4, 'call', 'Kiểm tra phản hồi sau tư vấn', 'Khách hài lòng nhưng muốn xem thêm báo cáo.', 'Hẹn gửi báo cáo mẫu.', DATE_SUB(NOW(), INTERVAL 6 DAY)),

    (6, 2, 'note', 'Ghi nhận khách ghé trực tiếp', 'Khách cần cách lưu thông tin khách mua lẻ và ghi chú nhu cầu.', 'Cần gọi xác nhận sau 5 ngày.', DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (7, 3, 'email', 'Gửi bảng tính ngân sách', 'Khách yêu cầu tài liệu để trình quản lý cửa hàng.', 'Chờ duyệt ngân sách.', DATE_SUB(NOW(), INTERVAL 11 DAY)),
    (8, 3, 'call', 'Gọi tư vấn ban đầu', 'Khách muốn quản lý khách hàng thân thiết cho chuỗi cà phê nhỏ.', 'Hẹn gửi kịch bản triển khai.', DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (10, 2, 'meeting', 'Tiếp nhận nhu cầu trường học', 'Khách muốn theo dõi phụ huynh quan tâm khóa học.', 'Cần phân tích thêm trước khi báo giá.', DATE_SUB(NOW(), INTERVAL 14 DAY)),
    (11, 3, 'other', 'Cập nhật trạng thái tạm ngưng', 'Khách chưa triển khai trong tháng này do thay đổi kế hoạch nội bộ.', 'Nhắc lại vào tháng sau.', DATE_SUB(NOW(), INTERVAL 15 DAY));

INSERT INTO `follow_up_tasks`
    (`customer_id`, `assigned_user_id`, `created_by`, `title`, `description`, `due_at`, `status`, `priority`, `completed_at`)
VALUES
    (1, 2, 1, 'Chuẩn bị kịch bản demo cho An Phú', 'Tập trung Top 3 khách hàng, KPI và việc quá hạn.', DATE_ADD(NOW(), INTERVAL 1 DAY), 'in_progress', 'high', NULL),
    (1, 2, 2, 'Gửi tài liệu phân loại khách VIP', 'Tài liệu đã gửi sau buổi gặp đầu tiên.', DATE_SUB(NOW(), INTERVAL 11 DAY), 'completed', 'medium', DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (1, 2, 2, 'Xác nhận danh sách người dự demo', 'Đã xác nhận trưởng nhóm kinh doanh và quản lý vận hành.', DATE_SUB(NOW(), INTERVAL 2 DAY), 'completed', 'high', DATE_SUB(NOW(), INTERVAL 1 DAY)),

    (2, 3, 1, 'Gọi chốt phản hồi báo giá nâng cấp', 'Nhắc khách phản hồi sau khi đã nhận báo giá.', DATE_ADD(NOW(), INTERVAL 2 DAY), 'pending', 'high', NULL),
    (2, 3, 3, 'Cập nhật hồ sơ khách trung thành', 'Đã bổ sung lịch sử hợp tác và ghi chú gia hạn.', DATE_SUB(NOW(), INTERVAL 8 DAY), 'completed', 'medium', DATE_SUB(NOW(), INTERVAL 7 DAY)),
    (2, 3, 3, 'Gửi mẫu báo cáo nhân viên', 'Đã gửi file mô tả chỉ số hiệu quả nhân viên.', DATE_SUB(NOW(), INTERVAL 4 DAY), 'completed', 'medium', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (2, 3, 3, 'Hẹn khảo sát kho vận', 'Khách hủy do đổi lịch nội bộ.', DATE_SUB(NOW(), INTERVAL 5 DAY), 'cancelled', 'low', NULL),

    (3, 2, 2, 'Gửi đề xuất triển khai cho Khoa Tech', 'Soạn đề xuất tập trung AJAX, kiểm tra trùng và dashboard.', DATE_ADD(NOW(), INTERVAL 3 DAY), 'pending', 'high', NULL),
    (3, 2, 2, 'Hoàn tất demo nhanh dashboard', 'Đã demo phần Top 3 và công việc sắp tới.', DATE_SUB(NOW(), INTERVAL 6 DAY), 'completed', 'medium', DATE_SUB(NOW(), INTERVAL 6 DAY)),

    (4, 4, 4, 'Gọi lại Hà An Studio sau 19h', 'Khách yêu cầu gọi ngoài giờ hành chính.', DATE_SUB(NOW(), INTERVAL 1 DAY), 'pending', 'high', NULL),
    (5, 4, 4, 'Gửi báo cáo mẫu cho Chi Mai Beauty', 'Việc đã quá hạn, cần ưu tiên xử lý trước buổi demo.', DATE_SUB(NOW(), INTERVAL 3 DAY), 'in_progress', 'medium', NULL),
    (5, 4, 4, 'Tạo lịch chăm sóc định kỳ', 'Đã thống nhất lịch nhắc theo từng đợt khuyến mãi.', DATE_SUB(NOW(), INTERVAL 15 DAY), 'completed', 'medium', DATE_SUB(NOW(), INTERVAL 14 DAY)),

    (6, 2, 2, 'Gọi xác nhận nhu cầu Huy Foods', 'Kiểm tra lại sau khi khách ghé trực tiếp.', DATE_ADD(NOW(), INTERVAL 5 DAY), 'pending', 'low', NULL),
    (7, 3, 3, 'Nhắc Phúc Gia Mart duyệt ngân sách', 'Công việc quá hạn để dashboard có cảnh báo rõ.', DATE_SUB(NOW(), INTERVAL 2 DAY), 'pending', 'high', NULL),
    (8, 3, 3, 'Gửi kịch bản khách hàng thân thiết', 'Gửi ví dụ quy trình cho quán cà phê.', DATE_ADD(NOW(), INTERVAL 4 DAY), 'pending', 'medium', NULL),
    (9, 4, 4, 'Kiểm tra lại nhu cầu Nhi Boutique', 'Khách mới từ mạng xã hội, cần xác nhận nhu cầu thật.', DATE_ADD(NOW(), INTERVAL 7 DAY), 'pending', 'medium', NULL),
    (10, 2, 1, 'Phân tích nhu cầu Hân Edu', 'Chưa đủ dữ liệu để báo giá nên cần thêm thông tin.', DATE_ADD(NOW(), INTERVAL 6 DAY), 'pending', 'medium', NULL),
    (11, 3, 3, 'Nhắc lại Dũng Auto sau tạm ngưng', 'Theo dõi lại khi khách có kế hoạch mới.', DATE_ADD(NOW(), INTERVAL 20 DAY), 'pending', 'low', NULL),
    (11, 3, 3, 'Demo quản lý lịch hẹn cho Dũng Auto', 'Khách hủy vì chưa sẵn sàng triển khai.', DATE_SUB(NOW(), INTERVAL 12 DAY), 'cancelled', 'low', NULL);

-- Quick visibility after import.
SELECT @app_collation AS selected_collation;
SELECT 'users' AS table_name, COUNT(*) AS row_count FROM `users`
UNION ALL
SELECT 'customer_types', COUNT(*) FROM `customer_types`
UNION ALL
SELECT 'customers', COUNT(*) FROM `customers`
UNION ALL
SELECT 'interactions', COUNT(*) FROM `interactions`
UNION ALL
SELECT 'follow_up_tasks', COUNT(*) FROM `follow_up_tasks`;
