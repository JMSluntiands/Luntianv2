-- Fix Slack toggle 500 on production / local.
-- Cause: slack_configs missing purpose webhook + active flag columns.
-- Run this in phpMyAdmin on the live DB (or local `luntian`).

ALTER TABLE `slack_configs`
  ADD COLUMN IF NOT EXISTS `webhook_new_job_url` varchar(500) DEFAULT NULL AFTER `webhook_url`,
  ADD COLUMN IF NOT EXISTS `webhook_assignment_url` varchar(500) DEFAULT NULL AFTER `webhook_new_job_url`,
  ADD COLUMN IF NOT EXISTS `new_job_slack_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `is_active`,
  ADD COLUMN IF NOT EXISTS `assignment_slack_active` tinyint(1) NOT NULL DEFAULT 1 AFTER `new_job_slack_active`;

-- Copy legacy single webhook into both purpose URLs when empty
UPDATE `slack_configs`
SET
  `webhook_new_job_url` = COALESCE(NULLIF(`webhook_new_job_url`, ''), `webhook_url`),
  `webhook_assignment_url` = COALESCE(NULLIF(`webhook_assignment_url`, ''), `webhook_url`),
  `new_job_slack_active` = `is_active`,
  `assignment_slack_active` = `is_active`
WHERE `webhook_url` IS NOT NULL AND `webhook_url` != '';
