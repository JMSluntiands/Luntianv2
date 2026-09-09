-- =============================================================================
-- Fix 500 on User Accounts (create / edit / save) — jms.luntian.com.au
-- Safe / idempotent for MariaDB 10.4+ (phpMyAdmin OK)
-- Skip any statement that errors with "Duplicate column/table" — already applied.
-- =============================================================================

-- 1) Branches dropdown source
CREATE TABLE IF NOT EXISTS `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `branches`
  ADD COLUMN IF NOT EXISTS `deleted_at` timestamp NULL DEFAULT NULL AFTER `updated_at`;

-- 2) Users columns required by current User Accounts form / model
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `branch` varchar(255) NOT NULL DEFAULT '' AFTER `role`,
  ADD COLUMN IF NOT EXISTS `add_job_staff_modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL AFTER `branch`,
  ADD COLUMN IF NOT EXISTS `add_job_checker_modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL AFTER `add_job_staff_modules`,
  ADD COLUMN IF NOT EXISTS `task` varchar(255) DEFAULT NULL AFTER `add_job_checker_modules`,
  ADD COLUMN IF NOT EXISTS `status` varchar(50) DEFAULT 'Active' AFTER `task`,
  ADD COLUMN IF NOT EXISTS `leave_credits` smallint unsigned NOT NULL DEFAULT 15 AFTER `status`,
  ADD COLUMN IF NOT EXISTS `is_employee` tinyint(1) NOT NULL DEFAULT 1 AFTER `leave_credits`;

-- Optional seed (uncomment if dropdown still empty and you want a starter row):
-- INSERT INTO `branches` (`branch_name`, `created_at`, `updated_at`)
-- SELECT 'Main', NOW(), NOW()
-- FROM DUAL
-- WHERE NOT EXISTS (SELECT 1 FROM `branches` WHERE `branch_name` = 'Main');
