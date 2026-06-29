<?php

/**
 * Fix Hostinger/MariaDB dump for local import.
 * Usage: php scripts/fix-sql-local-import.php
 */

$src = __DIR__ . '/../database/u501101592_jms (1).sql';
$dst = __DIR__ . '/../database/legacy/u501101592_jms_local_import.sql';

if (! is_file($src)) {
    fwrite(STDERR, "Source not found: {$src}\n");
    exit(1);
}

$sql = file_get_contents($src);

$sql = str_replace('utf8mb4_uca1400_ai_ci', 'utf8mb4_unicode_ci', $sql);

$header = <<<'HDR'
-- Fixed for local import (MySQL/MariaDB/XAMPP/WAMP/phpMyAdmin)
-- Source: database/u501101592_jms (1).sql
-- Changes: compatible collation, CREATE/USE database, FK checks off, DROP TABLE IF EXISTS

CREATE DATABASE IF NOT EXISTS `u501101592_jms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `u501101592_jms`;

SET FOREIGN_KEY_CHECKS=0;
SET UNIQUE_CHECKS=0;

HDR;

$needle = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";';
$pos = strpos($sql, $needle);
if ($pos === false) {
    $sql = $header.$sql;
} else {
    $sql = $header.substr($sql, $pos);
}

$sql = preg_replace(
    '/\nCREATE TABLE `([^`]+)`/',
    "\nDROP TABLE IF EXISTS `$1`;\nCREATE TABLE `$1`",
    $sql
);

$sql = str_replace(
    "COMMIT;",
    "SET FOREIGN_KEY_CHECKS=1;\nSET UNIQUE_CHECKS=1;\nCOMMIT;",
    $sql
);

$dir = dirname($dst);
if (! is_dir($dir)) {
    mkdir($dir, 0755, true);
}

file_put_contents($dst, $sql);

echo "Written: {$dst}\n";
echo 'Size: '.number_format(strlen($sql))." bytes\n";
echo 'uca1400 remaining: '.substr_count($sql, 'uca1400')."\n";
echo 'DROP TABLE count: '.substr_count($sql, 'DROP TABLE IF EXISTS')."\n";
