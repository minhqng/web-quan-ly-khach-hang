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
-- ============================================================

INSERT INTO `users`
    (`id`, `full_name`, `username`, `email`, `phone`, `password_hash`, `role`, `status`)
VALUES
    (1, 'Quản trị viên', 'admin', 'admin@example.com', '0900000000', '$2y$10$KJ7.uqFJcw3r24WLj6rEduHssNR3FcZe/S8UzLMfzzz5euNoPcl5W', 'admin', 'active'),
    (2, 'Nguyễn Minh Anh', 'minhanh', 'minhanh@example.com', '0901111222', '$2y$10$KzwOoW0/2chTFxyaQl89/uGXZrnpk65MPKr6a1OiZjfuaePUwUZ2S', 'staff', 'active'),
    (3, 'Trần Quốc Bảo', 'quocbao', 'quocbao@example.com', '0903333444', '$2y$10$Dq9H/w3xSYy6f.RizwJbZ.V8HG7xYQEGaWVLIY/PdnJx06Jr0suKW', 'staff', 'active');

INSERT INTO `customer_types`
    (`id`, `name`, `description`, `priority_score`, `color`, `is_active`)
VALUES
    (1, 'VIP', 'Khách hàng có giá trị cao, cần ưu tiên chăm sóc.', 90, '#b45309', 1),
    (2, 'Tiềm năng', 'Khách hàng có nhu cầu rõ ràng, cần theo sát.', 70, '#2563eb', 1),
    (3, 'Thường', 'Khách hàng phổ thông hoặc mới phát sinh nhu cầu.', 40, '#64748b', 1);

INSERT INTO `customers`
    (`id`, `customer_type_id`, `assigned_user_id`, `full_name`, `company_name`, `gender`, `date_of_birth`,
     `phone`, `phone_normalized`, `email`, `email_normalized`, `address`, `city`, `source`, `status`, `notes`, `deleted_at`)
VALUES
    (1, 1, 2, 'Nguyễn Thị Lan', 'Công ty TNHH An Phú', 'female', '1990-04-12',
     '0901 111 222', '0901111222', 'lan.nguyen@anphu.example', 'lan.nguyen@anphu.example',
     '12 Nguyễn Huệ, Quận 1', 'TP. Hồ Chí Minh', 'referral', 'active', 'Quan tâm gói chăm sóc khách hàng doanh nghiệp.', NULL),
    (2, 1, 3, 'Trần Văn Minh', 'Minh Long Logistics', 'male', '1985-08-20',
     '0902 222 333', '0902222333', 'minh.tran@minhlong.example', 'minh.tran@minhlong.example',
     '45 Lê Lợi, Quận Hải Châu', 'Đà Nẵng', 'website', 'active', 'Khách hàng cần báo giá nhanh trong tuần.', NULL),
    (3, 2, 2, 'Lê Hoàng Phúc', 'Phúc Gia Mart', 'male', '1992-11-03',
     '0903 333 444', '0903333444', 'phuc.le@phucgia.example', 'phuc.le@phucgia.example',
     '88 Trần Phú', 'Nha Trang', 'facebook', 'potential', 'Đang so sánh với nhà cung cấp khác.', NULL),
    (4, 2, 3, 'Phạm Thu Hà', 'Hà An Studio', 'female', '1994-02-18',
     '0904 444 555', '0904444555', 'ha.pham@haan.example', 'ha.pham@haan.example',
     '21 Nguyễn Trãi', 'Hà Nội', 'phone', 'potential', 'Muốn được nhắc lịch tư vấn sau giờ hành chính.', NULL),
    (5, 3, 2, 'Đặng Quốc Huy', NULL, 'male', NULL,
     '0905 555 666', '0905555666', 'huy.dang@example.com', 'huy.dang@example.com',
     '15 Võ Văn Tần', 'TP. Hồ Chí Minh', 'walk_in', 'active', 'Khách lẻ cần theo dõi nhu cầu định kỳ.', NULL),
    (6, 3, 3, 'Võ Mai Chi', 'Chi Mai Beauty', 'female', '1989-06-30',
     '0906 666 777', '0906666777', 'chi.vo@beauty.example', 'chi.vo@beauty.example',
     '9 Phan Chu Trinh', 'Huế', 'other', 'inactive', 'Tạm ngưng nhu cầu, gọi lại sau một tháng.', NULL),
    (7, 2, 2, 'Bùi Anh Khoa', 'Khoa Tech', 'male', '1991-12-01',
     '0907 777 888', '0907777888', 'khoa.bui@khoatech.example', 'khoa.bui@khoatech.example',
     '60 Cách Mạng Tháng 8', 'Cần Thơ', 'website', 'active', 'Có khả năng trở thành khách VIP nếu chốt hợp đồng.', NULL),
    (8, 3, 2, 'Khách hàng cũ An Phú', 'Dữ liệu cũ', 'unknown', NULL,
     '0901 111 222', '0901111222', 'lan.nguyen@anphu.example', 'lan.nguyen@anphu.example',
     'Địa chỉ cũ', 'TP. Hồ Chí Minh', 'other', 'inactive', 'Bản ghi đã xóa mềm để kiểm thử tái sử dụng điện thoại/email.', DATE_SUB(NOW(), INTERVAL 30 DAY));

