-- Database Schema for Anna Pakseleva Design Studio
-- Run this SQL to create all tables

-- Заявки с сайта
CREATE TABLE IF NOT EXISTS `leads` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `created_at` DATETIME NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `messenger` VARCHAR(50) DEFAULT NULL,
    `type` VARCHAR(50) NOT NULL COMMENT 'consultation, tariff, feedback',
    `tariff` VARCHAR(100) DEFAULT NULL,
    `object_type` VARCHAR(50) DEFAULT NULL,
    `area` VARCHAR(20) DEFAULT NULL,
    `stage` VARCHAR(100) DEFAULT NULL,
    `comment` TEXT DEFAULT NULL,
    `utm_source` VARCHAR(255) DEFAULT NULL,
    `utm_medium` VARCHAR(255) DEFAULT NULL,
    `utm_campaign` VARCHAR(255) DEFAULT NULL,
    `ip` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `status` VARCHAR(50) DEFAULT 'new' COMMENT 'new, contacted, in_progress, completed, rejected',
    `notes` TEXT DEFAULT NULL,
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_type` (`type`),
    INDEX `idx_phone` (`phone`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Отзывы
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `project` VARCHAR(255) NOT NULL COMMENT 'Тип и площадь проекта',
    `text` TEXT NOT NULL,
    `rating` TINYINT NOT NULL DEFAULT 5 COMMENT '1-5',
    `avatar_initials` VARCHAR(10) DEFAULT NULL COMMENT 'Инициалы для аватара',
    `is_published` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    INDEX `idx_is_published` (`is_published`),
    INDEX `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Портфолио проекты
CREATE TABLE IF NOT EXISTS `portfolio_projects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'Название проекта',
    `folder` VARCHAR(50) NOT NULL COMMENT 'Номер папки (1, 2, 3...)',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    UNIQUE KEY `unique_folder` (`folder`),
    INDEX `idx_sort_order` (`sort_order`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Администраторы
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `last_login` DATETIME DEFAULT NULL,
    UNIQUE KEY `unique_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставить дефолтного админа (логин: admin, пароль: admin123)
-- Пароль нужно будет изменить после первого входа!
-- Если пароль не работает, используйте скрипт admin/reset-password.php
INSERT INTO `admins` (`username`, `password_hash`, `created_at`) 
VALUES ('admin', '$2y$12$Dw/IfnslA3gtsw1KV6mfF..45C.as7Msj6keZ/4.D6K1txIBq1fGe', NOW())
ON DUPLICATE KEY UPDATE `username`=`username`;

