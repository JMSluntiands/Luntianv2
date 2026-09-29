-- Lets the standalone Job Status dropdown add, edit, and delete its own choices.
-- Safe to run once. Skip the ALTER if show_on_form already exists.

ALTER TABLE statuses
    ADD COLUMN show_on_form TINYINT(1) NOT NULL DEFAULT 0 AFTER font_color;

UPDATE statuses
SET show_on_form = 1
WHERE name IN ('For Inquiries', 'For Quotation');
