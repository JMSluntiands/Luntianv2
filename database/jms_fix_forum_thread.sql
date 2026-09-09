-- Fix online 500 on /dashboard/forum-thread
-- Cause: forum_posts / forum_comments tables (and columns) missing on JMS DB.
-- Run in phpMyAdmin on the live database.

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

-- If table already existed with only user_id + body:
ALTER TABLE `forum_posts`
  ADD COLUMN IF NOT EXISTS `title` varchar(200) DEFAULT NULL AFTER `user_id`,
  ADD COLUMN IF NOT EXISTS `post_type` varchar(20) NOT NULL DEFAULT 'discussion' AFTER `body`,
  ADD COLUMN IF NOT EXISTS `pinned_at` timestamp NULL DEFAULT NULL AFTER `post_type`,
  ADD COLUMN IF NOT EXISTS `image_path` varchar(500) DEFAULT NULL AFTER `body`;

-- Permissions (roles that already have dashboard)
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
