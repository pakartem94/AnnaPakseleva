-- Database Schema for Anna Pakseleva Design Studio
-- Run this SQL to create the leads table

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
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_type` (`type`),
    INDEX `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

