-- =============================================================================
-- JMS deploy schema updates (main → jms push)
-- Safe / idempotent for MariaDB 10.4+ / MySQL 8+ (phpMyAdmin OK)
-- Skip any statement that errors with "Duplicate column/table" — already applied.
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1) Slack toggle columns (fixes Slack settings 500)
-- -----------------------------------------------------------------------------
ALTER TABLE `slack_configs`
  ADD COLUMN IF NOT EXISTS `webhook_new_job_url` varchar(500) DEFAULT NULL AFTER `webhook_url`,
  ADD COLUMN IF NOT EXISTS `webhook_assignment_url` varchar(500) DEFAULT NULL AFTER `webhook_new_job_url`,
  ADD COLUMN IF NOT EXISTS `new_job_slack_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `is_active`,
  ADD COLUMN IF NOT EXISTS `assignment_slack_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `new_job_slack_active`;

UPDATE `slack_configs`
SET
  `webhook_new_job_url` = COALESCE(NULLIF(`webhook_new_job_url`, ''), `webhook_url`),
  `webhook_assignment_url` = COALESCE(NULLIF(`webhook_assignment_url`, ''), `webhook_url`),
  `new_job_slack_active` = `is_active`,
  `assignment_slack_active` = `is_active`
WHERE `webhook_url` IS NOT NULL AND `webhook_url` != '';

-- -----------------------------------------------------------------------------
-- 2) Task Management
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `assignee_user_id` bigint unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'not_started',
  `notes` text DEFAULT NULL,
  `visibility` varchar(20) NOT NULL DEFAULT 'public',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_assignee_user_id_index` (`assignee_user_id`),
  KEY `tasks_due_date_index` (`due_date`),
  KEY `tasks_status_index` (`status`),
  KEY `tasks_created_by_index` (`created_by`),
  KEY `tasks_visibility_index` (`visibility`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If `tasks` already existed without visibility:
ALTER TABLE `tasks`
  ADD COLUMN IF NOT EXISTS `visibility` varchar(20) NOT NULL DEFAULT 'public' AFTER `notes`;

-- -----------------------------------------------------------------------------
-- 3) Attendance / Timesheet
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `clocked_in_at` datetime NOT NULL,
  `clocked_out_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_user_id_attendance_date_unique` (`user_id`, `attendance_date`),
  KEY `attendances_user_id_index` (`user_id`),
  KEY `attendances_attendance_date_index` (`attendance_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `attendances`
  ADD COLUMN IF NOT EXISTS `clocked_out_at` datetime DEFAULT NULL AFTER `clocked_in_at`;

-- -----------------------------------------------------------------------------
-- 4) HR leave credits + leave days
-- -----------------------------------------------------------------------------
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `leave_credits` smallint unsigned NOT NULL DEFAULT 15 AFTER `status`,
  ADD COLUMN IF NOT EXISTS `is_employee` tinyint(1) NOT NULL DEFAULT 1 AFTER `leave_credits`;

CREATE TABLE IF NOT EXISTS `leave_days` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `leave_date` date NOT NULL,
  `leave_type` varchar(50) NOT NULL DEFAULT 'leave',
  `status` varchar(20) NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_days_user_id_leave_date_unique` (`user_id`, `leave_date`),
  KEY `leave_days_leave_date_index` (`leave_date`),
  KEY `leave_days_status_index` (`status`),
  CONSTRAINT `leave_days_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5) Forum / dashboard bulletin
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `body` text NOT NULL,
  `post_type` varchar(20) NOT NULL DEFAULT 'discussion',
  `pinned_at` timestamp NULL DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_posts_user_id_index` (`user_id`),
  KEY `forum_posts_post_type_index` (`post_type`),
  KEY `forum_posts_pinned_at_index` (`pinned_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `forum_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `forum_post_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_comments_forum_post_id_index` (`forum_post_id`),
  KEY `forum_comments_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `forum_posts`
  ADD COLUMN IF NOT EXISTS `title` varchar(200) DEFAULT NULL AFTER `user_id`,
  ADD COLUMN IF NOT EXISTS `post_type` varchar(20) NOT NULL DEFAULT 'discussion' AFTER `body`,
  ADD COLUMN IF NOT EXISTS `pinned_at` timestamp NULL DEFAULT NULL AFTER `post_type`,
  ADD COLUMN IF NOT EXISTS `image_path` varchar(500) DEFAULT NULL AFTER `body`;

-- -----------------------------------------------------------------------------
-- 6) Job status transitions + On Hold status
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `job_status_transitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source_table` varchar(64) NOT NULL,
  `job_id` bigint unsigned NOT NULL,
  `from_status` varchar(80) DEFAULT NULL,
  `to_status` varchar(80) NOT NULL,
  `changed_at` timestamp NOT NULL,
  `changed_by` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jst_source_job_changed_idx` (`source_table`, `job_id`, `changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `statuses` (`name`, `color`, `created_at`, `updated_at`)
SELECT 'On Hold', '#f59e0b', NOW(), NOW()
FROM DUAL
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'statuses')
  AND NOT EXISTS (
    SELECT 1 FROM `statuses` WHERE LOWER(TRIM(`name`)) = 'on hold'
  );

-- If statuses has font_color, set it (ignore error if column missing):
-- UPDATE `statuses` SET `font_color` = '#ffffff' WHERE LOWER(TRIM(`name`)) = 'on hold';

-- -----------------------------------------------------------------------------
-- 7) Permissions — Task Management
--    Copy from dashboard / task_management holders.
-- -----------------------------------------------------------------------------

