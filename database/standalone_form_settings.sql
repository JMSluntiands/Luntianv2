-- Standalone Form settings. Run once on the server.
-- Creates the final table and records the three migrations so artisan will not run them again.

CREATE TABLE IF NOT EXISTS `standalone_form_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_key` varchar(50) NOT NULL,
  `field_key` varchar(80) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `standalone_form_settings_form_key_field_key_unique` (`form_key`, `field_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT `next`.`migration`, `next`.`batch`
FROM (
  SELECT '2026_09_28_203200_create_standalone_form_settings_table' AS `migration`,
         COALESCE(MAX(`batch`), 0) + 1 AS `batch`
  FROM `migrations`
) AS `next`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` AS `m`
  WHERE `m`.`migration` = '2026_09_28_203200_create_standalone_form_settings_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT `next`.`migration`, `next`.`batch`
FROM (
  SELECT '2026_09_28_203500_add_field_key_to_standalone_form_settings' AS `migration`,
         COALESCE(MAX(`batch`), 0) AS `batch`
  FROM `migrations`
) AS `next`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` AS `m`
  WHERE `m`.`migration` = '2026_09_28_203500_add_field_key_to_standalone_form_settings'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT `next`.`migration`, `next`.`batch`
FROM (
  SELECT '2026_09_28_210000_add_is_visible_to_standalone_form_settings' AS `migration`,
         COALESCE(MAX(`batch`), 0) AS `batch`
  FROM `migrations`
) AS `next`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` AS `m`
  WHERE `m`.`migration` = '2026_09_28_210000_add_is_visible_to_standalone_form_settings'
);