INSERT INTO `interactions`
    (`customer_id`, `user_id`, `interaction_type`, `title`, `content`, `result`, `interaction_at`)
VALUES
    (1, 2, 'meeting', 'Gặp tư vấn lần đầu', 'Khách quan tâm gói chăm sóc khách hàng cho đội bán hàng.', 'Cần gửi demo và báo giá chi tiết.', DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (1, 2, 'call', 'Gọi xác nhận nhu cầu', 'Khách muốn xem demo dashboard và báo cáo.', 'Hẹn chuẩn bị demo Top 3 khách hàng.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (2, 3, 'email', 'Gửi báo giá sơ bộ', 'Đã gửi bảng giá và hẹn phản hồi trong tuần.', 'Chờ khách xác nhận ngân sách.', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (3, 2, 'chat', 'Trao đổi qua chat', 'Khách hỏi về chính sách hỗ trợ sau triển khai.', 'Gửi tài liệu hỗ trợ sau bán hàng.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4, 3, 'call', 'Gọi nhắc lịch tư vấn', 'Khách đề nghị gọi lại sau 19h.', 'Cần gọi lại ngoài giờ hành chính.', DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (5, 2, 'note', 'Ghi chú tại quầy', 'Khách cần quản lý danh bạ khách mua lẻ.', 'Theo dõi nhu cầu định kỳ.', DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (7, 2, 'meeting', 'Demo nhanh tính năng', 'Khách đánh giá cao phần theo dõi công việc.', 'Có cơ hội nâng cấp lên nhóm VIP.', DATE_SUB(NOW(), INTERVAL 1 DAY));

INSERT INTO `follow_up_tasks`
    (`customer_id`, `assigned_user_id`, `created_by`, `title`, `description`, `due_at`, `status`, `priority`, `completed_at`)
VALUES
    (1, 2, 1, 'Chuẩn bị demo Top 3 khách hàng', 'Tập trung vào dashboard và lịch sử tương tác.', DATE_ADD(NOW(), INTERVAL 1 DAY), 'in_progress', 'high', NULL),
    (2, 3, 1, 'Gọi chốt phản hồi báo giá', 'Nhắc khách phản hồi sau khi đã nhận email báo giá.', DATE_SUB(NOW(), INTERVAL 1 DAY), 'pending', 'high', NULL),
    (3, 2, 2, 'Gửi tài liệu hỗ trợ', 'Gửi tài liệu mô tả quy trình chăm sóc khách hàng.', DATE_ADD(NOW(), INTERVAL 2 DAY), 'pending', 'medium', NULL),
    (4, 3, 3, 'Gọi lại sau giờ hành chính', 'Liên hệ sau 19h theo yêu cầu của khách.', DATE_ADD(NOW(), INTERVAL 6 HOUR), 'pending', 'medium', NULL),
    (5, 2, 2, 'Xác nhận nhu cầu quản lý khách lẻ', 'Gọi kiểm tra nhu cầu sau khi khách ghé quầy.', DATE_ADD(NOW(), INTERVAL 5 DAY), 'pending', 'low', NULL),
    (6, 3, 3, 'Theo dõi lại sau tạm ngưng', 'Kiểm tra xem khách đã có nhu cầu trở lại chưa.', DATE_ADD(NOW(), INTERVAL 14 DAY), 'pending', 'low', NULL),
    (7, 2, 1, 'Gửi đề xuất nâng cấp', 'Soạn đề xuất để chuyển khách sang nhóm VIP.', DATE_SUB(NOW(), INTERVAL 2 DAY), 'completed', 'medium', DATE_SUB(NOW(), INTERVAL 1 DAY));

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