-- role_permissions: task_management + CRUD from dashboard
INSERT INTO `role_permissions` (`role`, `branch`, `route_name`)
SELECT rp.`role`, rp.`branch`, t.`route_name`
FROM `role_permissions` rp
CROSS JOIN (
  SELECT 'task_management' AS route_name UNION ALL
  SELECT 'task_management.store' UNION ALL
  SELECT 'task_management.update' UNION ALL
  SELECT 'task_management.destroy'
) t
WHERE rp.`route_name` = 'dashboard'
  AND NOT EXISTS (
    SELECT 1 FROM `role_permissions` x
    WHERE x.`role` = rp.`role` AND x.`branch` = rp.`branch` AND x.`route_name` = t.`route_name`
  );

-- role_permissions: view_all from task_management or dashboard
INSERT INTO `role_permissions` (`role`, `branch`, `route_name`)
SELECT rp.`role`, rp.`branch`, 'task_management.view_all'
FROM `role_permissions` rp
WHERE rp.`route_name` IN ('task_management', 'dashboard')
  AND NOT EXISTS (
    SELECT 1 FROM `role_permissions` x
    WHERE x.`role` = rp.`role` AND x.`branch` = rp.`branch` AND x.`route_name` = 'task_management.view_all'
  );

-- role_permissions: HR / timesheet / reports.export from reports
INSERT INTO `role_permissions` (`role`, `branch`, `route_name`)
SELECT rp.`role`, rp.`branch`, t.`route_name`
FROM `role_permissions` rp
CROSS JOIN (
  SELECT 'reports.export' AS route_name UNION ALL
  SELECT 'hr' UNION ALL
  SELECT 'timesheet'
) t
WHERE rp.`route_name` = 'reports'
  AND NOT EXISTS (
    SELECT 1 FROM `role_permissions` x
    WHERE x.`role` = rp.`role` AND x.`branch` = rp.`branch` AND x.`route_name` = t.`route_name`
  );

-- role_permissions: forum from dashboard / forum_thread
INSERT INTO `role_permissions` (`role`, `branch`, `route_name`)
SELECT rp.`role`, rp.`branch`, t.`route_name`
FROM `role_permissions` rp
CROSS JOIN (
  SELECT 'forum_thread' AS route_name UNION ALL
  SELECT 'forum_thread.post' UNION ALL
  SELECT 'forum_thread.comment' UNION ALL
  SELECT 'forum_thread.destroy' UNION ALL
  SELECT 'forum_thread.comment.destroy'
) t
WHERE rp.`route_name` IN ('dashboard', 'forum_thread')
  AND NOT EXISTS (
    SELECT 1 FROM `role_permissions` x
    WHERE x.`role` = rp.`role` AND x.`branch` = rp.`branch` AND x.`route_name` = t.`route_name`
  );

-- user_permissions: task CRUD from dashboard
INSERT INTO `user_permissions` (`user_id`, `branch`, `route_name`)
SELECT up.`user_id`, up.`branch`, t.`route_name`
FROM `user_permissions` up
CROSS JOIN (
  SELECT 'task_management' AS route_name UNION ALL
  SELECT 'task_management.store' UNION ALL
  SELECT 'task_management.update' UNION ALL
  SELECT 'task_management.destroy'
) t
WHERE up.`route_name` = 'dashboard'
  AND NOT EXISTS (
    SELECT 1 FROM `user_permissions` x
    WHERE x.`user_id` = up.`user_id` AND x.`branch` = up.`branch` AND x.`route_name` = t.`route_name`
  );

-- user_permissions: view_all
INSERT INTO `user_permissions` (`user_id`, `branch`, `route_name`)
SELECT up.`user_id`, up.`branch`, 'task_management.view_all'
FROM `user_permissions` up
WHERE up.`route_name` IN ('task_management', 'dashboard')
  AND NOT EXISTS (
    SELECT 1 FROM `user_permissions` x
    WHERE x.`user_id` = up.`user_id` AND x.`branch` = up.`branch` AND x.`route_name` = 'task_management.view_all'
  );

-- user_permissions: HR / timesheet / reports.export
INSERT INTO `user_permissions` (`user_id`, `branch`, `route_name`)
SELECT up.`user_id`, up.`branch`, t.`route_name`
FROM `user_permissions` up
CROSS JOIN (
  SELECT 'reports.export' AS route_name UNION ALL
  SELECT 'hr' UNION ALL
  SELECT 'timesheet'
) t
WHERE up.`route_name` = 'reports'
  AND NOT EXISTS (
    SELECT 1 FROM `user_permissions` x
    WHERE x.`user_id` = up.`user_id` AND x.`branch` = up.`branch` AND x.`route_name` = t.`route_name`
  );

-- user_permissions: forum
INSERT INTO `user_permissions` (`user_id`, `branch`, `route_name`)
SELECT up.`user_id`, up.`branch`, t.`route_name`
FROM `user_permissions` up
CROSS JOIN (
  SELECT 'forum_thread' AS route_name UNION ALL
  SELECT 'forum_thread.post' UNION ALL
  SELECT 'forum_thread.comment' UNION ALL
  SELECT 'forum_thread.destroy' UNION ALL
  SELECT 'forum_thread.comment.destroy'
) t
WHERE up.`route_name` IN ('dashboard', 'forum_thread')
  AND NOT EXISTS (
    SELECT 1 FROM `user_permissions` x
    WHERE x.`user_id` = up.`user_id` AND x.`branch` = up.`branch` AND x.`route_name` = t.`route_name`
  );

-- -----------------------------------------------------------------------------
-- Optional check queries (run after):
-- SHOW COLUMNS FROM tasks LIKE 'visibility';
-- SHOW COLUMNS FROM slack_configs LIKE '%slack_active%';
-- SELECT route_name, COUNT(*) FROM role_permissions
--   WHERE route_name LIKE 'task_management%' GROUP BY route_name;
-- =============================================================================
