-- Compatibility patch for Laravel auth users table.
-- Run this after importing `u501101592_jms.sql`.

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `password` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `username` varchar(255) DEFAULT NULL AFTER `id`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `email` varchar(255) DEFAULT NULL AFTER `username`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `fullname` varchar(255) DEFAULT NULL AFTER `email`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `role` varchar(255) NOT NULL DEFAULT 'user' AFTER `fullname`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `password` varchar(255) DEFAULT NULL AFTER `role`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `profile_image` varchar(255) DEFAULT NULL AFTER `password`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `email_verified_at` timestamp NULL DEFAULT NULL AFTER `profile_image`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `remember_token` varchar(100) DEFAULT NULL AFTER `email_verified_at`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `created_at` timestamp NULL DEFAULT NULL AFTER `remember_token`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `updated_at` timestamp NULL DEFAULT NULL AFTER `created_at`;
